/**
 * Created by weizs on 2015/5/14.
 */
'use strict';
/*global O,$,define,plupload*/
define(function (require, exports, module) {
    var utils = require('../../../../frontend/js/lib/utils');
    var template = require('../../../../frontend/js/lib/template.js');
    require('../../../../frontend/js/lib/tooltips/tooltips');
    require('../../../../frontend/js/lib/dialog.js');
    require('../../../../frontend/js/plugin/form.js');
    require('../../../../frontend/js/widgets/weixinEdition/weixinEdition');
    require('../../../../frontend/js/plugin/select-box');
    require('../../../../frontend/js/lib/copy');


    var viewTable = $('#view-table');
    //初始化上传
    var initImageUpload = function (wrap, id, fn) {
        var upload_btn = wrap.find('#upload_btn'),
            imgUploader = utils.upload({
                browse_button: 'upload_btn',
                container: wrap.find('.button-area').get(0),
                url: O.path('/basic/channel/upload-logo-qrcode', {id: id}),
                filters: [{title: '图片文件', extensions: 'jpg,png,gif'}],
                init: {
                    FilesAdded: function (up, files) {
                        var flag = false;
                        plupload.each(files, function (file) {
                            if (file.size / 1024 > 2048) {
                                flag = 'size';
                                return false;
                            }
                            if (['image/jpeg', 'image/png', 'image/gif'].indexOf(file.type) < 0) {
                                flag = 'type';
                                return false;
                            }
                        });
                        if (flag) {
                            plupload.each(files, function (file) {
                                up.removeFile(file);
                            });
                        }
                        if (flag === 'size') {
                            tips('每张最大支持2M', 'tips');
                            return;
                        }
                        if (flag === 'type') {
                            tips('只支持jpg/gif/png格式的图片', 'tips');
                            return;
                        }
                        upload_btn.addClass('waiting');
                        imgUploader.start();
                    },
                    FileUploaded: function (up, file, info) {
                        var data = JSON.parse(info.response) || {};
                        if (data.result) {
                            fn && fn(data.url);
                        } else {
                            tips(data.errmsg, 'tips');
                        }
                        upload_btn.removeClass('waiting');
                    },
                    Error: function (up, err) {
                        if (err.code === -601) {
                            tips('只支持jpg/gif/png格式的图片', 'tips');
                        } else {
                            tips(err.message, 'tips');
                        }
                    }
                }
            });
        imgUploader.init();
    };
    //tpl
    var addTpl = $('#add_tpl').html();

    var tips = function (msg, mode) {
        $.topTips({mode: mode || 'normal', tip_text: msg || '操作成功'});
    };

    var add = function (data) {

        var box = $.box({
            content: template(addTpl, data || {
                    id: '',
                    name: '',
                    jump_type: '',
                    activity_id: '',
                    activity_name: '',
                    redirect_url: '',
                    follow_msg: ''
                }),
            title: '添加拓客渠道',
            height: 'auto',
            width: 800
        });

        var addForm = $('#add-form'),
            followMsg = addForm.find('#follow_msg'),
            activity_id = addForm.find('#activity_id'),
            redirect_url = addForm.find('#redirect_url');

        activity_id.selectBox();

        var bodyEditor = $('#body_editor').weixinEdition({
            maxTextLen: 600,
            onlyAllowText: true,
            onEditorAreaChange: function () {
                var content = bodyEditor.getContent() || '';
                if (content.replace(/<br>/g, '') === '') {
                    content = '';
                }
                followMsg.val(content).keyup();
            }
        });

        if (data && data.follow_msg) {
            bodyEditor.setContent('文字', data.follow_msg + '');
        }

        addForm.form({
            submitbtn: 'submit_btn',
            formName: 'add-form',
            rules: [{
                id: 'name',
                required: true,
                msg: {required: '请填写拓客渠道名称'}
            }, {
                id: 'jump_type_hidden',
                required: true,
                msg: {required: '请选择扫描后跳转类型'}
            }, {
                id: 'activity_name',
                required: false,
                notAutoCheck: true,
                msg: {not_selected: '请选择活动'},
                fun: function (field) {
                    var jump_type = $('.radio-btn .selected input').val();
                    if (jump_type === '活动') {
                        if (activity_id.val() === '') {
                            return 'not_selected';
                        }
                    }
                    return '';
                }
            }, {
                id: 'redirect_url',
                required: false,
                notAutoCheck: true,
                msg: {not_url: '请填写跳转路径'},
                fun: function (field) {
                    var jump_type = $('.radio-btn .selected input').val();
                    if (jump_type === '注册页') {
                        if (redirect_url.val() === '') {
                            return 'not_url';
                        }
                    }
                    return '';
                }
            }, {
                id: 'follow_msg',
                required: false,
                msg: {required: '请填写关注消息内容'},
                fun: function (field) {
                    var jump_type = $('.radio-btn .selected input').val();
                    if (jump_type === '注册页') {
                        if (followMsg.val() === '') {
                            return 'required';
                        }
                    }
                }
            }
            ],
            validate: function () {
                return true;
            },
            submit: function (param, _data) {
                _data.redirect_url = _data.redirect_url.indexOf('http://') !== 0 && _data.redirect_url.indexOf('https://') !== 0 && _data.redirect_url !== '' ? 'http://' + _data.redirect_url : _data.redirect_url;
                _data.follow_msg = bodyEditor.getContent();
                O.ajaxEx({
                    url: O.path('basic/channel/add-channel'),
                    type: 'post',
                    data: _data
                }).then(function (res) {
                    box.close();
                    box.remove();
                    if (res.result) {
                        tips('保存成功！');
                    } else {
                        tips(res.msg, 'tips');
                    }
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                });
            }
        });

        var jump_type_hidden = $('#jump_type_hidden');
        $('.add-dialog').off('click').on('click', '.js-cancel-btn,.radio-btn .form-radio', function () {
            var $dom = $(this);
            if ($dom.hasClass('js-cancel-btn')) {
                box.close();
                box.remove();
            } else if ($dom.hasClass('form-radio')) {
                var val = $dom.find('span').text();
                if (val === '活动') {
                    jump_type_hidden.attr('value', '1');
                    jump_type_hidden.val('活动');
                    $('#div_activity_id').show();
                    $('#div_redirect_url').hide();
                    $('#div_follow_msg').hide();

                } else {
                    jump_type_hidden.attr('value', '0');
                    jump_type_hidden.val(val);
                    $('#div_activity_id').hide();
                    $('#div_redirect_url').show();
                    $('#div_follow_msg').show();
                }
                jump_type_hidden.keyup();

            }
        });

        $('#activity_id').change(function () {
            var checkText = $(this).children('option:selected').text();
            var activity_name = $('#activity_name');
            activity_name.attr('value', checkText);
            activity_name.val(checkText);
            if (checkText !== '') {
                activity_name.next('.form-error').remove();
            }
        });

        $('#redirect_url').keypress(function () {
            $(this).next('.form-error').remove();
        });

    };

    $('.member-form').off('click').on('click', '.opt-btn,.dl_barcode', function () {
        var $this = $(this);
        if ($this.hasClass('opt-del')) {
            $this.tipsLayer('确认删除？', function () {
                O.ajaxEx({
                    url: O.path('basic/channel/delete'),
                    type: 'post',
                    data: {
                        id: $this.data('id')
                    }
                }).then(function (res) {
                    if (res.result) {
                        tips('删除成功');
                        setTimeout(function () {
                            window.location.reload();
                        }, 1000);
                    } else {
                        tips(res.msg, 'tips');
                    }
                });
            });
        } else if ($this.hasClass('add-channel')) {
            add();
        } else if ($this.hasClass('dl_barcode')) {
            var url = $this.closest('.qr-code').find('img').attr('src');
            var text = $this.closest('tr').find('td:eq(1)').text();
            if (url) {
                utils.download(O.path('/basic/channel/down-code', {url: url, name: text}));
            } else {
                tips('二维码不存在', 'tips');
            }
        } else {
            add($this.data());
        }
    });


    //复制链接
    $('.copy-button').copy({
        beforeCopy: function (dom) {
            return $(dom).data('link');
        },
        onCopy: function () {
            $.topTips({tip_text: '复制成功'});
        }
    });

    //二维码点击放大
    viewTable.on('click', '.qr-code img', function () {
        $.box({
            content: $(this.outerHTML).css({height: '100%', width: '100%'}).get(0).outerHTML,
            title: '二维码',
            height: 'auto',
            width: 330
        });
    });

    var set_logo_tpl = $('#set_logo_tpl').html();

    //嵌入LOGO
    viewTable.on('click', '.qr-code .set-logo', function () {
        var $this = $(this),
            id = $this.data('id'),
            img = $this.closest('.qr-code').find('img'),
            img_url = img.attr('src');
        if (img_url) {
            var box = $.box({
                content: template(set_logo_tpl, {img_url: img_url}),
                title: '二维码',
                height: 'auto',
                width: 330
            });

            var wrap = $(box.node),
                bigImg = wrap.find('img');
            initImageUpload(wrap, id, function (url) {
                bigImg.attr('src', url);
                img.attr('src', url);
            });

            wrap.on('click', '.del', function () {
                O.ajaxEx({
                    url: O.path('/basic/channel/remove-logo', {id: id}),
                    type: 'get'
                }).then(function (res) {
                    if (res.result) {
                        bigImg.attr('src', res.url);
                        img.attr('src', res.url);
                    } else {
                        res.msg && tips(res.msg, 'tips');
                    }
                });
            });

        } else {
            tips('二维码不存在', 'tips');
        }
    });
});

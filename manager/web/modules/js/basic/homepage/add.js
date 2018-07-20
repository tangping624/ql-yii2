/**
 * Created by weizs on 2015/5/13.
 */
'use strict';
/*global O,$,define,plupload*/
define(function (require, exports, module) {
    var utils = require('../../../../frontend/js/lib/utils');
    var template = require('../../../../frontend/js/lib/template');
    require('../../../../frontend/js/lib/dialog');
    require('../../../../frontend/js/plugin/form.js');

    var type = $('#type').val();

    var opt_btn = '<div class="opeate"><span class="opt-btn f-btn" data-title="设为默认图标"><span class="icon-merge icon"></span></span><span class="opt-btn d-btn" data-title="删除"><span class="icon-merge icon"></span></span></div>';
    var _imgTempl = '<div class="img-wrap" id="<%-id%>"><div class="img-box"><div class="box-inner"><p class="wait">等待中...</p><div class="per"><span class="pct"></span></div></div></div>' + (type === 'banner' ? '' : opt_btn) + '</div>';

    var add = {
        init: function () {
            var $this = this,
                viewName = $('#view-name'),
                linkView = $('#link-view'),
                funcChoose = $('#function_choose'),
                app_list_tpl = $('#app_list_tpl').html(),
                link = $('#href'),
                name = $('#name'),
                uploadimg = $('.uploadimg-wrap'),
                img_count = $('#img_count'),
                img_cover = $('.img-cover'),
                highlight_img = $('#highlight_img');

            $('#user_form').form({
                submitbtn: 'submit_btn',
                formName: 'user_form',
                rules: [
                    {
                        id: 'name',
                        required: true,
                        msg: {required: $('#name').data('msg')},
                        fun: function (data) {
                            viewName.text(data.value);
                        }
                    },
                    {
                        id: 'href',
                        msg: {required: link.data('msg'), not_url: '请输入有效的URL'},
                        fun: function (data) {
                            linkView.text(data.value);
                            if (this.value === '') {
                                return type === 'menu' ? 'required' : '';
                            } else {
                                if (!utils.isURL(this.value.indexOf('http://') !== 0 && this.value !== '' ? 'http://' + this.value : this.value)) {
                                    return 'not_url';
                                }
                            }
                        }
                    },
                    {
                        id: 'img_url',
                        required: true,
                        msg: {required: $('#img_url').data('msg')}
                    }
                ],
                submit: function (data, _data, node) {
                    _data.href = _data.href.indexOf('http://') === 0 || _data.href.indexOf('https://') === 0 ? _data.href : 'http://' + _data.href;
                    if (!utils.isURL(_data.href)) {
                        _data.href = '';
                        link.val('');
                        linkView.text('');
                    }
                    O.ajaxEx({
                        node: node,
                        url: O.path('/basic/homepage/add-banner'),
                        data: _data,
                        type: 'post'
                    }).then(function (res) {
                        if (res.result) {
                            $.topTips({tip_text: '发布成功'});
                            setTimeout(function () {
                                window.location.href = O.path('/basic/homepage/index#home_' + type);
                            }, 1000);
                        } else {
                            $.topTips({tip_text: res.msg, mode: 'tips'});
                        }
                    });
                }
            });

            var _change = function (wrap, percent) {
                var wait = wrap.find('.wait'),
                    pct = wrap.find('.pct');
                wait.text(percent + '%');
                pct.css('width', percent + '%');
            };

            var updateCount = function () {
                img_count.text(uploadimg.find('img').length);
            };

            var updateCover = function (url, remove) {
                if (remove || !url) {
                    if (img_cover.find('img').attr('src') !== url) {
                        return;
                    }
                    img_cover.html('<span>暂无封面图片</span>');
                } else if (url) {
                    img_cover.html('<img src="' + url + '">');
                }
            };

            var _uploader = null;

            var init_uploader = function ($wrap, max) {
                var _imgWraps = $wrap.find('.img-wraps');
                _uploader = utils.upload({
                    browse_button: 'upload_btn',
                    container: $wrap.get(0),
                    multi_selection: max > 1,
                    url: O.path('/basic/upload/upload-image'),
                    filters: [{title: '图片文件', extensions: 'jpg,jpeg,png,gif'}],
                    init: {
                        FilesAdded: function (up, files) {
                            var flag = false;
                            plupload.each(files, function (file) {
                                if (file.size / 1024 > 10240) {
                                    flag = 'size';
                                    return false;
                                }
                                if (['image/jpeg', 'image/png', 'image/gif'].indexOf(file.type) < 0) {
                                    flag = 'type';
                                    return false;
                                }
                            });

                            if ($wrap.find('img').length >= max) {
                                flag = max > 1 ? 'max' : '';
                            }

                            if (flag) {
                                plupload.each(files, function (file) {
                                    up.removeFile(file);
                                });
                            }
                            if (flag === 'max') {
                                $.topTips({mode: 'tips', tip_text: '最多上传' + max + '张图片'});
                                return;
                            }
                            if (flag === 'size') {
                                $.topTips({mode: 'tips', tip_text: '每张最大支持10MB'});
                                return;
                            }
                            if (flag === 'type') {
                                $.topTips({mode: 'tips', tip_text: '只支持jpg/gif/png格式的图片'});
                                return;
                            }
                            plupload.each(files, function (file, i) {
                                if (max > 1 && i < max) {
                                    _imgWraps.append(template(_imgTempl, {id: file.id, type: type}));
                                } else if (!i) {
                                    _imgWraps.html(template(_imgTempl, {id: file.id, type: type}));
                                } else {
                                    up.removeFile(file);
                                }
                            });
                            _uploader.start();
                        },
                        FileUploaded: function (up, file, info) {
                            var data = JSON.parse(info.response) || {};
                            if (data.status - 0 === 0) {
                                $('#' + file.id).html('<span>上传失败</span>');
                            } else {
                                $('#' + file.id).find('.img-box').html('<img src="' + data.original + '">');

                                if ($wrap.find('img').length === 1) {
                                    $('#img_url').attr('value', data.original).keyup();
                                    _uploader.refresh();
                                    updateCover(data.original);
                                } else if (type !== 'banner') {
                                    $('#highlight_img_url').attr('value', data.original);
                                }
                            }
                            updateCount();
                        },
                        UploadProgress: function (up, file) {
                            _change($wrap, file.percent);
                        },
                        Error: function (up, err) {
                            if (err.code === -601) {
                                $.topTips({mode: 'tips', tip_text: '只支持jpg/gif/png格式的图片'});
                            } else {
                                $.topTips({tip_text: err.message, mode: 'tips'});
                            }
                        }
                    }
                });
                _uploader.init();
            };

            init_uploader(uploadimg, type === 'banner' ? 1 : 2);

            uploadimg.on('click', '.opt-btn', function () {
                var $btn = $(this),
                    wrap = $btn.closest('.img-wrap'),
                    src = wrap.find('img').attr('src');
                if ($btn.hasClass('d-btn')) {
                    $btn.tipsLayer('确定删除？', function () {
                        $btn.closest('.img-wrap').remove();
                        if (img_cover.find('img').attr('src') === src) {
                            $('#img_url').val('').keyup();
                        } else {
                            $('#highlight_img_url').val('');
                        }
                        _uploader.refresh();
                        updateCover(src, true);
                        updateCount();
                    });
                } else {
                    $('#img_url').val(src).keyup();
                    $('#highlight_img_url').val(wrap.siblings().eq(0).find('img').attr('src'));
                    updateCover(src);
                }
            });

            if (type === 'menu') {
                funcChoose.off('click').on('click', function () {
                    var box = $.box({
                        content: app_list_tpl,
                        title: '功能选择',
                        width: 660,
                        height: 'auto'
                    });

                    var node = $(box.node);
                    node.off('click');
                    node.on('click', '.app-item', function () {
                        $(this).addClass('selected').siblings().removeClass('selected');
                    });

                    node.on('click', 'button', function () {
                        if ($(this).hasClass('js-ok-btn')) {
                            var selected = node.find('.selected'),
                                nameVal = selected.find('.app-name').text(),
                                linkVal = selected.data('link');
                            if (!linkVal || linkVal === '') {
                                $.topTips({mode: 'tips', tip_text: '请选择功能'});
                            } else {
                                name.val(nameVal);
                                link.val(linkVal).focus();
                            }
                        }
                        box.close();
                        box.remove();
                    });

                });
            }
        }
    };
    add.init();
});
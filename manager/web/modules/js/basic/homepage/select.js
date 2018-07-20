/**
 * Created by weizs on 2015/6/30.
 */
'use strict';
/*global O,$,define*/
define(function (require, exports, module) {
    var utils = require('../../../../frontend/js/lib/utils');
    var template = require('../../../../frontend/js/lib/template.js');
    require('../../../../frontend/js/lib/dialog.js');
    require('../../../../frontend/js/plugin/form.js');


    var add_app_tpl = $('#add_app_tpl').html(),
        app_content = $('#app_content'),
        submit_btn = $('#submit_btn'),
        app_tpl = $('#app_tpl').html(),
        imgTpl = '<div class="img-wrap"><div class="img-box"><div class="box-inner"><p class="wait">等待中...</p><div class="per"><span class="pct"></span></div></div></div></div>';

    var imageUpload = function (container, uploadBtn, callback) {
        if (container.length) {
            var imgUploader = utils.upload({
                browse_button: uploadBtn,
                container: container.get(0),
                url: O.path('/basic/upload/upload-image'),
                filters: [{title: '图片文件', extensions: 'jpg,jpeg,png,gif'}],
                init: {
                    FilesAdded: function (up, files) {
                        var flag = false;
                        if (files[0].size / 1024 > 2048) {
                            flag = 'size';
                        }
                        if (flag) {
                            up.removeFile(files[0]);
                        }
                        if (flag === 'size') {
                            return $.topTips({tip_text: '每张最大支持2M', mode: 'tips'});
                        }
                        imgUploader.start();
                        container.find('.upload-area').html(imgTpl);
                    },
                    FileUploaded: function (up, file, info) {
                        var data = JSON.parse(info.response) || {};
                        if (data.status - 0 === 0) {
                            $.topTips({tip_text: '上传失败', mode: 'tips'});
                        } else {
                            container.find('.upload-area').html('<img src="' + data.original + '@80w.png" />');
                            callback && callback.call(imgUploader, data);
                        }
                    },
                    UploadProgress: function (up, file) {
                        container.find('.wait').text(file.percent + '%');
                        container.find('.pct').css('width', file.percent + '%');
                    },
                    Error: function (up, err) {
                        if (err.code === -601) {
                            $.topTips({tip_text: '只支持jpg/gif/png/bmp格式的图片', mode: 'tips'});
                        } else {
                            $.topTips({tip_text: err.message, mode: 'tips'});
                        }
                    }
                }
            });
            imgUploader.init();
        }
    };

    //组装数据
    var getData = function () {
        var data = [], newData = [], appItem = app_content.find('.app-item.selected'), sortArray = [];
        appItem.each(function (idx) {
            var app = appItem.eq(idx), item = app.data();
            item.name = app.find('.custom-app-name').val();
            if (item.sort !== '') {
                item.sort = item.sort - 0;
                sortArray.push(item.sort);
                data.push(item);
            } else {
                newData.push(item);
            }
        });
        var maxSort = Math.max.apply(this, [1].concat(sortArray));
        $.each(newData, function (idx, item) {
            item.sort = maxSort++;
        });
        return JSON.stringify(data.concat(newData));
    };

    var addApp = function (callback, data) {
        data = data || {img_url: '', href: '', name: '', id: '', function_id: '', sort: ''};
        var box = $.box({
            content: template(add_app_tpl, data),
            title: '自定义链接',
            height: 'auto',
            width: 675
        });

        $('#edit_form').form({
            submitbtn: 'dialog_submit_btn',
            formName: 'edit_form',
            rules: [{
                id: 'img_url',
                required: true,
                msg: {required: '请上传图标'}
            }, {
                id: 'name',
                required: true,
                msg: {required: '请填写名称'}
            }, {
                id: 'href',
                required: true,
                notAutoCheck: true,
                msg: {required: '请填写链接地址', url_error: '链接地址有误'},
                fun: function () {
                    var url = this.value || '';
                    if (url === '') {
                        return 'required';
                    }
                    if (url.indexOf('http://') !== 0 && url.indexOf('https://') !== 0) {
                        url = 'http://' + url;
                        this.value = url;
                    }
                    return utils.isURL(url) ? '' : 'url_error';
                }
            }],
            submit: function (paramStr, param) {
                param.href = param.href.indexOf('http://') === 0 || param.href.indexOf('https://') === 0 ? param.href : 'http://' + param.href;
                callback && callback(param);
                box.close();
                box.remove();
            }
        });

        var node = $(box.node);

        imageUpload(node.find('.upload-wrap'), 'upload_btn', function (data) {
            node.find('#img_url').val(data.original).keyup();
        });

        node.find('#href').off('keypress').on('keypress', function () {
            $(this).parent().find('.form-error').remove();
        });

        node.off('click').on('click', '.js-cancel-btn', function () {
            box.close();
            box.remove();
        });

    };

    app_content.on('click', '.app-item .icon-wrap', function () {
        var app_name = $(this).parent().addClass('selected').find('.custom-app-name').focus();
        app_name.val(app_name.val());
    });

    app_content.on('click', '.app-item .selected_mask', function () {
        $(this).parent().removeClass('selected');
    });

    app_content.on('click', '.add-item', function () {
        var $btn = $(this);
        addApp(function (param) {
            $btn.before(template(app_tpl, param));
        });
    });

    app_content.on('focus', '.custom-app-name', function () {
        var app_name = $(this);
        app_name.data('app-name', app_name.val());
    });

    app_content.on('blur', '.custom-app-name', function () {
        var app_name = $(this);
        if (app_name.val() === '') {
            app_name.val(app_name.data('app-name'));
        }
    });

    app_content.on('click', '.app-item .opt-btn', function () {
        var $btn = $(this), item = $btn.closest('.app-item'), data = item.data();
        data.name = item.find('.custom-app-name').val();
        addApp(function (param) {
            item.replaceWith(template(app_tpl, param));
        }, data);
    });

    submit_btn.on('click', function () {
        O.ajaxEx({
            node: submit_btn,
            url: O.path('/basic/homepage/add-entry'),
            type: 'post',
            data: {id_list: getData()}
        }).then(function (res) {
            if (res.result) {
                $.topTips({tip_text: '发布成功'});
                setTimeout(function () {
                    location.href = O.path('/basic/homepage/index');
                }, 1000);
            } else {
                $.topTips({tip_text: res.msg, mode: 'tips'});
            }
        });
    });
});
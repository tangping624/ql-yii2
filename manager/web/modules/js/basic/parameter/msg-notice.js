/**
 * Created by weizs on 2015/7/29.
 */
'use strict';
/*global O,$,define*/
define(function (require, exports, module) {
    var utils = require('../../../../frontend/js/lib/utils');
    var template = require('../../../../frontend/js/lib/template.js');
    require('../../../../frontend/js/plugin/grid.js');
    require('../../../../frontend/js/lib/dialog.js');
    require('../../../../frontend/js/plugin/form.js');


    var tips = function (msg, mode) {
        $.topTips({mode: mode || 'normal', tip_text: msg || '操作成功'});
    };

    var tpl = $('#edit_tpl').html(),
        addBtn = $('#add_btn'),
        viewTable = $('#view_table');

    var view = viewTable.grid({
        url: O.path('/basic/msg-notice/list'),
        delurl: O.path('/basic/msg-notice/del'),
        templateid: 'row_tpl',
        pagesize: 20,
        searchText: true,
        emptyText: '暂无数据',
        queryParams: function () {
            return '';
        }
    });

    var showDialog = function (data) {
        var isAdd = !data;
        data = isAdd ? {id: '', name: '', url: '', token: ''} : data;

        var dialog = $.box({
            title: isAdd ? '添加消息通知' : '编辑消息通知',
            width: 750,
            height: 'auto',
            content: template(tpl, data)
        });

        var wrap = $(dialog.node), editForm = $('#edit-form');

        editForm.form({
            submitbtns: [$('#submit_btn'), $('#check_btn')],
            formName: 'edit-form',
            rules: [{
                id: 'name',
                required: true,
                msg: {required: '请填写名称'}
            }, {
                id: 'url',
                required: true,
                msg: {required: '请填写URL', url_error: 'URL不合法'},
                fun: function () {
                    return this.value !== '' && !utils.isURL(this.value) ? 'url_error' : '';
                }
            }, {
                id: 'token',
                required: true,
                msg: {required: '请填写Token'}
            }],
            submit: function (param, data, node) {
                if (node.attr('id') === 'submit_btn') {
                    O.ajaxEx({
                        node: node,
                        url: data.id === '' ? O.path('/basic/msg-notice/save') : O.path('/basic/msg-notice/save?id=' + data.id),
                        type: 'post',
                        data: data
                    }).then(function (res) {
                        if (res.result) {
                            tips(data.id === '' ? '添加成功' : '修改成功');
                            view.search();
                        } else {
                            tips(res.msg, 'tips');
                        }
                        dialog.close();
                        dialog.remove();
                    });
                } else {
                    O.ajaxEx({
                        node: node,
                        url: O.path('/basic/msg-notice/validate-url'),
                        type: 'get',
                        data: data
                    }).then(function (res) {
                        if (res.result) {
                            tips('检测通过');
                        } else {
                            tips('检测不通过', 'tips');
                        }
                    });
                }
            }
        });

        wrap.off('click').on('click', 'button.js-cancel-btn', function () {
            dialog.close();
            dialog.remove();
        });

    };

    viewTable.on('click', '.edit-btn', function () {
        var $this = $(this),
            id = $this.data('id'),
            model = view ? view.grid.get(id) : null;
        if (model) {
            showDialog(model.toJSON());
        }
    });

    addBtn.on('click', function () {
        showDialog();
    });

});
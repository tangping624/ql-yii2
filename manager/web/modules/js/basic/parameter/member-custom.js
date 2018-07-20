/**
 * Created by weizs on 2015/12/07.
 */
'use strict';
/*global O,$,define*/
define(function (require, exports, module) {
    var template = require('../../../../frontend/js/lib/template');
    require('../../../../frontend/js/lib/dialog');
    require('../../../../frontend/js/plugin/form.js');
    require('../../../../frontend/js/lib/jquery.ui/jquery.sortable');

    var table = $('#table'),
        add = $('#add'),
        rowTpl = $('#row_tpl').html(),
        addTpl = '<form id="add_form"><label>自定义信息</label><input class="form-control" type="text" id="title" name="title" /></form>';

    var tips = function (msg, mode) {
        $.topTips({mode: mode || 'normal', tip_text: msg || '操作成功'});
    };

    var toggleRowEdit = function (row, open) {
        if (open) {
            row.addClass('edit');
            var input = row.find('input');
            input.focus();
            input.val(input.val());
        } else {
            row.removeClass('edit');
        }
    };

    var appendRow = function (data) {
        data.sort = table.find('tr:last>td:first').text() - 0 + 1;
        table.find('tbody').append(template(rowTpl, data));
    };

    table.on('click', '.opt-btn', function () {
        var $this = $(this),
            id = $this.data('id'),
            row = $this.closest('tr'),
            input = row.find('.editable input'),
            text = row.find('.editable span'),
            isModify = $this.hasClass('opt-modify');
        if ($this.hasClass('opt-cancel') || isModify) {

            toggleRowEdit($this.closest('tr'), isModify);

        } else if ($this.hasClass('opt-del')) {

            $this.tipsLayer('确定删除？', function () {
                O.ajaxEx({
                    url: O.path('basic/member-custom/delete'),
                    type: 'post',
                    data: {
                        id: id
                    }
                }).then(function (res) {
                    if (res.result) {
                        tips('删除成功');
                        row.fadeOut(function () {
                            row.remove();
                            var rows = table.find('tbody tr');
                            rows.each(function (i) {
                                rows.eq(i).find('td:first').text(i + 1);
                            });
                        });
                    } else {
                        tips(res.msg, 'tips');
                    }
                });
            });

        } else if ($this.hasClass('opt-ok')) {
            var title = input.val();
            O.ajaxEx({
                url: O.path('basic/member-custom/edit'),
                type: 'post',
                data: {
                    id: id,
                    title: title
                }
            }).then(function (res) {
                if (res.result) {
                    tips('修改成功');
                    text.text(title);
                    toggleRowEdit(row, false);
                } else {
                    input.val(text.text());
                    tips(res.msg, 'tips');
                }
            });
        }
    });

    var close = false;

    add.on('click', function () {
        $(this).tipsLayer(addTpl, {
            ok_id: 'submit_btn',
            create: function (layer) {
                var title = layer.find('#title').focus();
                layer.find('#add_form').form({
                    submitbtn: 'submit_btn',
                    formName: 'add_form',
                    rules: [{
                        id: 'title',
                        required: true,
                        msg: {required: '请填写自定义信息'}
                    }],
                    submit: function (param, _data) {
                        close = true;
                        O.ajaxEx({
                            url: O.path('basic/member-custom/edit'),
                            type: 'post',
                            data: _data
                        }).then(function (res) {
                            if (res.result) {
                                tips('添加成功');
                                appendRow({
                                    id: res.id,
                                    title: title.val()
                                });
                            } else {
                                tips(res.msg, 'tips');
                            }
                        });
                    }
                });
            },
            ok: function () {
                return close;
            }
        });
    });

});
/**
 * Created by weizs on 2015/5/14.
 */
'use strict';
/*global O,$,define*/
define(function (require, exports, module) {
    var template = require('../../../../frontend/js/lib/template');
    require('../../../../frontend/js/lib/dialog');
    require('../../../../frontend/js/plugin/form.js');


    //tpl
    var addTpl = $('#add_tpl').html(),
        rowTpl = $('#row_tpl').html();

    var tips = function (msg, mode) {
        $.topTips({mode: mode || 'normal', tip_text: msg || '操作成功'});
    };

    var load = function (id) {
        O.ajaxEx({
            url: O.path('basic/signinpoint/list'),
            type: 'post',
            data: {
                scope_id: id
            }
        }).then(function (res) {
            var viewTable = $('#view-table');
            viewTable.find('tbody').html('');
            if (res.result) {
                viewTable.find('tbody').html(template(rowTpl, {
                    data: res
                }));
            }
        });
    };
    load();

    $('.certificate-form').off('click').on('click', '.opt-btn', function () {
        var $this = $(this);
        if ($this.hasClass('opt-del')) {

            $this.tipsLayer('确定删除？', function () {
                O.ajaxEx({
                    url: O.path('basic/signinpoint/delete'),
                    type: 'post',
                    data: {
                        id: $this.data('id')
                    }
                }).then(function (res) {
                    if (res.result) {
                        tips(res.msg);
                        var row = $this.closest('tr');
                        row.fadeOut(function () {
                            row.remove();
                        });
                    } else {
                        tips(res.msg, 'tips');
                    }
                });

            });
        } else if ($this.hasClass('opt-modify')) {
            $this.closest('tr').addClass('edit');
        } else if ($this.hasClass('opt-cancel')) {
            if ($this.data('id')) {
                $this.closest('tr').removeClass('edit');
            } else {
                $this.closest('tr').remove();
            }
        } else if ($this.hasClass('opt-save')) {

            var title = $this.closest('tr').find('.opt-input-title').val(),
                value = $this.closest('tr').find('.opt-input-value').val();

            var num_re = /^[1-9][0-9]{0,3}$/;
            if (!num_re.test(title)) {
                tips('天数必须为大于0的最大4位数', 'tips');
                return false;
            }

            var float_re = /^[1-9][0-9]{0,5}\.[0-9]{1,2}$/;
            if (!num_re.test(value) && !float_re.test(value)) {
                tips('积分必须为整数或者至多两位小数！', 'tips');
                return false;
            }

            O.ajaxEx({
                url: O.path('basic/signinpoint/edit'),
                type: 'post',
                data: {
                    id: $this.data('id'),
                    title: title,
                    value: value,
                    scope_id: $('#scope_id').val(),
                }
            }).then(function (res) {
                if (res.result) {
                    tips(res.msg);
                } else {
                    tips(res.msg, 'tips');
                }
                var $new_title = $this.closest('tr').find('.opt-input-title').val(),
                    $new_value = $this.closest('tr').find('.opt-input-value').val();

                $this.closest('tr').find('.opt-del').attr('data-id', res.id).end().find('.opt-save').attr('data-id', res.id);
                $this.closest('tr').find('.opt-input-title').val($new_title).end().find('.v-text-title').html($new_title).end().removeClass('edit');
                $this.closest('tr').find('.opt-input-value').val($new_value).end().find('.v-text-value').html($new_value).end().removeClass('edit');
            });
        } else if ($this.hasClass('add-point-type')) {
            var tbody = $('.table').find('tbody'),
                text_title = tbody.find('tr:last span.v-text-title').text(),
                add_title = text_title === '' ? 1 : parseInt(text_title) + 1;
            tbody.append(template(addTpl, {
                title: add_title,
                value: add_title * 5
            }));
        }
    });
});
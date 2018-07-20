/**
 * Created by weizs on 2015/5/25.
 */
'use strict';
/*global O,$,define*/
define(function (require, exports, module) {
    var template = require('../../../../frontend/js/lib/template');
    require('../../../../frontend/js/lib/dialog');


    var viewTable = $('#view-table'),
        rowTpl = $('#row_tpl').html();


    var tips = function (msg, mode) {
        $.topTips({mode: mode || 'normal', tip_text: msg || '操作成功'});
    };

    var load = function (id) {
        O.ajaxEx({
            url: O.path('basic/attention/list'),
            type: 'post',
            data: {
                //id:id
            }
        }).then(function (res) {
            if (res.result) {
                viewTable.find('tbody').html(template(rowTpl, {
                    data: res
                }));
            }
        });
    };
    load();

    $('.attention-form').on('click', '.opt-btn', function () {
        var $this = $(this),
            row = $this.closest('tr'),
            vText = row.find('.v-text'),
            optInput = row.find('.opt-input');
        if ($this.hasClass('opt-del')) {
            O.ajaxEx({
                url: O.path('basic/attention/delete'),
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
                }
            });
        }
        else if ($this.hasClass('opt-modify')) {
            row.addClass('edit');
        } else if ($this.hasClass('opt-cancel')) {
            row.removeClass('edit');
            optInput.attr('value', vText.text());
            optInput.val(vText.text());
        }
        else {
            O.ajaxEx({
                url: O.path('basic/attention/edit'),
                type: 'post',
                data: {
                    id: $this.data('id'),
                    title: $this.closest('tr').find('.opt-input-title').text(),
                    value: $this.closest('tr').find('.opt-input-value').val(),
                    scope_id: $this.data('scope_id')
                }
            }).then(function (res) {
                if (res.result) {
                    var $new_title = $this.closest('tr').find('.opt-input-title').val(),
                        $new_value = $this.closest('tr').find('.opt-input-value').val();

                    $this.closest('tr').find('.opt-del').attr('data-id', res.id).end().find('.opt-save').attr('data-id', res.id);
                    $this.closest('tr').find('.opt-input-title').val($new_title).end().find('.v-text-title').html($new_title).end().removeClass('edit');
                    $this.closest('tr').find('.opt-input-value').val($new_value).end().find('.v-text-value').html($new_value).end().removeClass('edit');
                    $.topTips({tip_text: res.msg});
                    row.removeClass('edit');
                }
            });
        }
    });
});
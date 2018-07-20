/**
 * Created by weizs on 2015/5/14.
 */
'use strict';
/*global O,$,define*/
define(function (require, exports, module) {
    var utils = require('../../../../frontend/js/lib/utils');
    var template = require('../../../../frontend/js/lib/template');
    require('../../../../frontend/js/lib/dialog');


    var viewTable = $('#view-table'),
        rowTpl = $('#row_tpl').html();


    var load = function () {
        O.ajaxEx({
            url: O.path('basic/integration-rule/list'),
            type: 'post'
        }).then(function (res) {
            if (res.result) {
                viewTable.find('tbody').html(template(rowTpl, {
                    data: res.data.items
                }));
            }
        });
    };

    load();


    $('.integral-form').on('click', '.opt-btn', function () {
        var $this = $(this),
            row = $this.closest('tr'),
            vText = row.find('.v-text'),
            optInput = row.find('.opt-input');

        if ($this.hasClass('opt-modify')) {
            row.addClass('edit');
        } else if ($this.hasClass('opt-cancel')) {
            row.removeClass('edit');
            optInput.attr('value', vText.text());
            optInput.val(vText.text());
        } else {
            O.ajaxEx({
                url: O.path('basic/integration-rule/edit'),
                type: 'post',
                data: {
                    code: $this.data('code'),
                    value: optInput.val(),
                    scope_id: $('#scope_id').val(),
                    id: $this.data('value_id')
                }
            }).then(function (res) {
                if (res.result) {
                    vText.text(optInput.val());
                    $.topTips({tip_text: '保存成功！'});
                    row.removeClass('edit');
                }
            });
        }

        optInput.off('keypress').on('keypress', function (e) {
            var val = this.value,
                pos = utils.getPosition(optInput.get(0)),
                keyCode = e.keyCode || e.which;
            return val.substr(val.indexOf('.')).length - 1 < 2 &&
                (keyCode >= 48 && keyCode <= 57) ||
                (pos === 0 && val.indexOf('-') === -1 && keyCode === 45) ||
                (val.indexOf('.') === -1) ||
                (keyCode >= 35 && keyCode <= 40) ||
                keyCode === 8 ||
                keyCode === 46;
        });

        optInput.off('keyup').on('keyup', function () {
            var val = optInput.val();
            if (!/^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/.test(val) || val.substr(val.indexOf('.')).length - 1 > 2) {
                val = (val + '').replace(/[^0-9.-]/g, '');
                if (val.lastIndexOf('-') > 0) {
                    val = '-' + val.substr(1).replace(/-/g, '');
                }
                var idx = val.indexOf('.');
                var lastIdx = val.lastIndexOf('.');
                if (idx > -1 && lastIdx !== idx) {
                    if (idx === 0) {
                        val = val.substr(1);
                    }
                }
                if (idx !== -1) {
                    val = val.substring(0, idx) + '.' + val.substr(idx).replace(/\./g, '').substring(0, 2);
                }
                optInput.val(val);
            }
            return true;
        });

    });
});
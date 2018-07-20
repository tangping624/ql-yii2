/**
 * Created by weizs on 2015/9/23.
 */
'use strict';
/*global O,$,define*/
define(function (require, exports, module) {
    require('../../../../frontend/js/lib/dialog');


    var tips = function (msg, mode) {
        $.topTips({mode: mode || 'normal', tip_text: msg || '操作成功'});
    };

    var form = $('.name-rule-form'),
        value = $('#value');

    var radioEnable = function (flag) {
        if (flag) {
            form.addClass('edit');
            form.find('.table').on('click', '.form-radio', function () {
                $(this).addClass('selected').siblings().removeClass('selected');
            });
        } else {
            form.removeClass('edit');
            form.find('.table').off('click')
                .find('[data-value=' + value.val() + ']')
                .addClass('selected').siblings().removeClass('selected');
        }
    };

    form.on('click', '.opt-btn', function () {
        var $this = $(this);
        if ($this.hasClass('opt-modify')) {
            radioEnable(true);
        } else if ($this.hasClass('opt-cancel')) {
            radioEnable(false);
        } else if ($this.hasClass('opt-save')) {
            var val = form.find('.table .selected').data('value');
            O.ajaxEx({
                url: O.path('basic/name-rule/edit'),
                type: 'post',
                data: {value: val}
            }).then(function (res) {
                if (res.result) {
                    tips('修改成功');
                    value.val(val);
                    radioEnable(false);
                } else {
                    tips(res.msg, 'tips');
                }
            });
        }

    });

});
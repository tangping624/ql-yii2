/**
 * Created by weizs on 2015/5/14.
 */
'use strict';
/*global O,$,define*/
define(function (require, exports, module) {
    require('../../../../frontend/js/lib/dialog.js');
    require('../../../../frontend/js/plugin/form.js');
    require('../../../../frontend/js/lib/jquery.number/jquery.number');


    var tips = function (msg, mode) {
        $.topTips({mode: mode || 'normal', tip_text: msg || '操作成功'});
    };

    var form = $('#form');
    var point_gift_limit = form.find('#point_gift_limit');

    form.form({
        submitbtn: 'submit_btn',
        formName: 'form',
        rules: [{
            id: 'point_gift_limit',
            required: false,
            notAutoCheck: true,
            msg: {not_selected: '请填写积分赠送上限'},
            fun: function () {
                var point_gift_enabled = $('.radio-btn .selected input').val();
                if (point_gift_enabled === '1') {
                    if (point_gift_limit.val() === '') {
                        return 'not_selected';
                    }
                }
                return '';
            }
        }],
        submit: function (param, _data, node) {
            O.ajaxEx({
                node: node,
                url: O.path('basic/pointgiftenabled/edit'),
                type: 'post',
                data: _data
            }).then(function (res) {
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

    form.on('click', '.radio-btn .form-radio', function () {
        $('#div_point_gift_limit').toggle($(this).find('input').val() == 1);
    });

    point_gift_limit.number(true);
});

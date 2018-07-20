/**
 * Created by weizs on 2016/3/30.
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

    form.form({
        submitbtn: 'submit_btn',
        formName: 'form',
        submit: function (param, data, node) {
            O.ajaxEx({
                node: node,
                url: O.path('basic/recommend-integral/edit'),
                type: 'post',
                data: data
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

    $('input').number(true);

});

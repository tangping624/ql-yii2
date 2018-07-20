/**
 * Created by weizs on 2015/12/16.
 */
'use strict';
/*global O,$,define,plupload*/
define(function (require, exports, module) {
    require('../../../../frontend/js/lib/dialog');
    require('../../../../frontend/js/plugin/form.js');

    var name_text = $('#name_text');
    var name = $('#name');
    var value = $('#value');

    $('#user_form').form({
        submitbtn: 'submit_btn',
        formName: 'user_form',
        rules: [
            {
                id: 'name',
                msg: {required: '请输入名称'},
                fun: function () {
                    name_text.text(this.value);
                    if (this.value === '' && value.val() !== '') {
                        return 'required';
                    }
                }
            },
            {
                id: 'value',
                msg: {required: '请输入联系电话'},
                fun: function () {
                    if (this.value === '' && name.val() !== '') {
                        return 'required';
                    }
                }
            }
        ],
        submit: function (param, data, node) {
            O.ajaxEx({
                node: node,
                url: O.path('/basic/homepage/add-banner'),
                data: data,
                type: 'post'
            }).then(function (res) {
                if (res.result) {
                    $.topTips({tip_text: '发布成功'});
                    setTimeout(function () {
                        window.location.href = O.path('/basic/homepage/index#home_phone');
                    }, 1000);
                } else {
                    $.topTips({tip_text: res.msg, mode: 'tips'});
                }
            });
        }
    });

});
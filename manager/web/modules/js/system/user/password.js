define(function(require , exports , module){
    require('../../../../frontend/js/plugin/form.js');
    require('../../../../frontend/js/lib/template.js');
    require('../../../../frontend/js/lib/dialog.js');
    require('../../../../frontend/js/lib/tooltips/tooltips');

    var modifyPwdTpl = $('#password').html(),
        new_password=$('#new_password'),
        confirm_password=$('#confirm_password');

    var showLayer = function () {
        var box = $.box({
            content: modifyPwdTpl,
            title: '修改密码',
            height: 'auto',
            width: 550
        });

        $('#password_form').form({
            submitbtn: 'save_password',
            rules: [
                {id: 'old_password', required: true, msg: {required: '请输入当前密码', pwd_error: '当前密码错误'}},
                {id: 'new_password', required: true, msg: {required: '请输入新密码'},fun:function(){
                    confirm_password.keyup();
                }},
                {
                    id: 'confirm_password',
                    required: true,
                    msg: {required: '请输入确认密码', diff_show: '两次输入的密码不一样'},
                    fun: function () {
                        new_password.keyup();
                        var newPwd = $('#new_password').val();
                        var confirmPwd = $('#confirm_password').val();
                        if (newPwd != confirmPwd) return 'diff_show';
                    }
                }

            ],
            submit: function (paramStr) {
                O.ajaxEx({
                    url: O.path('/system/user/update-password'),
                    type: 'post',
                    data: paramStr
                }).then(function (res) {
                    if (res.status) {
                        $.topTips({tip_text: '修改成功'});
                    } else {
                        $.topTips({tip_text: res.msg, mode: 'tips'});
                    }
                    box.close();
                    box.remove();
                });
            }
        });

        $(box.node).on('click','#cancel_password',function(){
            box.close();
            box.remove();
        });
    };

    $('#modify_pwd').on('click', function () {
        showLayer();
    });

});
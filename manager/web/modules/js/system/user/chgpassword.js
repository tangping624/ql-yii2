/**
 * Created by kongy on 2015/4/27.
 */  
define(function (require, exports, module) {
    //加载依赖性
    var form = require('/frontend/js/plugin/form.js');
    require('/frontend/js/lib/overall.js'); 

    /**
     * 消息提示
     * @param message
     * @param isNormal
     */
    function showMessage(message, isNormal) {
        var parent = window.parent || window; 
        parent.$.topTips({
            mode: isNormal ? 'normal' : 'warning',
            tip_text: message
        });
    }

    function savePassword(id, data) { 
        var result = null;
        Util.ajaxEx({
            type: 'post',
            data: {password: data.name},
            url: Overall.path('/system/user/savepassword?id=' + id),
            async: false,
            success: function (data) {
                result = data;
                showMessage(data.message, data.status);
            }
        });
        return result; 
    }

    function getFormData() { 
        var data = {
            name: $('#password').val() 
        }; 
        return data;
    } 
    exports.init = function (id) { 
        //保存检验规则
        var formRules = [
            {
                id: 'password',
                required: true, 
                msg: {required: '请输入密码'} 
            }
        ]; 
        $('#contractor_form').form({ 
            submitbtns: [$('#submit_btn')],
            rules: formRules,
            validate: function () { 
                return true;
            },
            submit: function () { 
                var data = getFormData();
                var result = savePassword(id, data);
                if (result !== null && result.status) {
                    parent && parent.DialogAddUser && parent.DialogAddUser.ok(result);
                }
            }
        });

        //对话框取消按钮
        $('#cancel_btn').bind('click', function () { 
            //取消
            parent && parent.DialogAddUser && parent.DialogAddUser.cancel();
        });
    }
});
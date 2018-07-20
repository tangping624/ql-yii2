/**
 * Created by quw on 2015/3/23.
 */ 
define(function (require, exports, module) {
    //加载依赖性 
    var form = require('/frontend/js/plugin/form.js');
    require('/frontend/js/lib/overall.js');
    require('/modules/js/public/plugin/topTips.js'); 

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

    /**
     * 功能：保存用户数据
     * @param data 用户表单信息
     * */
    var saveUser = function (data, action) {
        var result = false; 
        Util.ajaxEx({
            type: 'post',
            data: data,
            url: Overall.path('/system/user/' + action),
            async: false,
            success: function (data) {
                if (data.status) {
                    result = true; 
                    showMessage(data.message, true);
                } else {
                    showMessage(data.message, false);
                }
            }
        });
        return result;
    }

    //userinfo模块的初始化
    exports.init = function () { 
        //保存新增
        $('#user_form').form({
            submitbtns: [$('#submit_new_btn'), $('#submit_btn')],
            rules: [
                {
                    id: 'name',
                    required: true,
                    msg: {'required': '请输入姓名', limit: '姓名最多20个字符'},
                    fun: function (field, ele) {
                        if(field.value){
                            if (field.value.length > 20) {
                                return 'limit';
                            }
                        }else{
                            return 'required';
                        }
                    }
                },
                {
                    id: 'account',
                    required: true,
                    msg: {'required': '请输入帐号', limit: '帐号最多20个字符'},
                    fun: function (field, ele) {
                        if(field.value){
                            if (field.value.length > 20) {
                                return 'limit';
                            }
                        }else{
                            return 'required';
                        }
                    }
                },
                {
                    id: 'mobile',
                    required: true,
                    msg: {'required': '请输入手机号码',validator: '手机号码格式错误'},
                    fun: function (field, ele) {
                        if(field.value){
                            if (!(/^0?1[3|4|5|7|8][0-9]\d{8}$/gi.test(field.value))) {
                                return 'validator';
                            }
                        }else{
                            return 'required';
                        }
                    }
                },
                {
                    id: 'email',
                    required: false,
                    msg: { validator: '邮箱格式错误'},
                    fun: function (field, ele) {
                        if (field.value && !(/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/gi.test(field.value))) {
                            return 'validator';
                        }
                    }
                } 
            ],
            validate: function () { //添加其他验证规则 
                return true;
            },
            submit: function (data, _data, $btn) {
                var action = ($('#id').length > 0 && $('#id').val() != '') ? 'update' : 'save'; 
                if($('#account_id').length>0 && $('#account_id').val() != ''){
                    action = 'save-account';
                }
                if (saveUser(data, action)) {
                    if ($btn.attr('id') === 'submit_new_btn') {
                        //保存后操作
                        parent && parent.DialogAddUser && parent.DialogAddUser.save();

                        //重置表单
                        $('#user_form')[0].reset();  
                    } else {
                        //点确定后操作
                        parent && parent.DialogAddUser && parent.DialogAddUser.ok();
                    }
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
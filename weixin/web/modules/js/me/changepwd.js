define(function(require,exports,modules){
    $.Form=require('../../../mobiend/js/mod/form');
    modules.exports={
        init:function(){
            this.login();
        },
        login:function(){
            function getQueryString(name) {
                var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
                var r = window.location.search.substr(1).match(reg);
                if (r != null) return decodeURIComponent(r[2]); return null;
            }
            $('.top-text').on('click',function(){
                var _checkRules ={
                    oPwd: function (value, type) {
                        var msg='';
                        if(value==''){msg = '亲，原始密码不能为空哦！'}
                        else if(value.trim().length < 6){msg = '原始密码请输入6位以上字符！'};
                        if (msg) return msg;
                        return true;
                    },
                    nPwd: function (value, type) {
                        var msg='';
                        if(value==''){msg = '亲，新密码不能为空哦！'}
                        else if(value.trim().length < 6){msg = '新密码请输入6位以上字符！'};
                        if (msg) return msg;
                        return true;
                    },
                    isPwd: function (value, type) {
                        var msg='';
                        if(value==''){msg = '亲，确认密码不能为空哦！'}
                        else if(value.trim().length < 6){msg = '确认密码请输入6位以上字符！'};
                        if (msg) return msg;
                        return true;
                    }
                }
                var form = new $.Form('login_form',_checkRules);
                form.validate(function(errorDesc,errorType,node,prop){
                    $.Env.showMsg(errorDesc);
                },function(str,type,obj){
                    var opwd = $('input[name="oPwd"]').val().trim();
                    var npwd = $('input[name="nPwd"]').val().trim();
                    var ispwd = $('input[name="isPwd"]').val().trim();
                    if(opwd == npwd){
                        $.Env.showMsg('新密码与旧密码不能一样！');
                        return false;
                    }
                    if(npwd == ispwd){
                        $.ajaxEx({
                            url:$.path('/me/me/update-pwd'),
                            type:'get',
                            data:{id:getQueryString('id'),opwd:opwd,npwd:ispwd,mobile:getQueryString('mobile')},
                            success:function(data){
                                if(data.result){
                                    $.Env.showMsg('修改成功！');
                                    location.href='/me/me/login-index';
                                }else{
                                    $.Env.showMsg(data.msg);
                                }

                            },
                            error:function(data){
                                return data.msg;
                            }
                        })
                    }else{
                        $.Env.showMsg('新密码输入不一致！');
                    }
                    
                })
            })
        }
    }
})

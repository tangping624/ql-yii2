define(function(require,exports,modules){
    $.Form=require('../../../mobiend/js/mod/form');
    modules.exports={
        init:function(){
            this.login();
        },
        login:function(){
            $('.formbtn button').on('click',function(){
                var _checkRules ={
                    number: function (value, type) {
                        var msg='';
                        if(value==''){msg = '亲，手机号不能为空哦！'}
                        else if(!(/^1[3|4|5|7|8][0-9]\d{4,8}$/.test(value))){msg = '亲，手机号格式不正确！'};
                        if (msg) return msg;
                        return true;
                    },
                    password: function (value, type) {
                        var msg='';
                        if(value==''){msg = '亲，密码不能为空！'}
                        else if(value.trim().length < 6){msg = '密码请输入6位以上字符！'};
                        if (msg) return msg;
                        return true;
                    },
                }
                var form = new $.Form('login_form',_checkRules);
                form.validate(function(errorDesc,errorType,node,prop){
                    $.Env.showMsg(errorDesc);
                },function(str,type,obj){
                    $.ajaxEx({
                        url:$.path('/me/me/login'),
                        type:'post',
                        data:{mobile:$('#login_form input[name="number"]').val(),pwd:$('#login_form input[name="password"]').val()},
                        success:function(data){
                            if(data.result){
                                location.href='/me/me/index';
                            }else{
                               	$.Env.showMsg('账号或密码不正确，请重新登录');
                            }

                        },
                        error:function(data){
                            return data.msg;
                        }
                    })
                })
            })
        }
    }
})

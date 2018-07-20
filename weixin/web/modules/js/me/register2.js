define(function(require,exports,modules){
    $.Form=require('../../../mobiend/js/mod/form');
    modules.exports={
        init:function(){
            this.login();
        },
        login:function(){
            //获取url中的tel
            function getQueryString(name) {
                var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
                var r = window.location.search.substr(1).match(reg);
                if (r != null) return decodeURIComponent(r[2]); return null;
            }
            $('.formbtn button').on('click',function(event){
                var _checkRules ={
                    nPwd: function (value, type) {
                        var msg='';
                        if(value==''){msg = '亲，密码不能为空哦！'}
                        else if(value.trim().length < 6){msg = '密码请输入6位以上字符！'};
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
                    var nPwd=$('#login_form input[name="nPwd"]').val().trim();
                    var isPwd=$('#login_form input[name="isPwd"]').val().trim();
                    if(nPwd!=''&&isPwd!=''&&nPwd == isPwd){
                        $.ajaxEx({
                            url:$.path('/me/me/save'),
                            type:'get',
                            data:{mobile:getQueryString('mobile') == null ? '' : getQueryString('mobile'),pwd:isPwd},
                            success:function(data){
                                if(data.result){
                                    window.location = '/me/me/login-index';
                                }else{
                                    $.Env.showMsg(data.msg);
                                }
                            },
                            error:function(data){
                                return data.msg;
                            }
                        });
                    }else{
                        $.Env.showMsg('密码输入不一致！');
                    }
                });    
            });
        }
    }
})

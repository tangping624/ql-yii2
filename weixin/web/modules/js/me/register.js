define(function(require,exports,modules){
    $.Form=require('../../../mobiend/js/mod/form');
    modules.exports={
        init:function(){
            this.login();
        },
        login:function(){
            var reCode = '';
            $('#login_form input[name="number"]').on('keyup',function(){
                if($(this).val().trim().length==11){
                    $('#getCode').addClass('myCur');
                }else{
                    $('#getCode').removeClass('myCur');
                }
            });
            $('#getCode').on('click',function(event){
                var _checkRules ={
                    number: function (value, type) {
                        var msg='';
                        if(value==''){msg = '亲，手机号不能为空哦！'}
                        else if(!(/^1[3|4|5|7|8][0-9]\d{4,8}$/.test(value))){msg = '亲，手机号格式不正确！'};
                        if (msg) return msg;
                        return true;
                    }
                }
                var form = new $.Form('login_form',_checkRules);
                form.validate(function(errorDesc,errorType,node,prop){
                    $.Env.showMsg(errorDesc);
                },function(str,type,obj){
                    var _this =$('#getCode');
                    if(_this.hasClass('myCur')){
                        var html = _this.html();
                         _this.removeClass('myCur').html(59+'秒后重试');
                        var t =59;
                        var setInterval1 = setInterval(function(){
                             t--;
                             _this.html(t+'秒后重试');
                             $('input[name="number"]').attr('disabled','disabled');
                             if(t==0) {
                                clearInterval(setInterval1);
                                _this.addClass('myCur').html(html);
                                $('input[name="number"]').removeAttr('disabled');
                             }
                         },1000);
                         $.ajaxEx({
                            url:$.path('/me/me/check-code'),
                            type:'get',
                            data:{mobile:$('#login_form input[name="number"]').val()},
                            success:function(data){
                                if(data.result){
                                    $.Env.showMsg('验证码已发送至您的手机！');
                                    reCode = data.verifycode;
                                }else{
                                    $.Env.showMsg('验证码获取失败！');
                                }
                            },
                            error:function(data){
                                return data.msg;
                            }
                        });
                    }else{
                        event.preventDefault();
                    }
                });    
            });
            $('.formbtn button').on('click',function(){
                var _checkRules ={
                    number: function (value, type) {
                        var msg='';
                        if(value==''){msg = '亲，手机号不能为空哦！'}
                        else if(!(/^1[3|4|5|7|8][0-9]\d{4,8}$/.test(value))){msg = '亲，手机号格式不正确！'};
                        if (msg) return msg;
                        return true;
                    },
                    code: function (value, type) {
                        var msg='';
                        if(value==''){msg = '亲，验证码不能为空哦！'}
                        if (msg) return msg;
                        return true;
                    }
                };
                var form = new $.Form('login_form',_checkRules);
                form.validate(function(errorDesc,errorType,node,prop){
                    $.Env.showMsg(errorDesc);
                },function(str,type,obj){
                    var tel = $('#login_form input[name="number"]').val();
                    var code = $('#login_form input[name="code"]').val();
                    if(tel!=''&&code!=''){
                        if(code == reCode){
                            window.location='/me/me/register-next?mobile='+tel+'&code='+code;
                        }else{
                            $.Env.showMsg('您的验证码输入不正确！');
                        }
                    }else{
                        $.Env.showMsg('手机号或验证码不能为空！');
                    }
                });
            })
        }
    }
})

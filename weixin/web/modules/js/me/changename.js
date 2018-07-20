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
            };
            //$('input[name="changename"]').attr('placeholder',getQueryString('name'));
            $('input[name="changename"]').val(getQueryString('name'));
            $('.top-text').on('click',function(){
                var _checkRules ={
                    changename: function (value, type) {
                        var msg='';
                        if(value==''){msg = '亲，昵称不能为空哦！'}
                        if (msg) return msg;
                        return true;
                    }
                }
                var form = new $.Form('login_form',_checkRules);
                form.validate(function(errorDesc,errorType,node,prop){
                    $.Env.showMsg(errorDesc);
                },function(str,type,obj){
                    $.ajaxEx({
                        url:$.path('/me/me/update-name'),
                        type:'get',
                        data:{id:getQueryString('id'),name:$('input[name="changename"]').val()},
                        success:function(data){
                            if(data.result){
                                $.Env.showMsg(data.msg);
                                location.href='/me/me/index';
                            }else{
                               	$.Env.showMsg(data.msg);
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

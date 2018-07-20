seajs.use(['dialog','template','form','validate'],function(){
    var _validate = new DataValid('<p class="color-red1">{errorHtml}</p>');
    var tips = function (msg, mode) {
        $.topTips({mode: mode || 'normal', tip_text: msg || '操作成功'});
    };
    var _id = $('#id').val()?$('#id').val():"";
    var title = $('.title').text();
    var code = wenf.Geturl('code');
    $('#submit_btn').on('click',function(){
        if(_doCheck()){
             $(this).attr("disabled","true").removeClass("bg-green").addClass("color-gray");
            var curEle = $(this);
            var data = _getData();
            O.ajaxEx({
                type:'post',
                data:data,
                url:O.path('basic/merchant/score-add'),
                success:function(data){
                    if(data.result===true){
                        $(window).off('beforeunload');
                        showMessage("保存成功","isNormal")
                        setTimeout(" location.href = '/basic/parameter/index?_ac=merchant'",1000);
                    }else{
                        showMessage(data.msg,"isNormal")
                        curEle.removeAttr("disabled").removeClass("color-gray").addClass("bg-green");
                    }
                },
                error:function(){
                    tips('网络错误','tips');
                }
            });
        }else{
            return false;
        }
    });
    var _getData = function(){

        return {
            'id': _id,
            'title':title,
            'code':'ScoreSet',
            'value':$("#score_discount").val()
        };
    };

    var _checkCfg = {
        config:function(){
            return [{
                id:'score_discount',
                rules:'required',
                ruleMsg:{'required':'请输入积分抵扣现金比例'}
            }];
        }
    };
    var _doCheck = function(){
        if(_validate.fieldList.length === 0){
            _validate.addFields(_checkCfg.config());
        }
        if(!_validate.process(false)){
            // console.log("2");
            var id = _validate.errorField.split(',')[1];
            $('#'+id)[0].scrollIntoView();
            return false;
        }
        return true;
    };
    function showMessage(message, isNormal) {
        var parent = window.parent || window;
        parent.$.topTips({
            mode: isNormal ? 'normal' : 'warning',
            tip_text: message
        });
    }


});
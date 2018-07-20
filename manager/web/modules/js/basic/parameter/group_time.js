define(function(require,exports,modules) {
    require('form');
    require('/frontend/js/lib/dialog');
    require('/frontend/js/lib/tooltips/tooltips');
    window.Template = require('/frontend/js/lib/template');
    window.DataValid = require('/frontend/js/lib/validate');
    var _validate = new DataValid('<p class="color-red1">{errorHtml}</p>');
    var tips = function (msg, mode) {
        $.topTips({mode: mode || 'normal', tip_text: msg || '操作成功'});
    };
    var _id=$('#id').val()?$('#id').val():'';
     $('#submit_btn').on('click',function(){
        if(_doCheck()){
             $(this).attr("disabled","true").removeClass("bg-green").addClass("color-gray");
            var curEle = $(this);
            var data = _getData();
            O.ajaxEx({
                type:'post',
                data:data,
                url:O.path('/basic/group/group-time-add'),
                success:function(data){
                    if(data.result===true){
                        $(window).off('beforeunload');
                        tips(data.msg,'tips');
                        setTimeout(" location.href = '/basic/parameter/index?_ac=group'",1000);
                    }else{
                        tips(data.msg,'tips');
                        curEle.removeAttr("disabled").removeClass("color-gray").addClass("bg-green");
                    }
                },
                error:function(){
                    console.log("错误");
                    tips('网络错误','tips');
                }
            });
        }else{
            return false;
        }
    });
    //   $("#group-time").keydown(function (e) {
    //      var code = parseInt(e.keyCode);
    //      if (code >= 96 && code <= 105 || code > 48 && code <= 57 || code == 8) {
    //          return true;
    //          console.log(code);
    //      } else {
    //          console.log(code);
    //          return false;
    //      }
    // })
    $("#group-time").bind("input propertychange", function () {
    if (isNaN(parseFloat($(this).val())) || parseFloat($(this).val()) <= 0) $(this).val('');
     })
     //  $('#group-time').on('keyup',function(){
     //     if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}
     // });
     //  $('#group-time').on('afterpaste',function(){
     //     if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}
     //  });
     //   $('#group-time').on('keydown',function(e){

     //         var key=e.keyCode;
     //         console.log(key);
     //          if(key==13){
     //              e.preventDefault();
     //              console.log('00')
     //              if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}
     //          }
     //   });
     $('#cancel_btn').click(function(){
     	setTimeout(" location.href = '/basic/parameter/index?_ac=group'",500);
     });
    var _getData = function(){

        return {
            'id': _id,
            'value':$('#group-time').val()
        };
    };

    var _checkCfg = {
        config:function(){
            return [{
                id:'group-time',
                rules:'required',
                ruleMsg:{'required':'请输入关闭时间'}
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

});
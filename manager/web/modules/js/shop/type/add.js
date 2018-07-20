define(function(require, exports, module) {
    require('form');
    require('/frontend/js/lib/dialog');
    require('/frontend/js/lib/tooltips/tooltips');
    window.Template = require('/frontend/js/lib/template');
    window.DataValid = require('/frontend/js/lib/validate');
    var _id=$('#id').val();
    //事件绑定
    if(!_id){
        $(".next").show();
    }
    var getQueryString=function(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = location.search.substr(1).match(reg);
        if (r != null) return unescape(decodeURI(r[2])); return null;
    }
    var appcode=getQueryString('app_code');
    var _bindEvent = function() {
        //发布按钮
        $('.submit_btn').on('click', function() {
            
            if(_doCheck()&&$(this).hasClass("next")) {
                var curEle = $(this);
                $(this).removeClass("bg-green").addClass("color-gray").attr("disabled","true");
                var data = _getData();
                O.ajaxEx({
                    type: 'post',
                    data: data,
                    url: O.path('/shop/type/save'),
                    success: function(data) {
                    	if(data.result){
                           showMessage('保存成功','isNormal');
                            
                            // $(window).off('beforeunload');
                            setTimeout("location.reload()",1000);
                        }else{     
                            _showTips(data.msg);
                            curEle.removeClass("color-gray").addClass("bg-green").attr("disabled","false");
                        }
                    },
                    error: function() {
                        _showTips('网络错误');                       
                    }
                   });
            
            }else if(_doCheck()&&!$(this).hasClass("next")){
                var curEle = $(this);
                $(this).removeClass("bg-green").addClass("color-gray").attr("disabled","true");
                var data = _getData();
                O.ajaxEx({
                    type: 'post',
                    data: data,
                    url: O.path('/shop/type/save'),
                    success: function(data) {
                        if(data.result){
                            showMessage('保存成功','isNormal');
                            $(window).off('beforeunload');
                            setTimeout(function(){
                                location.href = $('#url').val();
                            },500);
                        }else{     
                            _showTips(data.msg);
                            curEle.removeClass("color-gray").addClass("bg-green").attr("disabled","false");
                        }
                    },
                    error: function() {
                        _showTips('网络错误');                       
                    }
                   });
                 $(window).on('beforeunload', function(e) {
                    if(!O.compare(_initDate, _getData())) {
                        return '离开后，刚刚填写数据会丢失';
                    }
                });
            }else {
                return false;
            }
        });
        $('input').keyup(function(){
            if($(this).val().trim()){
                $('.color-red1').hide();
            }else{
                $('.color-red1').show();
            }

        })
 
       
    };


    //获取得到的数据  提交数据用
    var _getData = function() {
        return {
        	'id': _id,
            'name':  $('#typename').val(),
            app_code:appcode
        };
	}
    var _showTips = function(tip) {
        var d = $.tips(tip);
        setTimeout(function() {
            d.close().remove();
        }, 2000);
    }
    var showMessage=function(message, isNormal) {
        var parent = window.parent || window;
        parent.$.topTips({
            mode: isNormal ? 'normal' : 'warning',
            tip_text: message
        });
    }
    //验证配置、规则
    var _checkCfg = {
        config: function() {
            return [{
                id: 'typename', 
                msg: {
                        empty: '请输入分类名称' 
                },
                fun: function(el) {                      
                       if($('#typename').val().length === 0) 
                        return 'empty';
                } 
            }]
        }
    };
	var _doCheck = function() {        
        if (_validate.fieldList.length === 0) {
            _validate.addFields(_checkCfg.config());
        }

        if (!_validate.process(false)) {
            var id = _validate.errorField.split(',')[1];
            $('#' + id)[0].scrollIntoView();//之后添加效果
            $('.color-red1').css({'color':'#e15f63','margin-left':'75px'})
            return false;
        }
        return true;
    };
	_initDate = _getData();
	

    var _init = {
        init : function() {  
            _bindEvent();
            _validate = new DataValid('<p class="color-red1">{errorHtml}</p>');

        },
    };
    return _init;
});
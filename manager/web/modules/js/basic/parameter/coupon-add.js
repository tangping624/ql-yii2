seajs.use(['dialog','template','form','validate','laydate'],function(){
    var _validate = new DataValid('<p class="color-red1">{errorHtml}</p>');
    var tips = function (msg, mode) {
        $.topTips({mode: mode || 'normal', tip_text: msg || '操作成功'});
    };

    var _id,_initDate=null;
    _id= getQueryString("id");
    var checked;
    function getQueryString (name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);  //获取url中"?"符后的字符串并正则匹配
        var context = "";
        if (r != null)
            context = r[2];
        reg = null;
        r = null;
        return context == null || context == "" || context == "undefined" ? "" : decodeURIComponent(context);
    }

    $('#submit_btn').on('click',function(){
        if(_doCheck()){
             $(this).attr("disabled","true").removeClass("bg-green").addClass("color-gray");
            var curEle = $(this);
            var data = _getData();
            O.ajaxEx({
                type:'post',
                data:data,
                url:O.path('/basic/coupon/save'+(_id?'&id='+_id:'')),
                success:function(data){
                    if(data.result===true){
                        $(window).off('beforeunload');
                        showMessage("保存成功","isNormal")
                        setTimeout("location.href ='/basic/coupon/coupon-set'",1000);
                    }else{
                        showMessage(data.msg,"isNormal")
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
    $(window).on('beforeunload', function(e) {
        if(!O.compare(_initDate, _getData())) {
            return '离开后，刚刚填写数据会丢失';
        }
    });
    checked = $(".js-ioscheck").prop('checked');
    $(".js-ioscheck").click(function(){
        checked = $(this).prop('checked');
    })
    function showMessage(message, isNormal) {
        var parent = window.parent || window;
        parent.$.topTips({
            mode: isNormal ? 'normal' : 'warning',
            tip_text: message
        });
    }
    var _getData=function(){
        return {
            amount:$("#amount").val(),
            quantity:$("#quantity").val(),
            is_enable:checked==true?1:0,
            bgndate:$("#regstar").val()||'',
            enddate:$("#regend").val()||'',
        };
    };
    _initDate=_getData();
    var _checkCfg = {
        config:function(){
            return [{
                id:'amount',
                msg:{
                    empty:"请输入优惠券金额",
                    fault:"优惠券金额不能为0"
                },
                fun:function (el) {
                    if(el.value==="0"){
                        return 'fault';
                    }else if(!el.value){
                        return 'empty';
                    }
                }
            },
                {
                    id: 'quantity',
                    msg: {
                        empty: "请输入优惠券数量",
                        fault: "优惠券数量不能为0"
                    },
                    fun: function (el) {
                        if (el.value === "0") {
                            return 'fault';
                        } else if (!el.value) {
                            return 'empty';
                        }
                    }
                }
               ];
        }
    };
    var _doCheck = function(){
        if(_validate.fieldList.length === 0){
            _validate.addFields(_checkCfg.config());
        }
        if(!_validate.process(false)){
            var id = _validate.errorField.split(',')[1];
            $('#'+id)[0].scrollIntoView();
            return false;
        }
        if($("#regstar").val()&&$("#regend").val()){
            if($("#regstar").val()>$("#regend").val()){
                showMessage("结束时间必须在开始时间之后");
                return false
            }
        }
        return true;
    };
    var option={
        start:{
            elem: '#regstar',
            format: 'YYYY-MM-DD',
            istime: true,
            isclear: true,
            choose: function(datas){
                option.end.start = datas;
                option.end.min=datas;
            }
        },
        end:{
            elem: '#regend',
            format: 'YYYY-MM-DD',
            istime: true,
            isclear: true,
            choose: function(datas){
                option.start.max=datas;
            }
        }
    };
    laydate(option.start);
    laydate(option.end);



});
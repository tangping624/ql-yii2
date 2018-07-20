/**
 * Created by tx-03 on 2016/8/4.
 */
seajs.use(['dialog','template','form','validate'],function(){
    var _validate = new DataValid('<p class="color-red1">{errorHtml}</p>');
    var $selectMap = $('#select_map');
    var $proLon = $('#longitudes'),
        $prlLat = $('#latitudes'),
        $proAddr = $('#address');
    var _isEdit = $('#is_edit').val(),
        _id = $('#shop_id').val(),
        _count = 0,_initDate;
    var urlkeys;
    var _proxy = window.Proxy = {
        dialog:null,
        getPos:function(pos){
            return [
                $.trim($proLon.val())||0,
                $.trim($prlLat.val())||0
            ]
        },
        setPos:function(pos){
            if(pos[0]){
                $proLon.val(pos[0]);
                $prlLat.val(pos[1]);
            }
        },
        setAddr:function(addr){
            addr && $proAddr.val(addr);
        }
    };
    var _ac=getQueryString("_ac");
    if(_ac == 'shop'){
        urlkeys='?_ac=shop';
    }else{
        urlkeys='?_ac=group'
    }
    $("body").on("click",".backwards",function(){
        window.location.href='/basic/parameter/index'+urlkeys;
    })

    $('#submit_btn').on('click',function(){
        if(_doCheck()){
            $(this).attr('disabled','true').removeClass('bg-green').addClass('color-gray');
            var curEle = $(this);
            var data = _getData();
            O.ajaxEx({
                type:'post',
                data:data,
                url:O.path('/basic/merchant/save-merchant'+(_isEdit?'&id='+_id:'')+urlkeys),
                success:function(data){
                    // console.log(data);
                    if(data.result==true){
                        $(window).off('beforeunload');
                        location.href = '/basic/parameter/index'+urlkeys
                    }else{
                        _showTips(data.msg);
                        curEle.attr('disabled','false').removeClass('color-gray').addClass('bg-green');
                    }
                },
                error:function(){
                    _showTips('网络错误');
                }
            });
        }else{
            return false;
        }
    });
    $selectMap.on('click',function(){
        _proxy.dialog = $.dialog({
            url:'map',
            title:'设置坐标',
            id:'js_map',
            width:640,
            height:450
        }).show();
    });
    $(window).on('beforeunload',function(e){
        if(!O.compare(_initDate,_getData())){
            return '离开后，刚刚填写数据会丢失';
        }
    });
    var _checkCfg = {
        config:function(){
            return [{
                id:'name',
                rules:'required',
                ruleMsg:{'required':'请输入商家名称'}
            },{
                id:'linkman',
                rules:'required',
                ruleMsg:{'required':'请输入联系人'}
            },{
                id: 'linktel',
                msg: {
                    empty: '请输入手机号码',
                    error: '手机号码有误'
                },
                fun: function() {
                    var linktel = $('#linktel').val();
                    if(linktel.length === 0){
                        return 'empty';
                    }else if(!(/^1[3|4|5|7|8]\d{9}$/.test(linktel))){
                        return 'error';
                    }
                }
            },{
                id: 'address',
                rules: 'required',
                ruleMsg: {'required': '请输入地址'}
            }]
        }
    };
    var _getData = function(){
        return {
            'name':$('#name').val(),
            'linkman':$('#linkman').val(),
            'linktel':$('#linktel').val(),
            'address':$('#address').val(),
            'supaddress':$('#supaddress').val()||"",
            'longitudes':$('#longitudes').val(),
            'latitudes':$('#latitudes').val()
        };
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
    var _showTips = function(tip) {
        var d = $.tips(tip);
        setTimeout(function() {
            d.close().remove();
        }, 5000);
    };
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
    var init = function() {
        var interval = setInterval(function() {
            if(_count <= 0) {
                clearInterval(interval);
                _initDate = _getData();
                _count++;
            }
        }, 100);
    };
    init();
})
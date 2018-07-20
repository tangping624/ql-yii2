/**
 * Created by tx-03 on 2016/8/4.
 */
seajs.use(['dialog','template','form','validate'],function(){
    var _validate = new DataValid('<p class="color-red1">{errorHtml}</p>');
    var $selectMap = $('#select_map');
    var $proLon = $('#longitudes'),
        $prlLat = $('#latitudes'),
        $proAddr = $('#address');
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
    $('#submit_btn').on('click',function(){
        if(_doCheck()){
            $(this).attr('disabled','true').removeClass('bg-green').addClass('color-gray');
            var curEle = $(this);
            var data = _getData();
            // console.log(JSON.stringify(data));
            O.ajaxEx({
                type:'post',
                data:data,
                url:O.path('/basic/merchant/save-merchant'),
                success:function(data){
                    // console.log("123")
                    if(data.result==true){
                        $(window).off('beforeunload');
                        location.href = ''
                    }else{
                        _showTips(data.msg);
                        curEle.attr('disabled','false').removeClass('color-gray').addClass('bg-green');
                    }
                },
                error:function(){
                    console.log("错误")
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
            // onshow:function(){
            //
            // }
        }).show();
    });
    var _getData = function(){

        return {
            // 'id': _id,
            'name':$('#name').val(),
            'linkman':$('#linkman').val(),
            'linktel':$('#linktel').val(),
            'address':$('#address').val(),
            'supaddress':$('#supaddress').val()||"",
            'longitudes':$('#longitudes').val(),
            'latitudes':$('#latitudes').val()
        };
    };

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
    }



})
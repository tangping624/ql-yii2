define(function(require, exports, module) {
    require('form');
    require('../../../../frontend/js/lib/dialog');
    require('/frontend/js/lib/tooltips/tooltips');
    require('/frontend/3rd/laydate/laydate');
    window.Template = require('/frontend/js/lib/template');
    window.DataValid = require('/frontend/js/lib/validate');
    var _imgTempl = $('#img_templ').html(),
        _deleteTempl = $('#de_templ').html() ,
        _editTempl = $('#edit_templ').html(),
        _addTempl = $('#add_templ').html(),
        _ptetempl = $('#de_pte_templ').html(),
        _deleteUeTempl = $('#de_ue_templ').html();
//模板内容
    var  parent_id = getQueryString('parent_id')=='null'?'':getQueryString('parent_id');
    var $selectMap = $('#select_map'),//选择地图ID
        $proAddr = $('#advert_address'),//地图查询地址
        $form = $('#form'),//右侧最外层div
        $title = $('#title');//左侧广告名称id

    var _uploader = null, _validate, _initDate = null,$editName,
        _isEdit = $('#isEdit').val(),
        title = $title.html(),//左侧广告名称内容输出;
        _id;

    function showMessage(message, isNormal) {
        var parent = window.parent || window;

        parent.$.topTips({
            mode: isNormal ? 'normal' : 'warning',
            tip_text: message
        });
    }
    function getQueryString(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]); return null;
    }

    //添加编辑器
    var _ue = UE.getEditor('advert_productinof');//产品介绍编辑框引入
    _ue.ready($.proxy(function() {
        _isEdit && this.ue.setContent($('<div />').html($('#js-detail').html()).text());  
    }, {ue: _ue}));
    
    //加载地图
    try{
        var myCenter = _isEdit ? new google.maps.LatLng($('#lat').val(),$('#lng').val()) : new google.maps.LatLng(35.1923177,33.3623828);
        var mapProp = {
            center: myCenter,
            zoom: 10,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
    //在指定DOM元素中嵌入地圖  
        var map = new google.maps.Map(document.getElementById("l-map"), mapProp);  
    //加入標示點(Marker)  
        var marker = new google.maps.Marker({  
            position: myCenter //經緯度  
        });
        marker.setMap(map);
        map.addListener('click', function(e) {
            marker.setMap(null);
            placeMarkerAndPanTo(e.latLng, map);
        });
        function placeMarkerAndPanTo(latLng, map) {
            marker = new google.maps.Marker({
                position: latLng,
                map: map
            });
            map.panTo(latLng);
            if(typeof latLng.lat === "function"){
                var lat = parseFloat(latLng.lat()),
                lng = parseFloat(latLng.lng());
                $('#lng').val(lng);
                $('#lat').val(lat);
                var str = lat+','+lng;
                transAddr(str);
            }
        }
    }
    catch(err){
        $.topTips({
            mode: 'warning',
            tip_text: 'google地图加载失败，请打开vpn并刷新'
        });
    }        
    //同步更新广告名称
    var update=(function(){
        $('body').on('blur','#advert_title',function(){
            var title=$('#advert_title').val();
            if(title!=''){
                $('#title').html(title);
            }
        })
        $(document).ready(function(){
            var title=$('#advert_title').val();
            if(title!='') {
                $('#title').html(title);
            }
        })
    })();

    //事件绑定
    var _bindEvent = function() {
        $('body').on('mouseenter mouseleave', '.text-wrap .icon-edit,.text-wrap .icon-delete', function(e) {
            if(e.type == 'mouseenter') {
                var content = $(this).hasClass('icon-edit') ? '编辑名称' : '删除';
                $.pt({//同上
                    target: this,
                    width: 'auto',
                    position: 't',
                    align: 'c',
                    autoClose: false,
                    leaveClose: false,
                    content: content,
                    skin: 'pt-black'
                });
            } else {
                var pt = $('.pt');
                pt.hasClass('pt-black') && pt.hide()
            }
        });

        $('body').on('click', '.cancel-btn', function(e) {
            $('.pt').hide();
        });

        //编辑文本编辑器
        $('body').on('click', '.icon-edit', function() {
            $editName = $(this).closest('p').find('.name');
            $.pt({//固定写法
                target: this,
                width: 286,
                position: 'b',
                align: 'c',
                autoClose: false,
                leaveClose: false,
                content: _editTempl.replace('{name}', $editName.text())//将模板的内容替换为新编辑内容
            });
        });
        //确认编辑
        $('body').on('click', '.edit-btn', function(e) {
            var input = $('.edit-inp'),
                error = $('.edit-error'),
                val = $.trim(input.val());//去掉input前后空格
            if(val === '') {
                error.removeClass('hide');//显示error的内容
                return;
            } else {
                error.addClass('hide');//隐藏error的内容
            }
            $editName.text(val);
            $('.pt').hide();
        });
        $('.selcity').on('click',function(){
            var city = $('#name').val();
            if(city){
                transLat(city);
            }
        })
        //发布按钮
        $('#submit_btn').on('click', function() {
            if(_doCheck()) {
                $(this).attr("disabled","true").removeClass("bg-green").addClass("color-gray");
                var curEle = $(this);
                var datas = _getData();//获得数据
                O.ajaxEx({
                    type: 'post',
                    data: datas,
                    url: O.path('/city/city/save' + (_isEdit ? '?id=' + $(this).data('id') : '')),
                    success: function(data) {
                        if(data.result == true){
                            datas.id = data.id;
                            $(window).off('beforeunload');//$(window)当前浏览器的窗口  关闭beforeunload事件
                            parent && parent.DialogEditUser && parent.DialogEditUser.ok(datas);
                        }else{
                            curEle.removeAttr("disabled").removeClass("color-gray").addClass("bg-green");
                            _showTips(data.msg);//_showTips（）显示信息的方法
                        }
                    },
                    error: function() {
                        _showTips('网络错误');
                    }
                });
            }else{
                return false;
            }
        });
        $('#cancel').on('click',function(){
            if(top && top.DialogEditUser) {
                top.DialogEditUser.dialog.close().remove();
            }
        });
        $(window).on('beforeunload', function(e) {
            if(!O.compare(_initDate, _getData())) {
                return '离开后，刚刚填写数据会丢失';
            }
        });
    };
    //经纬度转换具体地址
    function transAddr(latLng) {
        $.ajax({
            url: 'http://maps.googleapis.com/maps/api/geocode/json?latlng='+latLng+'&sensor=true_or_false',
            success:function(data){
                if(data.status == 'OK'){
                    if(data.results[data.results.length-1].formatted_address == '中国'){
                        if(parent_id){
                            for(var i=0;i<data.results.length;i++){
                                if(data.results[i].types[0]=="political"&data.results[i].types[1]=="sublocality"&data.results[i].types[2]=="sublocality_level_1"){
                                    var address = data.results[i].address_components[0].long_name;
                                    $('#name').val(address);
                                    break;
                                }
                            }
                        }else{
                            for(var i=0;i<data.results.length;i++){
                               if(data.results[i].types[0]=="political"&data.results[i].types[1]=="sublocality"&data.results[i].types[2]=="sublocality_level_1"){
                                    var address = data.results[i].address_components[1].long_name;
                                    $('#name').val(address);
                                    break;
                                }
                            }  
                        }
                    }else{
                        for(var i=0;i<data.results.length;i++){
                            if(data.results[i].types[0]=='locality'&data.results[i].types[1]=="political"||data.results[i].types[0]=='political'&data.results[i].types[1]=="locality"){
                                var address = data.results[i].formatted_address;
                                $('#name').val(address);
                                break;
                            }else if(data.results[i].types[0]=='administrative_area_level_3'&data.results[i].types[1]=="political"||data.results[i].types[0]=='political'&data.results[i].types[1]=="administrative_area_level_2"){
                                var address = data.results[i].formatted_address;
                                $('#name').val(address);
                                break;
                            }
                        }
                    }
                }else{
                    window.alert('未能识别选择的位置');
                }
            }
        });
    }
    //具体地址转换经纬度
    function transLat(addr) {
        $.ajax({
            url: 'http://maps.googleapis.com/maps/api/geocode/json?address='+addr+'&sensor=true_or_false',
            success:function(data){
                if(data.status == 'OK'){
                    var location = data.results[0].geometry.location;
                    $('#lat').val(location.lat);
                    $('#lng').val(location.lng);
                    placeMarkerAndPanTo(data.results[0].geometry.location, map)
                }else{
                    window.alert('未能识别选择的位置');
                }
            }
        });
    }
    //获取得到的数据  提交数据用
    var _getData = function() {
        // var fullcode = getQueryString('fullcode')=='null'?'':getQueryString('fullcode'),
        // $('#fullcode').val(fullcode);
        $('#parent_id').val(parent_id);
        var seri = '', ueCon = '',advert_exts;//图片地址的上传;
        seri = O.serialize($form,2);//整个右侧DIV表单序列化
        //编辑器的内容放入对象;
        var ueConProduce = _ue.getContent();//getContent()获得内容的方法;
        seri.content = ueConProduce;
        return seri;
    }
    var _showTips = function(tip) {
        var d = $.tips(tip);//输出信息;
        setTimeout(function() {
            d.close().remove();
        }, 2000);
    }
    //验证配置、规则
    var _checkCfg = {
        config: function() {//通过config接口注入权限验证配置
            return [{
                id: 'name',
                msg: {
                    empty: '请输入城市名'
                },
                fun: function(el) {
                    if(!$('#name').val()){
                        return 'empty';
                    }   
                }
            },{
                id: 'advert_productinof',
                msg: {
                    empty: '请输入城市信息'
                },
                fun: function(el) {
                    var ueCon = _ue.getContent();
                    if(ueCon.length === 0)
                        return 'empty';
                }
            }
            ]
        }
    };
    var _doCheck = function() {//校验函数;
        if (_validate.fieldList.length === 0) {//
            _validate.addFields(_checkCfg.config());
        }

        if (!_validate.process(false)) {//
            var id = _validate.errorField.split(',')[1];//?
            $('#' + id)[0].scrollIntoView();//之后添加效果  解决抛锚定位时页面整体往上跳的问题
            return false;
        }
        return true;
    };
    _ue.ready($.proxy(function() {//proxy()返回一个新函数，并且这个函数始终保持了特定的作用域。
        _isEdit && this.ue.setContent($('<div />').html($('#js-detail').html()).text());//编辑内容赋值
        //_isEdit 判断是否为编辑页面;
        var interval = setInterval(function() {
            if(_count <= 0) {
                clearInterval(interval);
                _initDate = _getData();
                _count++;
            }
        }, 100);
    }, {ue: _ue}));//函数的作用域会被设置到这个对象上来。
   
    var _count=0;
    var _init = {
        init : function() {//初始化函数
            _bindEvent();//绑定事件调用
            _validate = new DataValid('<p class="color-red1" style="position:absolute;">{errorHtml}</p>');
        }
    };
    return _init;
});

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
    <script type="text/javascript" src="/frontend/js/lib/jquery/jquery-1.11.2.min.js"></script>
    <script type="text/javascript" src="/modules/js/public/public.js"></script>
    <script type="text/javascript" src="/frontend/js/lib/layer/layer.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAWxxGyOJkLnC6MSF67Woxn015idn1dFgo"
    ></script>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />  
    <style type="text/css">
        body, html {width: 100%;height: 100%;overflow: hidden;margin: 0;font-family:"Microsoft YaHei","Helvetica";font-size:14px;}
        #allmap {width: 100%;height: 100%;overflow: hidden;margin: 0;}
        .box{margin-bottom:5px;margin-top:15px;}
        #l-map {height: 100%;width: calc(100% - 20px);margin:10px;}
        /*.panel{border: 1px solid #C0C0C0; width: 150px; height: auto;}*/
        /*input{width:150px;}*/
        .inp{outline:none;background:transparent;vertical-align:top;-webkit-appearance:none;border:none;}
        .inp-pr{width:300px;border:1px solid #E7E7EB;margin-bottom:5px;padding:5px;margin-right:40px;}

        .btn-pr{padding: 6px 12px;text-align: center;white-space: nowrap;vertical-align: middle;cursor: pointer;border-radius: 4px;outline: none;border:1px solid #E7E6EB;background-color: #44B549;color:white;height:30px;}
        .search{display: block;width: 25px;height: 25px;position:absolute;top:16px;right:280px;cursor:pointer;}
        /*.btn-pr:hover{background-color:#E7E6EB;}*/
    </style>
</head>
<body>

<input type="hidden" id="longitude" value="0" />
<input type="hidden" id="latitude" value="0" />

<div class="box">
    <div id="r-result">
        请输入:
        <input type="text" id="suggestId" size="20" value="" class="inp inp-pr"/>
        <img class="search" src="/modules/images/search.png"></img>
        <input type="button" id="ok" value="保存" class="btn-pr" />
    </div>
    <div id="searchResultPanel" class="panel"></div>
</div>

<div id="l-map"></div>

<script type="text/javascript">
    var marker1;
    $(function () {
            var lat = pub.Geturl('lat')?pub.Geturl('lat'):'35.1923177';
            var lng = pub.Geturl('lng')?pub.Geturl('lng'):'33.3623828';
            try{
                var myCenter = new google.maps.LatLng(lat,lng);
                var geocoder = new google.maps.Geocoder;
                var mapProp = {
                    center: myCenter,
                    zoom: 10,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                //在指定DOM元素中嵌入地圖  
                var map = new google.maps.Map(  
                document.getElementById("l-map"), mapProp);
                $('.search').click(function(){
                    $.ajax({
                        url: 'http://maps.googleapis.com/maps/api/geocode/json?address='+$('#suggestId').val()+'&sensor=true_or_false',
                        success:function(data){
                            if(data.status == 'OK'){
                                var lat = data.results[0].geometry.location.lat;
                                var lng = data.results[0].geometry.location.lng;
                                $('#longitude').val(lng);
                                $('#latitude').val(lat);
                                var myCenter = new google.maps.LatLng(lat,lng);
                                var geocoder = new google.maps.Geocoder;
                                var mapProp = {
                                    center: myCenter,
                                    zoom: 10,
                                    mapTypeId: google.maps.MapTypeId.ROADMAP
                                };
                                map = new google.maps.Map(document.getElementById("l-map"), mapProp);
                                //加入標示點(Marker)  
                                marker = new google.maps.Marker({  
                                    position: myCenter //經緯度  
                                });
                                marker.setMap(map);
                                map.addListener('click', function(e) {
                                    marker.setMap(null);
                                    placeMarkerAndPanTo(e.latLng, map);
                                });
                            }else{
                                window.alert('未能识别选择的位置');
                            }
                        }
                    });
                    });
                //加入標示點(Marker)  
                var marker = new google.maps.Marker({  
                    position: myCenter //經緯度  
                });
                marker.setMap(map);
                map.addListener('click', function(e) {
                    marker.setMap(null);
                    placeMarkerAndPanTo(e.latLng, map);
                });
            }
            catch(err){
                layer.msg('google地图加载失败，请打开vpn并刷新');
            }
            //经纬度转换具体地址
            function transAddr(latLng) {

                $.ajax({
                    url: 'http://maps.googleapis.com/maps/api/geocode/json?latlng='+latLng+'&sensor=true_or_false',
                    success:function(data){
                        if(data.status == 'OK'){
                            $('#suggestId').val(data.results[0].formatted_address);
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
                            console.log(data.results[0].geometry.location);
                        }else{
                            window.alert('未能识别选择的位置');
                        }
                    }
                });
            }
            function placeMarkerAndPanTo(latLng, map) {
                marker = new google.maps.Marker({
                    position: latLng,
                    map: map
                });
                map.panTo(latLng);
                var lat = parseFloat(map.getCenter().lat()),
                    lng = parseFloat(map.getCenter().lng());
                    $('#longitude').val(lng);
                    $('#latitude').val(lat);
                var str = lat+','+lng;
                transAddr(str);
            }
            function getPos() {
                return [
                    $.trim($("#longitude").val()),
                    $.trim($("#latitude").val())
                ]
            }

            function getAddr() {
                // console.log($('#suggestId').val())
                return $.trim($('#suggestId').val())
            }
            $("#ok").on("click", function () {
                if(parent && parent.Proxy) {
                    parent.Proxy.setPos(getPos());
                    parent.Proxy.setAddr(getAddr());
                    parent.Proxy.dialog.close().remove();
                }
            });
        });
</script>
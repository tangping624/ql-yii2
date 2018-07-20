 <!DOCTYPE html>
<html lang="zh-cn">
    <head>
        <title>详细地址</title>
        <script type="text/javascript" src="/modules/js/jquery-1.9.1.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAWxxGyOJkLnC6MSF67Woxn015idn1dFgo"
    ></script>
        <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />  
    <style type="text/css">
        body{padding:0;margin:0;}
    </style> 
    <body>
        <div id="l-map" style="width:100%;height:auto;"></div>
    </body>
    <script type="text/javascript" src="/mobiend/js/lib/public.js"></script>
    <script type="text/javascript">
        $('#l-map').height($(window).height());
        var address = $.deCode($.getUrlParam('address'))?$.deCode($.getUrlParam('address')):'无详细地址信息';
        var lng = $.getUrlParam('lng')?$.getUrlParam('lng'):'33.3623828';
        var lat = $.getUrlParam('lat')?$.getUrlParam('lat'):'35.1923177';
        $(document).ready(function () {  
            if (typeof google != 'undefined') {
                var myCenter = new google.maps.LatLng(lat,lng);
                var geocoder = new google.maps.Geocoder;
                var mapProp = {
                    center: myCenter,
                    zoom: 17,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                //在指定DOM元素中嵌入地圖  
                var map = new google.maps.Map(  
                document.getElementById("l-map"), mapProp);
                var marker=new google.maps.Marker({
                    position:myCenter,
                });
                marker.setMap(map);
                var infowindow = new google.maps.InfoWindow({
                    content:address
                });
                infowindow.open(map,marker);
            }else{
                alert('无法访问，请打开vpn重新访问！');
                window.history.back();
            }
        });
    </script>
</html>
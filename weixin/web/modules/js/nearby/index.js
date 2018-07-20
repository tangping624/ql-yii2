/**
 * Created by tx-03 on 2017/3/29.
 */
define(function(require, exports, module){
    $.Jweixin = require("../../../mobiend/js/mod/weixin/jweixin_2");
    $.Template = require('../../../mobiend/js/mod/template');
    $.Scroll=require("../../../mobiend/js/mod/scroll.js");
    var activityScroll,wx_type,time=0,typepid;var latitude = 0;
    var latitude ;var longitude;
            var longitude = 0;
    module.exports={
        isWeixin: function () {
            var ua = navigator.userAgent.toLowerCase();
            if (ua.match(/micromessenger/i) == "micromessenger") {
                return true;
            } else {
                return false;
            }
        },
        googleMap:function(){
            var me = this;
            //腾讯地图定位
            var geolocation = new qq.maps.Geolocation("4ZTBZ-XK7RU-D6TV7-2F3LV-QIDNS-G4FBJ", "myapp");
            geolocation.getLocation(showPosition, showErr);
            function showPosition(position) {
                latitude = position.lat;
                longitude = position.lng;
                var str = latitude+','+longitude;
                me.transAddr(str);
                me.curClick(latitude,longitude);
            }
         
            function showErr() {
                console.log('定位失败');
            }
            //h5定位
            // if (navigator.geolocation)
            // {
            //     navigator.geolocation.getCurrentPosition(showPosition);
            // }
            // else
            // {
            //     alert("该浏览器不支持获取地理位置。");
            // }
            // function showPosition(position)
            // {
            //     latitude = position.coords.latitude; 
            //     longitude = position.coords.longitude;
            //     var str = latitude+','+longitude;
            //     me.transAddr(str);
            //     me.curClick(latitude,longitude);   
            // }
            // google定位
            // $.ajax({
            //     'type' : 'post',
            //     'url':'https://www.googleapis.com/geolocation/v1/geolocate?key=AIzaSyDcuAhA-aZv9H4hnUr7m1i1y21niIil1OY',
            //     'success' : function(data) {
            //         var lat = data.location.lat;
            //         var lng = data.location.lng;
            //         var str = lat+','+lng;
            //         me.transAddr(str);
            //         me.curClick(latitude,longitude); 
            //     }
            // });
            //微信定位
            // if(wx_type){
            //     if(WxJSSDKSign&&WxJSSDKSign!='null') {
            //         $.Jweixin.init(JSON.parse(WxJSSDKSign),function (jweixin) {
            //             //获取定位
            //             jweixin.invoke("getLocation", {
            //                 type: 'gcj02',
            //                 success: function (res) {
            //                     latitude = res.latitude; // 纬度
            //                     longitude = res.longitude; // 经度
            //                     var str = latitude+','+longitude;
            //                     me.transAddr(str);
            //                     me.curClick(latitude,longitude);
            //                 },
            //                 cancel: function (res) {
            //                     $('.loaction_text').text('当前：定位失败');
            //                     window.alert('用户拒绝授权获取地理位置');
            //                     me.curClick(latitude,longitude);
            //                 },
            //                 fail:function(res){
            //                     $('.loaction_text').text('当前：定位失败');
            //                     window.alert('获取地理位置失败，请手动刷新定位！');   
            //                     me.curClick(latitude,longitude);                             
            //                 }
            //             });
            //         },function(res) {
            //             $('.loaction_text').text('当前：定位失败');
            //             window.alert('获取地理位置失败，请手动刷新定位！');
            //             me.curClick(latitude,longitude);
            //         });
            //     }else{
            //         $('.loaction_text').text('当前：定位失败');
            //         window.alert('获取地理位置失败，请手动刷新定位！');
            //         me.curClick(latitude,longitude);
            //     }
            // }else{
                 // $('.loaction_text').text('当前：定位失败');
                 // window.alert('获取地理位置失败，请手动刷新定位！');
                 // me.curClick(latitude,longitude);
            // }
        },
        loadMore : function(type,lat,lng){
            var me=this;
            $('#loading').html('<div class="align-c" style="padding-top:40%;"><img src="/images/loading.gif" width="35"/></div>');
            $('#loading').css('padding-top','50px');
            if(activityScroll){
                activityScroll.scroll&&activityScroll.scroll.destroy();
            }
            activityScroll = new $.Scroll(function(start,reqLen,callback){
                $.ajaxEx({
                    'type' : 'get',
                    'url' : $.path("/nearby/nearby/ajax-get-seller-list"),
                    'data':'pageIndex='+(start)+'&pageSize='+(reqLen)+'&typePid='+type+'&lat='+lat+'&lng='+lng,
                    'success' : function(data) {
                        var list = data?data:'';
                        var len = list?list.length:0;
                        var listData={
                            list:list
                        };
                        listData.list.splice(10);
                        $('#loading').html('');
                        $('#loading').css('padding-top',0);
                        $('#menu').append($($.Template($('#menu_tmpl').html(), listData)));
                        if(typepid == '39de1187-4da4-ea7a-56cd-0e2f79400083'){
                            $('.bg-blue').html('旅游');
                        }else if(typepid == '39de1187-4da4-ea7a-56cd-0e2f79400080'){
                            $('.bg-blue').html('美食');
                        }else if(typepid == '39de1187-4da4-ea7a-56cd-0e2f79400079'){
                            $('.bg-blue').html('购物');
                        }
                        callback(len);  
                    }
                });
            },'LoadMore');
        },
        bindEvent:function(){
            var code;
            if(typepid == '39de1187-4da4-ea7a-56cd-0e2f79400083'){
                code = 'tour';
            }else if(typepid == '39de1187-4da4-ea7a-56cd-0e2f79400080'){
                code = 'repast';
            }else if(typepid == '39de1187-4da4-ea7a-56cd-0e2f79400079'){
                code = 'shop';
            }
            $('#menu').on('click','.menu_list',function(){
                var id= $(this).attr('data-id');
                window.location.href = $.path('/pub/seller/details?id='+id+'&appcode='+code);
            });
        },
        curClick:function(lat,lng){
            $('#menu').html('');
            typepid = $('.cur').attr('data-type');
            this.loadMore(typepid,lat,lng);
        },
         //经纬度转换具体地址
        transAddr:function(latLng) {
            // if(wx_type){
                times = setInterval(this.timeGetCity,1000);
                $.ajax({
                    url: 'http://maps.googleapis.com/maps/api/geocode/json?latlng='+latLng+'&sensor=true_or_false',
                    success:function(data){
                        clearInterval(times);
                        if(data.status == 'OK'){
                            var address = data.results[0].formatted_address;
                            address = address.replace(/邮政编码[^]+/, "");
                            $('.loaction_text').text('当前：'+address);
                        }else{
                            window.alert('未能识别位置信息');
                        }
                    },
                    error: function(e) { 
                        window.alert('请求位置信息失败');
                    } 
                });
            // }
        },
        timeGetCity:function(){
            if(time == 5){
                window.alert('未能识别位置信息');
                $('.loaction_text').text ('当前：详细地址无法识别');
            }
            time++;
        },
        init:function(){
            var me=this;
            wx_type = this.isWeixin();
            $('.reast').click(function(){
                $('.loaction_text').text('当前：定位中...');
                me.googleMap();
            });
            this.googleMap();
            $('.flex_div').click(function(){
                $('.flex_div').removeClass('cur');
                $(this).addClass('cur');
                me.curClick(latitude,longitude);
            });
            this.bindEvent();
        }
    };
});



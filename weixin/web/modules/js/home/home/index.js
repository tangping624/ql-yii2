 define(function(require, exports, module){
    $.Jweixin = require("../../../../mobiend/js/mod/weixin/jweixin_2");
    require("../../../../mobiend/js/lib/public");
    var template=require("../../../../mobiend/js/lib/art-template.js");
    $.Scroll=require("/mobiend/js/mod/scroll.js");
    var activityScroll;
    var alongitude = $('#longitude').val(),alatitude = $('#latitude').val();
    var id;var wx_type,t;var time = 0;var times;var status = 1;var page=1,page1=1,page2=1,page3=1,page4=1;
    module.exports={
        init:function(){
            $('.add_top').click(function(){
                $('.add_top').hide();
                $(window).scrollTop(0);
            });
            window.sessionStorage.removeItem("type");
            window.sessionStorage.removeItem("seach");
            $(".HLocation").on("click",function () {
                var address = $(this).find(".addr_city").html();
                window.location="/home/city/index?city="+$.enCode(address);
            });
            // wx_type = this.isWeixin();
            // var me=this;
            // setTimeout(function(){
               this.GetAddr(); 
           // },500);
            
            $(document).on('click','.prolist11 a',function(){
                window.location = "/pub/seller/details?id="+$(this).attr('data-id')+'&type_pid='+$(this).attr('data-pid')+"&appcode="+$(this).attr('data-code');
            });
            $(document).on('click','#ho a',function(){
                window.location = "/pub/seller/details?id="+$(this).attr('data-id')+'&type_pid='+$(this).attr('data-pid')+"&appcode="+$(this).attr('data-code');
            });
        },
        // isWeixin: function () {
        //     var ua = navigator.userAgent.toLowerCase();
        //     if (ua.match(/micromessenger/i) == "micromessenger") {
        //         return true;
        //     } else {
        //         return false;
        //     }
        // },
        GetAddr:function(){
            var city = $.deCode($.getUrlParam('citys'));
            var city_id = $.getUrlParam('citys_id')?$.getUrlParam('citys_id'):'';
            if(!city_id){
                getCity('');
                // if(wx_type){
                //     if(WxJSSDKSign&&WxJSSDKSign!='null') {
                //         $.Jweixin.init(JSON.parse(WxJSSDKSign),function (jweixin) {
                //             //获取定位
                //             jweixin.invoke("getLocation", {
                //                 type: 'gcj02',
                //                 success: function (res) {
                //                     var latitude = res.latitude; // 纬度
                //                     var longitude = res.longitude; // 经度
                //                     var str = latitude+','+longitude;
                //                     transAddr(str);
                //                 },
                //                 cancel: function (res) {
                //                     getCity('');
                //                     alert('用户拒绝授权获取地理位置');
                //                 },
                //                 fail:function(res){
                //                     alert('获取地理位置失败，将启用默认城市');
                //                     getCity('');
                //                 }
                //             });
                //         });
                //     }else{
                //         alert('获取地理位置失败，将启用默认城市');
                //         getCity('');
                //     }
                // }else{
                //     alert('获取地理位置失败，将启用默认城市');
                //     getCity('');
                // }
            }else{
                $('.addr_city').html(city);
                //通过city_id获取商家推荐
                $.ajaxSetup({ cache: false });
                $.ajax({
                    type: 'GET',
                    url: '/home/home/ajax-get-recommend-seller',
                    data:{cityPid:city_id},
                    success:function(data){
                        $('.ho_loading').hide();
                        var html = template('recommend_tpml', {data:data});
                        $('#ho').html(html);
                        slide();
                        touch(city_id);
                    }
                });
            }
        },
        
    };
    // function timeGetCity(){
    //     if(time == 3){
    //         status = '';
    //         alert('未能识别位置信息，将启用默认城市');
    //         getCity(''); 
    //     }
    //     time++;
    // }

    //  //经纬度转换具体地址
    // function transAddr(latLng) {
    //     times = setInterval(timeGetCity,1000);
    //     $.ajaxSetup({ cache: false });
    //     $.ajax({
    //         url: 'http://maps.googleapis.com/maps/api/geocode/json?latlng='+latLng+'&sensor=true_or_false',
    //         success:function(data){
    //             clearInterval(times);
    //             if(status){
    //                 if(data.status == 'OK'){
    //                     for(var i=0;i<data.results.length;i++){
    //                         if(data.results[i].types[0]=='locality'&data.results[i].types[1]=="political"||data.results[i].types[0]=='political'&data.results[i].types[1]=="locality"){
    //                             var address = data.results[i].formatted_address;
    //                             //通过城市获取城市id
    //                             getCity(address);
    //                             break;
    //                         }
    //                     }
    //                 }else{
    //                     alert('未能识别位置信息，将启用默认城市');
    //                     getCity('');
    //                 }
    //             }
    //         }
    //     });
    // }
    function getCity(name){
        $.ajaxSetup({ cache: false });
       $.ajax({
            url: '/home/city/ajax-loc-city',
            data:{name:name},
            success:function(data){
                id = data.id;
                $('.addr_city').html(data.name);
                $.ajax({
                    type: 'GET',
                    url: '/home/home/ajax-get-recommend-seller',
                    data:{cityPid:id},
                    success:function(data){
                        $('.ho_loading').hide();
                        if (data.length>0) {
                            var html = template('recommend_tpml', {data:data});
                            $('#ho').html(html);
                            slide();
                        }else {
                            var html = template('kong_tpml', {data:data});
                            $('#ho').html(html);
                        }
                         touch(id);
                    }
                });
            }
        }); 
    }

    function slide(){
            var ws = $('#ho>li').outerWidth();
            ws = parseFloat(ws*$('#ho>li').length+10);
            $('#ho').css('width',ws);
            var maxl = parseFloat($('.madegame').width()) - ws;
            var slider = {
                //判断设备是否支持touch事件
                touch:('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch,
                slider:document.getElementById('ho'),
                //事件
                events:{
                    slider:document.getElementById('ho'),     //this为slider对象
                    handleEvent:function(event){
                        var self = this;     //this指events对象
                        if(event.type == 'touchstart'){
                            self.start(event);
                        }else if(event.type == 'touchmove'){
                            self.move(event);
                        }else if(event.type == 'touchend'){
                            self.end(event);
                        }
                    },
                    //滑动开始
                    start:function(event){
                        var touch = event.targetTouches[0];     //touches数组对象获得屏幕上所有的touch，取第一个touch
                        startPos = {x:touch.pageX,y:touch.pageY};    //取第一个touch的坐标值
                        isScrolling = 0;   //这个参数判断是垂直滚动还是水平滚动
                        this.slider.addEventListener('touchmove',this,false);
                        this.slider.addEventListener('touchend',this,false);
                        this.sl = parseFloat(this.slider.style.left);
                    },
                    //移动
                    move:function(event){
                        //建议滑动时禁止页面滚动
                        //当屏幕有多个touch或者页面被缩放过，就不执行move操作
                        if(event.targetTouches.length > 1 || event.scale && event.scale !== 1) return;
                        var touch = event.targetTouches[0];
                        endPos = {x:touch.pageX - startPos.x,y:touch.pageY - startPos.y};
                        isScrolling = Math.abs(endPos.x) < Math.abs(endPos.y) ? 1:0;    //isScrolling为1时，表示纵向滑动，0为横向滑动
                        if(isScrolling === 0){
                            event.preventDefault();      //阻止触摸事件的默认行为，即阻止滚屏
                            // var x = parseFloat(this.slider.style.left)+endPos.x;
                            if(this.sl<0){
                                var x = this.sl + endPos.x; 
                                this.slider.style.left = x + 'px';
                            }else{
                                this.slider.style.left = endPos.x + 'px';
                            }
                        }
                    },
                    //滑动释放
                    end:function(event){
                        if(isScrolling === 0){    //当为水平滚动时
                            if(parseFloat(this.slider.style.left)>0){
                                this.slider.style.left = 0;
                            } else if(parseFloat(this.slider.style.left)<maxl&&maxl<0) {
                                this.slider.style.left = maxl + 'px';
                            }else if(parseFloat(this.slider.style.left)<maxl&&maxl>0){
                                this.slider.style.left = 0;
                            }
                        }
                        //解绑事件
                        this.slider.removeEventListener('touchmove',this,false);
                        this.slider.removeEventListener('touchend',this,false);
                    }
                },
                init:function(){
                    var self = this;     //this指slider对象
                    if(!!self.touch) self.slider.addEventListener('touchstart',self.events,false);    //addEventListener第二个参数可以传一个对象，会调用该对象的handleEvent属性
                }
            };
        slider.init();
    } 
    function fangajax(city,sTop){
        var appcode = $('.tabs>.on').attr('data-id');
        var type = $('.tabs>.on').attr('data-type');
        $.ajax({
            type: 'GET',
            url: '/home/home/ajax-get-seller-list',
            data:{
                cityPid:city,
                appcode:appcode,
                page:page,
                pagesize:10
            },
            success:function(data){
                //console.log(data)
                $('#loading').hide();
                $('#LoadMore').html('点击加载更多');
                if (data.items.length>0) {
                    var html = template('Touch_tpml', {data:data.items});
                    $('#menu').append(html);
                    if(sTop){
                        if(sTop>=t){
                            $(window).scrollTop(t);
                        }else{
                            $(window).scrollTop(sTop);
                        }
                    }
                    if(data.items.length>=10){
                        $('#LoadMore').show();
                        $('#LoadMore').off('click').click(function(){
                            $(this).html($('#loading').html());
                            page++;
                            fangajax(city);
                        });
                    }else{
                       $('#LoadMore').hide(); 
                    }
                }else {
                    if(page<=1){
                        var html = template('kong_tpml', {data:data});
                        $('#menu').html(html);
                        $('#LoadMore').hide();
                    }else{
                        $('#LoadMore').hide(); 
                    }
                }

            }
        });
    }
    function touch(city){
            var u = navigator.userAgent, app = navigator.appVersion;
            var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Linux') > -1; //android终端或者uc浏览器
            var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/);//ios端
            // TouchSlide({ 
            //     slideCell:"#listScroll",
            //     endFun:function(){
            //         $('#menu').html('');
            //         fangajax(city);
            //     }
            // });
            // var marL=$(".HLocation").width();
            // $(".Hsearbox").css({
            //     "margin-left": marL,
            // });
            // var w = $('.prolist11').width();
            // $('.prolist11').css({
            //     'width': w,
            //     'float': 'left'
            // });
            // $('.swiper-wrapper').css({
            //     'width': w*4,
            //     'transition': 'all 0.3s linear',
            // });
            fangajax(city,'');
            $('.tabs').on('click','li',function(){
                $(this).addClass('on').siblings('.on').removeClass('on');
                // $('.swiper-wrapper').css({
                // 'transform': 'translate3d(-'+ $(this).index()*w + 'px, 0, 0)'
                // });
                $('#menu').html('');
                $('#LoadMore').hide();
                page = 1;
                $('#loading').show();
                var sTop = $(window).scrollTop();
                fangajax(city,sTop);
            });
            t = $('#sidebar').offset().top;
            t = Math.ceil(t);
            $(window).on("scroll",function(){
                //ios微信会把顶部算进高度，移动端时建议判断ios和微信内核
                // if(isiOS){
                //     if($(this).scrollTop() >= t+50){
                //         $('#sidebar').css({
                //             'position':'fixed',
                //             'top':0
                //         });
                //     }else{
                //         $('#sidebar').css({
                //             'position':'absolute',
                //             'top':''
                //         });
                //     };
                // }else{
                if($(this).scrollTop() >= t){
                    $('.bg-org').hide();
                    //$('.padt1').hide();
                    $('.add_top').show();
                    $('#sidebar').css({
                        'position':'fixed',
                        'top':0
                    });
                    //$(window).scrollTop(0);
                }else{
                    $('.bg-org').show();
                    $('.add_top').hide();
                    $('#sidebar').css({
                        'position':'absolute',
                        'top':''
                    }); 
                }
                    // }
            });   
    }
    // function sellerList(city){
    //     var type_id = $('.tabs>.on').attr('data-id');
    //     var type = $('.tabs>.on').attr('data-type');
    //     $.ajax({
    //         type: 'GET',
    //         url: '/home/home/ajax-get-seller-list',
    //         data:{
    //             cityPid:city,
    //             typePid:type_id
    //         },
    //         success:function(data){
    //             var html = template('Touch_tpml', {data:data.items});
    //             $('#'+type).html(html);
    //         }
    //     });
    // }
});
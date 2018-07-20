/**
 * Created by tx-04 on 2017/4/5.
 */
define(function(require,exports,module) {
    $.Template=require('/mobiend/js/mod/template');
    $.Scroll=require("/mobiend/js/mod/scroll.js");
    var activityScroll,page=1,_typeid='',city_pid='';
    var getQueryString=function(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = location.search.substr(1).match(reg);
        if (r != null) return unescape(decodeURI(r[2])); return null;
    };
    var appcode=getQueryString('appcode');
    var id=getQueryString('id');
    var type_id=getQueryString('type_id')||'';
    var url='/pub/seller/ajax-index?id='+id+'&type_id='+type_id;
    $('.typeList li').each(function(i,v){
        if($(v).data('id')==type_id){
            $('.type span').html($(v).find('a').html());
            $('.typeList li').eq(i).addClass('cur');
        }
    });
    module.exports= {
        init: function () {
            $('.ho_loading').height($(window).height());
            this.LoadList(url);
            this.bindEvent();
            this.slide();   
            $('.hot_pic').height($('.hot_pic').width());

        },
        slide:function(){
            TouchSlide({
                slideCell:"#picScroll2",
                titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
                autoPage:true, //自动分页
                autoPlay:true,
                defaultIndex:0,
                startFun:function(){

                }

            });
            TouchSlide({
                slideCell:"#picScroll",
                titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
                autoPage:true, //自动分页
            });

        },
        LoadList:function(url){
            me=this;
            //$('#loading').html('正在加载...');
            sss();
            // activityScroll = new $.Scroll(function(start,reqLen,callback){
                function sss(){
                    $.ajaxEx({
                        url:url,
                        type:'get',
                        data:'page='+page,
                        success:function(data){
                            $('#loading').hide();
                            $('#LoadMore').html('点击加载更多');
                            if (data.items.length>0) {
                                var list = data.items;
                                var listData={data:list};
                                if(list.length>=10){
                                    $('#LoadMore').show();
                                    $('#LoadMore').off('click').click(function(){
                                        //$('.ho_loading').show();
                                        $(this).html($('#loading').html());
                                        page++;
                                        sss();
                                    });
                                }else{
                                   $('#LoadMore').hide(); 
                                }
                                $('#menu').append($.Template($('#menu_tmpl').html(),listData));
                            }else {
                                if(page<=1){
                                    $('#menu').html($.Template($('#empty_tmpl').html()));
                                    $('#LoadMore').hide(); 
                                }else{
                                    $('#LoadMore').hide(); 
                                }
                            }
                            var t = $('.padt21').offset().top;
                            var a = $('#scrollWrap').offset().top;
                            $(window).on('scroll',function(){
                                if($(this).scrollTop() >= (t-a)){
                                    $('.rankbox').css({
                                        'position':'fixed',
                                        'top':a
                                    });
                                    //$('.header_flxed').hide();
                                }else{
                                    $('.rankbox').css({
                                        'position':'absolute',
                                        'top':''
                                    });
                                   $('.header_flxed').show(); 
                                }
                            }); 
                            // callback(len);
                        }
                    });
                }
            // },'LoadMore');
            
        },
        bindEvent:function(){
            $('.idxAd1').show();
            $('.padt1').show();
            $('.con').each(function(){
                var str=$(this).html();
                str=str.replace(/<\/?[^>]*>/g,'');
                // str=str.replcae(/\s/,'');
                $(this).prev(".summary").html(str);
            });
            $('.hot_wrapper').show();
            $('.rankbox').show();
            $('.popL>li').first().addClass('cur');
            $('.popL>li').first().find('ul').show();
            $('body').on('click','.navbox .bd li',function(){
                var type_id=$(this).data('id')||'';
                location.href='/pub/seller/type-index?id='+id+'&type_id='+type_id+'&title='+$("title").html()+'&appcode='+appcode;
            });
            // var iClick=true;
            $(".rankbox .flex li").click(function(){
                that=$(this);
                $('#LoadMore').hide();
                $(this).addClass("cur").siblings().removeClass("cur");
                $(".Prank").show();
                $(".layer").children().hide();
                $(".layer").children().eq($(this).index()).show();
                // $('.scroll-box').css('transform','translate(0px, 0) scale(1) translateZ(0px)')
                // iClick&&$('.popL>li').eq(0).trigger('click');
                // iClick=false;

            });
            $('body').on('click','.poprank>li',function(){
                // $('.pop1').eq(0).hide();
                $('#loading').show();
                page=1;
                $('#menu').empty();
                var index=that.index();
                $(this).addClass("cur").siblings().removeClass("cur");
                $(".Prank").hide();
                $(".rankbox .flex li").eq($(this).closest('.conbox').index()).find("span").html($(this).find("a").html());
                $(".rankbox .flex li").eq($(this).parent().parent().index()).removeClass("cur");
                if(index==0){
                    city_id=$(this).data('id');
                    if($('.Pnearby .popR').find('.cur')){
                        $('.Pnearby .popR').find('.cur').removeClass('cur');
                        $(this).addClass('cur');
                    }
                    // if($('.Pnearby .popR').find('.cur')){
                    //     $('.Pnearby .popR').find('.cur').removeClass('cur')
                    //     $(this).addClass('cur');
                    // }
                    // city_id=$(this).data('id');

                    // if($(this).attr('data-text')=='全部'){
                    //     var city_pid=$(this).data('pid');
                    //     var city_pname=$(this).data('pname');
                    //     var url='/pub/seller/ajax-index?id='+id+'&city_pid='+city_pid;
                    //     me.LoadList(url);
                    //     $(".rankbox .flex li").eq($(this).closest('.conbox').index()).find("span").html(city_pname);
                    // }else{
                    //     var url='/pub/seller/ajax-index?city_id='+city_id+'&id='+id;
                    //     me.LoadList(url);
                    // }
                    $(this).addClass("cur").siblings().removeClass("cur");
                    $($(this).find("a").attr("href")).show().siblings(".poprank").hide();
                    city_pid=$(this).data('id');
                    var city_pname=$(this).find('a').html();
                    $(".rankbox .flex li").eq($(this).closest('.conbox').index()).find("span").html(city_pname);
                    var url='/pub/seller/ajax-index?id='+id+'&city_pid='+city_pid;
                    me.LoadList(url);
                }else if(index==1){
                    _typeid=$(this).data('id');
                    var url='/pub/seller/ajax-index?type_id='+_typeid+'&id='+id+'&city_pid='+city_pid;
                    me.LoadList(url);
                }else{
                    if($(this).is('.assess')){
                        var keyword=3;
                    }else if($(this).is('.collection')){
                        var keyword=4;
                    }
                    var url='/pub/seller/ajax-index?id='+id+'&keyword='+keyword+'&city_pid='+city_pid+'&type_id='+_typeid;
                    me.LoadList(url);
                }

            });
            $(".mask").click(function(){
                $(".Prank").hide();
                $(".flex li").removeClass("cur");

            });
            $('.rankbox').on('click','a',function(e){
                e.preventDefault();
            });

            $('.title').click(function(){
                location.href='/news/news/index?id='+id;
            });

            $('body').on('click','.prolist1 li a',function(){
                var _id=$(this).data('id');
                var type_id=$(this).data('type_pid');
                location.href='/pub/seller/details?id='+_id+'&appcode='+appcode+'&type_pid='+type_id;
            });

            $('body').on('click','.imgico',function(){
                var newsId=$(this).data('id');
                if(newsId){
                    location.href='/news/news/details?id='+newsId;
                }
            });
        }

    };
    
});


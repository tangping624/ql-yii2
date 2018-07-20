/**
 * Created by tx-04 on 2017/4/6.
 */
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
    var type_id=getQueryString('type_id');
    var title=getQueryString('title');
    $("title").html(title);
    $(".Hcon h1").html(title);
    var url='/pub/seller/ajax-index?id='+id+'&type_id='+type_id;
    $('.typeList li').each(function(i,v){
        if($(v).data('id')==type_id){
            $('.type span').html($(v).find('a').html());
            $('.typeList li').eq(i).addClass('cur');
        }
    });
    module.exports= {
        init: function () {
            $('.ho_loading').show();
            this.LoadList(url);
            this.bindEvent();
            this.slide();
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
        },
        LoadList:function(url){
            //$('.ho_loading').show();
            me=this;
            //$('#loading').html('正在加载...');
            // if(activityScroll){
            //     activityScroll.scroll&&activityScroll.scroll.destroy();
            // }
            // activityScroll = new $.Scroll(function(start,reqLen,callback){
            sss();
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
                                $("#scrollWrap").css("overflow","auto");
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
        },
        bindEvent:function(){
            $('.idxAd1').show();
            $('.rankbox').show();
            $('.popL>li').first().addClass('cur');
            $('.popL>li').first().find('ul').show();
            $('body').on('click','#picScroll li',function(){
                location.href='/pub/seller/type-list?id='+id+'&type_id='+$(this).data('id');
            });
            $(".rankbox .flex li").click(function(){
                that=$(this);
                $('#LoadMore').hide();
                $(this).addClass("cur").siblings().removeClass("cur");
                $(".Prank").show();
                $('.mask').height($('#scrollWrap').height());
                $(".layer").children().hide();
                $(".layer").children().eq($(this).index()).show();
                // activityScroll.scroll&&activityScroll.scroll.disable();
            });
            $('body').on('click','.poprank>li',function(){
                page=1;
                $('#menu').empty();
                // activityScroll.scroll&&activityScroll.scroll.enable();
                $(this).addClass("cur").siblings().removeClass("cur");
                $(".Prank").hide();
                $(".rankbox .flex li").eq($(this).closest('.conbox').index()).find("span").html($(this).find("a").html());
                $(".rankbox .flex li").eq($(this).parent().parent().index()).removeClass("cur");
                var index=that.index();
                if(index==0){
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
                // activityScroll.scroll&&activityScroll.scroll.enable();
                // $(".rankbox li").removeClass("cur");
            });
            $('.popL>li').click(function(){
                $(this).addClass("cur").siblings().removeClass("cur");
                $($(this).find("a").attr("href")).show().siblings(".poprank").hide();
                if($(this).find('ul').length){
                    $(this).find('ul').show();
                }else{
                    $(this).parent().find('.popR').hide();
                }
            });
            $('.rankbox').on('click','a',function(e){
                e.preventDefault();
            });

            $('body').on('click','.prolist1 li a',function(){
                var _id=$(this).data('id');
                location.href='/pub/seller/details?id='+_id+'&appcode='+appcode;
            });
        }

    };
});




// $(".child").on('click',function (o) {
//     $this = $(this);
//     var text = $this.attr('data-text');
//     if(text == "全部"){
//
//     }else{
//
//     }
// })
/**
 * Created by tx-04 on 2017/4/17.
 */
define(function(require,exports,module) {
    $.Template=require('/mobiend/js/mod/template');
    $.Scroll=require("/mobiend/js/mod/scroll.js");
    var activityScroll,page=1,_typeid='';
    var getQueryString=function(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = location.search.substr(1).match(reg);
        if (r != null) return unescape(decodeURI(r[2])); return null;
    }
    var appcode=getQueryString('appcode');
    var id=getQueryString('id');
    var type_id=getQueryString('type_id')||'';
    var url='/repast/repast/ajax-get-seller-list?typePid='+id+'&lng='+""+'&lat='+""+'&type_id='+type_id;
    $('.typeList li').each(function(i,v){
        if($(v).data('id')==type_id){
            $('.type span').html($(v).find('a').html());
            $('.typeList li').eq(i).addClass('cur')
        }
    })
    module.exports= {
        init: function () {
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
            me=this;
            $('#loading').html('正在加载...');
            if(activityScroll){
                activityScroll.scroll&&activityScroll.scroll.destroy();
            }
            activityScroll = new $.Scroll(function(start,reqLen,callback){
                $.ajaxEx({
                    url:url,
                    type:'get',
                    data:'pageIndex='+page,
                    success:function(data){
                        // var len = list?data.total-(start/(reqLen-1)+1)*(reqLen-1):0;
                        var len = data?data.length:'';
                        var listData={data:data}
                        $("#LoadMore").hide();
                        if (data.length) {
                            $('#menu').append($.Template($('#menu_tmpl').html(),listData));
                        } else {
                            $('#menu').html($.Template($('#empty_tmpl').html()));
                        }
                        callback(len);
                    }
                })
                page++;
            },'LoadMore')

        },
        bindEvent:function(){
            $('.idxAd1').show();
            $('.rankbox').show();
            $('.popL>li').first().addClass('cur');
            $('.popL>li').first().find('ul').show();
            // $('.hot_pic').height($('.hot_pic').width())
            $(".rankbox .flex li").click(function(){
                that=$(this);
                $(this).addClass("cur").siblings().removeClass("cur");
                $(".Prank").show();
                $('.mask').height($('#scrollWrap').height())
                $(".layer").children().hide();
                $(".layer").children().eq($(this).index()).show();
                activityScroll.scroll&&activityScroll.scroll.disable()
                $('#scrollWrap').css('overflow','inherit')
                $("body,html").css({"overflow":"auto"});
            });
            $('body').on('click','.poprank>li',function(){
                page=1;
                $('#menu').empty();
                activityScroll.scroll&&activityScroll.scroll.enable()
                var index=that.index();
                if(index==0){
                    city_id=$(this).data('id');
                    var url='/repast/repast/ajax-get-seller-list?city_id='+city_id+'&typePid='+id;
                    me.LoadList(url);
                    // if($(this).parent().parent().siblings().find('.child').hasClass('cur')){
                    //     $(this).parent().parent().siblings().find('.child').removeClass('cur');
                    // }
                    if($('.Pnearby .popR').find('.cur')){
                        $('.Pnearby .popR').find('.cur').removeClass('cur')
                        $(this).addClass('cur');
                    }
                    $("body,html").css({"overflow":"auto"});
                }else if(index==1){
                    _typeid=$(this).data('id');
                    var url='/repast/repast/ajax-get-seller-list?type_id='+_typeid+'&typePid='+id;
                    me.LoadList(url);
                    $("body,html").css({"overflow":"auto"});
                }else{
                    if($(this).is('.assess')){
                        var keyword=3;
                    }else if($(this).is('.collection')){
                        var keyword=4;
                    }
                    var url='/repast/repast/ajax-get-seller-list?id='+id+'&keyword='+keyword;
                    me.LoadList(url);
                    $("body,html").css({"overflow":"auto"});
                }
                $(this).addClass("cur").siblings().removeClass("cur");
                $(".Prank").hide();
                $(".rankbox .flex li").eq($(this).closest('.conbox').index()).find("span").html($(this).find("a").html());
                $(".rankbox .flex li").eq($(this).parent().parent().index()).removeClass("cur");

            });
            $(".mask").click(function(){
                $(".Prank").hide();
                activityScroll.scroll&&activityScroll.scroll.enable()
                // $(".rankbox li").removeClass("cur");
                $("body,html").css({"overflow":"auto"});
            });
            $('.popL>li').click(function(){
                $(this).addClass("cur").siblings().removeClass("cur");
                // $(this).parent().find('.popR').hide();
                $($(this).find("a").attr("href")).show().siblings(".poprank").hide();
                if($(this).find('ul').length){
                    $(this).find('ul').show();
                }else{
                    $(this).parent().find('.popR').hide();
                }
            });
            $('.rankbox').on('click','a',function(e){
                e.preventDefault()
            })

            $('body').on('click','.prolist1 li a',function(){
                var _id=$(this).data('id');
                location.href='/pub/seller/details?id='+_id+'&appcode='+appcode;
            })
        }

    }
})



/**
 * Created by tx-03 on 2017/3/29.
 */
define(function(require, exports, module){
    $.Template = require('../../../mobiend/js/mod/template');
    $.Scroll=require("../../../mobiend/js/mod/scroll.js");

    var activityScroll;
    var getQueryString=function(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = location.search.substr(1).match(reg);
        if (r != null) return unescape(decodeURI(r[2])); return null;
    }
    
    var id=getQueryString('id');
    var appcode=getQueryString('appcode');
    var typeid=getQueryString('type_id');
    var me;

    var list=$('.conbox').eq(1).find('.poprank li');
    for(var i=0;i<list.length;i++){
      if($(list[i]).attr('data-id')==typeid){
        $('.flex .flex_div').eq(1).find('span').html($(list[i]).find('a').html());
        // $('.flex .flex_div').eq(1).addClass("cur");
      }
    }
    $('.rankbox').show();
    $('.padt21').show();
    $('#picScroll2').show();
    module.exports={
        urlKey : '',
        loadMore : function(city_id,tag_id,keyword){
            me=this;
            // var id = $.getQuery('id')||'';
            // var keywords = $.getQuery('keywords');
            urlKey = "/house/house/ajax-house-house";
            $('#loading').html('正在加载...');
            $('#loading').css('padding-top','50px');
            city_id=city_id?city_id:'';
            tag_id=tag_id?tag_id:'';
            if(activityScroll){
                activityScroll.scroll&&activityScroll.scroll.destroy();
            }
            activityScroll = new $.Scroll(function(start,reqLen,callback){
                $.ajaxEx({
                    'type' : 'get',
                    'url' : $.path("house/house/ajax-house-list"),
                    'data':'page='+(start)+'&pageSize='+(reqLen)+'&city_id='+city_id+'&tag_id='+tag_id+'&keyword='+keyword,
                    'success' : function(data) {
                        
                        var list = data.items;
                        if(data.total>0){
                        var len = list?list.length:0;
                        // for(var i=0;i<list.length;i++){
                        //     list[i].content=list[i].content.replace(/<\/?[^>]*>/g,'');
                        // }
                        var listData={
                            list:list
                        };
                        listData.list.splice(10);
                        $('#loading').html('');
                        $('#loading').css('padding-top',0);
                        // $('.min-h').css('min-height',"15rem");
                        $('#menu').append($($.Template($('#menu_tmpl').html(), listData)));
                      }else{
                        // $('.min-h').css('min-height',"30px");
                        $("#scrollWrap").css("overflow","auto");
                        $('#menu').html($.Template($('#empty_tmpl').html()));
                      }
                        callback(len);   
                      }
                    
                });
            },'LoadMore');
        },
        bindEvent:function(){
            $('#menu').on('click','li a',function(){
                var id= $(this).attr('data-id');
                window.location.href = $.path('/pub/seller/details?id='+id+'&appcode='+appcode)
            });
            TouchSlide({
                  slideCell:"#picScroll2",
                  titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
                  autoPage:true, //自动分页

              });
             TouchSlide({
                    slideCell:"#picScroll",
                    titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
                    autoPage:true, //自动分页

                });
            $(".popR").eq(0).css("diaplay","none")
            $(".Pnearby .popL li").eq(0).addClass("cur");
            $(".hot_wrapper").css("display","block");
            var len =$(".hot_wrapper ul li").length;
            if(len==1){
               $(".hot_wrapper ul li").css("width","100%");
               $(".hot_wrapper ul li").css("height",$(".hot_wrapper ul li img").css("width"));
            }else if(len==2){
               $(".hot_wrapper ul li").css("width","49%");
               $(".hot_wrapper ul li").css("height",$(".hot_wrapper ul li img").css("width"));
               
            }else if(len==3){
               $(".hot_wrapper ul li").css("width","32.4%");
               $(".hot_wrapper ul li").css("height",$(".hot_wrapper ul li img").css("width"));
            }
            var str=$(".content").html();
            for(var i=0;i<$(".content").length;i++){
            var str=$(".content").eq(i).html(); 
            str=str.replace(/<p>/gi,'');
            str=str.replace(/<\/p>/gi,'');
            str=str.replace(/<br>/gi,'');
            str=str.replace(/<span>/gi,'');
            str=str.replace(/<\/span>/gi,'');
            str=str.replace(/<img>/gi,'');
            $(".tips").eq(i).html(str)
            }
      var index=0;
      $(".rankbox .flex li").click(function(e){
           index=$(".rankbox .flex li").index($(this));
          $(this).addClass("cur").siblings().removeClass("cur");
          $('.mask').css('height',$("#scrollWrap").height());
          $(".Prank").show();
          // $("#scrollWrap").css("overflow","auto");
          // $('.scroll-box').css('transform','translate(0px, 0) scale(1) translateZ(0px)')
          // var height=$(window).height()-[$(".layer").offset().top-$(document).scrollTop()];
          activityScroll.scroll&&activityScroll.scroll.disable();
          $(".layer").children().hide()
          $(".layer").children().eq($(this).index()).show()
          $("body,html").css({"overflow":"hidden"});
          // $("#scrollWrap").css({"height":"100%"})
      })
      $(".poprank li").click(function(){
         $(this).addClass("cur").siblings().removeClass("cur")
         $(".Prank").hide();
         // $("#scrollWrap").css("overflow","hidden");
         if(index==0){
         var city_id=$(this).attr("data-id");
         $("#menu").html("");
         $("body,html").css({"overflow":"auto"});
         activityScroll.scroll&&activityScroll.scroll.enable();
         me.loadMore(city_id,'');
         }else if(index==1){
          var tag_id=$(this).attr("data-id");
         $("#menu").html("");
         $("body,html").css({"overflow":"auto"});
         activityScroll.scroll&&activityScroll.scroll.enable();
         me.loadMore('',tag_id);
         }else{
             // var tag_id=$(this).attr("data-id");
             $("#menu").html("");
             $("body,html").css({"overflow":"auto"});
             activityScroll.scroll&&activityScroll.scroll.enable();
             if($(this).is('.assess')){
                 var keyword=3;
             }else if($(this).is('.collection')){
                 var keyword=4;
             }
             me.loadMore('','',keyword);
         }
         $(".rankbox li").eq($(this).parent().parent().index()).find("span").html($(this).find("a").html())
         $(".rankbox li").eq($(this).parent().parent().index()).removeClass("cur")
         
          // if($(this).attr('class')=='anchor cur'){
          //     location.href='Fcsubcate.html?link=Tesexm';
          // }

      })
      $(".mask").click(function(){
        $(".Prank").hide();
        // $("#scrollWrap").css("overflow","auto");
        activityScroll.scroll&&activityScroll.scroll.enable();
        $(".rankbox li").removeClass("cur")
        $("body,html").css({"overflow":"auto"});
      });
      
      $(".popL li").click(function(){
        $(this).addClass("cur").siblings().removeClass("cur");
      $($(this).find("a").attr("href")).show().siblings(".poprank").hide();
       $("body,html").css({"overflow":"auto"});
      });

      $('#allCity').click(function(e){
          page=1;
          e.stopPropagation();
          $('#menu').empty();
          $('#scrollWrap').css('overflow','hidden')
          $(".Prank").hide();
          $('.pop1').hide();
          // var url='/pub/seller/ajax-index?city_id=&id='+id;
          // me.LoadList(url);
          me.loadMore()
          $(".rankbox .flex li").eq($(this).closest('.conbox').index()).find("span").html($(this).find("a").html());
          $(".rankbox .flex li").eq($(this).parent().parent().index()).removeClass("cur");
          $("body,html").css({"overflow":"auto"});
          if($('.Pnearby .popR').find('.cur')){
              $('.Pnearby .popR').find('.cur').removeClass('cur')
              $(this).addClass('cur');
          }
      })
        //子分类下面的全部
        $('body').on('click','#allArea',function(e){
            page=1;
            $('#menu').empty();
            e.stopPropagation();
            $(".Prank").hide();
            $('.pop1').hide();
            var sel=$(this).parent().attr('id');
            $('.popL>li').each(function(){
                var href=$(this).find('a').attr('href').split('#')[1];
                if(sel==href){
                    city_pid=$(this).data('id');
                }
            })
            $(".rankbox .flex li").eq($(this).closest('.conbox').index()).find("span").html($(this).find("a").html());
            $(".rankbox .flex li").eq($(this).parent().parent().index()).removeClass("cur");
            $("body,html").css({"overflow":"auto"});
            if($('.Pnearby .popR').find('.cur')){
                $('.Pnearby .popR').find('.cur').removeClass('cur')
                $(this).addClass('cur');
            }
            me.loadMore('','','',city_pid)
        })
    // console.log($('.rankbox>ul>li'));
    $('.rankbox a').click(function(e){
           e.preventDefault();
       });
    $('.Prank').on('click','a',function(e){
           e.preventDefault()
    })
    
    

    },

        init:function(){
            this.loadMore('',typeid);
            this.bindEvent();
        }
    };
})



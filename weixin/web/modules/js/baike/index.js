/**
 * Created by tx-04 on 2017/3/29.
 */
define(function(require,exports,module) {
    $.Template=require('/mobiend/js/mod/template');
    $.Scroll=require("/mobiend/js/mod/scroll.js");
    // require("/mobiend/js/mod/app.js");
    var activityScroll,typeid='',page=1;
    var liWidth;
    module.exports= {
        init: function () {
            this.LoadList();
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
        LoadList:function(){
            me=this;
            $('#loading').html('正在加载...');
            if(activityScroll){
                activityScroll.scroll&&activityScroll.scroll.destroy();
            }
            activityScroll = new $.Scroll(function(start,reqLen,callback){
                $.ajaxEx({
                    url:'/wiki/wiki/ajax-wiki?id='+typeid,
                    type:'get',
                    data:'page='+page,
                    success:function(data){
                        $('.ho_loading').hide();
                        var list = data.items;
                        var len = list?list.length:'';
                        var listData={data:list}
                        $("#LoadMore").hide();
                        if (data.total>0) {
                            $('#menu').append($.Template($('#baikeList').html(),listData));
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
            $('.ad').show();
            $('.bknav').show();
            liWidth = $('.nav1 li').width();
            var liLen=$('.nav1 li').length;
            $('.flex').width(liWidth*liLen+3*liLen);
            $('.scrollBar').width(liWidth*liLen+8*liLen);
            $(".nav1 li").click(function(){
                page=1;
                $('#menu').empty();
                typeid=$(this).data('id');
                me.LoadList(typeid);
                $(this).addClass("cur").siblings().removeClass("cur")
                var liindex = $('.nav1 li').index(this);
                $(".nav1 s").css({'left' : liindex * (liWidth +5.4)+ liWidth/2 + 'px'});
            })
            $(".nav2 li").click(function(){
                $(this).addClass("cur").siblings().removeClass("cur")
            })
            $('body').on('click','.prolist1 li a',function(){
                var _id=$(this).data('id');
                location.href='/wiki/wiki/details?id='+_id;
            })

            // $('.ad').each(function(i,v){
            //     $(v).append($(v).next());
            // })
            // $('.ad').each(function(i,v){
            //     $(v).append($(v).next());
            //    
            // })
            // if($('.listAd').length%2!=0){
            //     var link=$('.listAd a').attr('href');
            //     $('.bd').find('ul:last-child').append('<li class="listAd" style="display: inline-block"><a href="'+link+'"><img src=""></a></li>')
            // }
        },

    }
})

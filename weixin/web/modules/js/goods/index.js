/**
 * Created by tx-04 on 2017/4/13.
 */
define(function(require,exports,module) {
    $.Template = require('/mobiend/js/mod/template');
    $.Scroll = require("/mobiend/js/mod/scroll.js");
    var activityScroll, page = 1, typeid = $('.default').data('id')||'';
    var getQueryString=function(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = location.search.substr(1).match(reg);
        if (r != null) return unescape(decodeURI(r[2])); return null;
    }
    var seller_id=getQueryString('seller_id')
    var isLogin=getQueryString('isLogin');
    module.exports = {
        init: function () {
            this.LoadList(typeid);
            this.bindEvent();
            this.slide();
        },
        slide: function () {
            TouchSlide({
                slideCell: "#picScroll2",
                titCell: ".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
                autoPage: true, //自动分页
                autoPlay: true,
                defaultIndex: 0,
            });
        },
        LoadList: function (typeid) {
            me = this;
            $('#loading').html('正在加载...');
            if (activityScroll) {
                activityScroll.scroll && activityScroll.scroll.destroy();
            }
            activityScroll = new $.Scroll(function (start, reqLen, callback) {
                $.ajax({
                    url: '/pub/goods/ajax-product-list?seller_id='+seller_id+'&type_id='+typeid,
                    type: 'get',
                    data: 'page=' + page,
                    success: function (data) {
                        $('#ho_loading').hide();
                        var list = data.items;
                        var len = list?list.length:'';
                        var listData = {data: list}
                        if (data.total > 0) {
                            $('#menuList').append($.Template($('#menu_tmpl').html(), listData));
                        } else {
                            $('.idxAd1').css('border-bottom','0')
                            $('#menuList').html($.Template($('#empty_tmpl').html()));
                        }
                        callback(len);
                    }
                })
                page++;
            }, 'LoadMore')
        },
        bindEvent: function () {
            $('#picScroll2').show();
            $('.tabs').show();
            liWidth = $('.tabs li').width();
            var liLen = $('.tabs li').length;
            $('.tabs').width(liWidth * liLen);
            $('.scrollBar').width(liWidth * liLen);
            var mySwiper = new Swiper('.swiper-container', {
                autoHeight: true,
                onSlideChangeStart: function () {
                    $(".tabs .default").removeClass('default');
                    $(".tabs li").eq(mySwiper.activeIndex).addClass('default');
                }
            });
            // $(".tabs li").on('click', function (e) {
            //     e.preventDefault();
            //     $(".tabs .default").removeClass('default');
            //     $(this).addClass('default');
            //     mySwiper.swipeTo($(this).index());
            // });
            $(".tabs li").click(function (e) {
                e.preventDefault();
            });
            $(".tabs li").click(function(){
                page=1;
                $('#menuList').empty();
                typeid=$(this).data('id')||'';
                me.LoadList(typeid);
                $(this).addClass("default").siblings().removeClass("default")
                var liindex = $('.nav1 li').index(this);
                $(".nav1 s").css({'left' : liindex * liWidth + liWidth/2 + 'px'});
            })
            $('body').on('click','#menuList li',function(){
                location.href='/pub/goods/product-details?product_id='+$(this).data('id')+'&seller_id='+seller_id;
            })
        }
    }
})
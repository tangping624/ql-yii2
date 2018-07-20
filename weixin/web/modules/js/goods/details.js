/**
 * Created by tx-04 on 2017/4/13.
 */
define(function(require,exports,module) {
    require("/mobiend/js/mod/app");
    var getQueryString=function(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = location.search.substr(1).match(reg);
        if (r != null) return unescape(decodeURI(r[2])); return null;
    }
    var isLogin=$('#cookie').val();
    var id=$('#id').val();
    var seller_id=$('#seller_id').val();
    module.exports= {
        init: function () {
            this.bindEvent();
        },
        bindEvent:function(){
            //    点赞
            $('body').on('click', '.dz', function () {
                var isDz = $(this).attr('class').indexOf('wei-dz') == -1 ? true : false;
                var me=$(this);
                if (!isLogin) {
                    $.Env.showMsg('亲，您尚未登录哦');
                    setTimeout("location.href='/me/me/login-index'", 1000);
                } else {
                    if (!isDz) {//没有点赞
                        $.ajaxEx({
                            url: '/pub/seller/praise?id=' + seller_id+'&product_id='+id + '&type=2',
                            data: '',
                            success: function (data) {
                                if (!data.result) {
                                    $.Env.showMsg(data.msg);
                                } else {
                                    me.removeClass('wei-dz').addClass('yi-dz');
                                    $('.dzNum').html(parseInt($('.dzNum').html()) + 1)
                                    $.Env.showMsg('已点赞');
                                }
                            },
                            error: function () {
                                $.Env.showMsg('网络连接不可用,请稍后重试');
                            }

                        })
                    } else {
                        $.Env.showMsg('已经点过点赞了哦');
                    }
                }
            })
            if ($('#page_content').width() > 425) {
                $('.icon').css('font-size', '36px')
            }

            // 收藏
            $('body').on('click', '.sc', function () {
                var me = $(this);
                isSc = $(this).attr('class').indexOf('wei-sc') != -1 ? false : true;
                if (!isLogin) {
                    $.Env.showMsg('亲，您尚未登录哦');
                    setTimeout("location.href='/me/me/login-index'", 1000);
                } else {
                    if (!isSc) {//没有收藏
                        $.ajaxEx({
                            url: '/pub/seller/praise?id=' + seller_id+'&product_id='+id +'&type=1',
                            data: '',
                            success: function (data) {
                                if (!data.result) {
                                    $.Env.showMsg(data.msg);
                                } else {
                                    me.removeClass('wei-sc').addClass('yi-sc');
                                    $.Env.showMsg('收藏成功');
                                }
                            },
                            error: function () {
                                $.Env.showMsg('网络连接不可用,请稍后重试');
                            }

                        })
                    } else {
                        $.ajaxEx({
                            url: '/pub/seller/cancel-goods?id=' + id + '&type=1',
                            data: '',
                            success: function (data) {
                                if (!data.result) {
                                    $.Env.showMsg(data.msg);
                                } else {
                                    me.removeClass('yi-sc').addClass('wei-sc');
                                    $.Env.showMsg('已取消收藏');
                                }
                            },
                            error: function () {
                                $.Env.showMsg('网络连接不可用,请稍后重试');
                            }

                        })
                    }
                }
            })
        }
    }


})
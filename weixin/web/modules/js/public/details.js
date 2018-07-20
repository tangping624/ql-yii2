/**
 * Created by tx-04 on 2017/4/6.
 */
define(function(require,exports,module) {
    $.jweixin=require("/mobiend/js/mod/weixin/jweixin_2");
    // require("/mobiend/js/mod/app");
    $.Template=require('/mobiend/js/mod/template');
    var id=$('#shopid').val();
    var isLogin=$('#cookie').val();
     // isLogin=isLogin?1:0;
    var getQueryString=function(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = location.search.substr(1).match(reg);
        if (r != null) return unescape(decodeURI(r[2])); return null;
    }
    var type_pid=getQueryString('type_pid');
    // var height=parseFloat($(".over>p").css("height"));
    var he=parseFloat($(".over").css("height"));
    var sum=0;
   // for(var i=0;i<$(".over>p").length;i++){
   //     var height=parseFloat($($(".over>p")[i]).css("height"));
   //     sum+=height;
   //     // console.log(height);
   // }
   // console.log(sum);
    var showMsg=function(msg) {
        $this=this;
        $('#dialog_msg_box').show();
        $('#dialog_msg_content').html(msg);
        var leFt = ($(window).width()-$('#dialog_msg_wrap').outerWidth())/2;
        $('#dialog_msg_box').css('left',leFt);
        setTimeout("$('#dialog_msg_box').hide()",2000);
    }
    if(he>200){
        // console.log($(".over"));
        $(".more").show();
         $(".over").css("height","200px");
         // $(".over").text($(".over").text().replace(/(\s)*([a-zA-Z0-9]+|\W)(\.\.\.)?$/, "..."));  
        $(".over").css("overflow","hidden");
        // $(".over").css("text-overflow","ellipsis"); 
        $(".more").click(function(){
            if(parseFloat($(".over").css("height"))==200){
                $(".over").css("overflow","auto");
                
                $(".over").css("height",(he+7)+'px');
                $(".more").css("transform","rotate(90deg);");
                $(".more").css("bottom","0px")
            }else{
                $(".over").css("overflow","hidden"); 
                $(".over").css("height",'200px');
                $(".more").css("transform","rotate(-90deg);");
                $(".more").css("bottom","6px")
            }
        })
    }else{
       $(".more").hide(); 
    }
    
    var appcode=getQueryString('appcode');
    module.exports= {
        init: function () {

            this.bindEvent();
            this.previewImg();
            this.nearby();

            
        },
        bindEvent:function(){

            if($('.about').length) {
                // $('.about').show();
                $('body').on('click', '.intro .about', function () {
                    location.href = '/pub/goods/product-index?seller_id=' + getQueryString('id') + '&appcode=' + appcode;
                })
            }
            $('body').on('click','#nearby .about',function(){
                var selelr_id=$(this).data('id');
                var type_pid=$(this).data('type_pid');
                location.href='/pub/seller/details?id='+selelr_id+'&appcode='+appcode+'&type_pid='+type_pid;
            })
            //    点赞
            $('body').on('click','.dz',function(){
                var isDz=$(this).attr('class').indexOf('wei-dz')==-1?true:false;
                var me=$(this);
                if(!isLogin){
                    showMsg('亲，您尚未登录哦');
                    setTimeout("location.href='/me/me/login-index'",1000);
                }else{
                    if(!isDz){//没有点赞

                        $.ajaxEx({
                            url:'/pub/seller/praise?id='+id+'&type=2',
                            data:'',
                            success:function(data){
                                if (!data.result) {
                                    showMsg(data.msg);
                                } else {
                                    me.removeClass('wei-dz').addClass('yi-dz');
                                    $('.dzNum').html(parseInt($('.dzNum').html())+1)
                                    showMsg('已点赞');
                                }
                            },
                            error:function(){
                                showMsg('网络连接不可用,请稍后重试');
                            }

                        })
                    } else{
                        showMsg('已经点过赞了');
                    }
                }
            })
            // $('body').on('click','.praise',function(){
            //     var is_praised=$(this).attr('data-is_praised');
            //     var me=$(this);
            //     if(!isLogin){
            //         showMsg('亲，您尚未登录哦');
            //         setTimeout("location.href='/me/me/login-index'",1000);
            //     }else{
            //         if(!is_praised){//没有点赞
            //             $.ajaxEx({
            //                 url:'/pub/seller/praise?id='+id+'&type=2',
            //                 data:'',
            //                 success:function(data){
            //                     if (!data.result) {
            //                         showMsg(data.msg);
            //                     } else {
            //                         me.attr('data-is_praised','praised')
            //                         $('.dzNum').html(parseInt($('.dzNum').html())+1)
            //                         showMsg('已点赞');
            //                     }
            //                 },
            //                 error:function(){
            //                     showMsg('网络连接不可用,请稍后重试');
            //                 }
            //
            //             })
            //         } else{
            //             showMsg('已经点过点赞了哦');
            //         }
            //     }
            // })
            // 收藏
            $('body').on('click','.sc',function(){
                var me=$(this);
                var isSc=$(this).attr('class').indexOf('wei-sc')!=-1?false:true;
                if(!isLogin){
                    showMsg('亲，您尚未登录哦');
                    setTimeout("location.href='/me/me/login-index'",1000);
                }else {
                    if (!isSc) {//没有收藏
                        $.ajaxEx({
                            url: '/pub/seller/praise?id=' + id + '&type=1',
                            data: '',
                            success: function (data) {
                                if (!data.result) {
                                    showMsg(data.msg);
                                } else {
                                    me.removeClass('wei-sc').addClass('yi-sc');
                                    showMsg('收藏成功');
                                }
                            },
                            error: function () {
                                showMsg('网络连接不可用,请稍后重试');
                            }

                        })
                    } else {
                        $.ajaxEx({
                            url: '/pub/seller/cancel?id=' + id + '&type=1',
                            data: '',
                            success: function (data) {
                                if (!data.result) {
                                    showMsg(data.msg);
                                } else {
                                    me.removeClass('yi-sc').addClass('wei-sc');
                                    showMsg('已取消收藏');
                                }
                            },
                            error: function () {
                                showMsg('网络连接不可用,请稍后重试');
                            }

                        })
                    }
                }
            })
        },
        previewImg:function(){

            var openPhotoSwipe = function() {
                var pswpElement = document.querySelectorAll('.pswp')[0];

                // build items array
                var arr=[],imgList={};
                $('.imgList img').each(function(){
                    arr.push($(this).attr('src'));
                })
                arr=arr.reverse();
                var items = [];
                for ( var i = 0; i < arr.length; i++) {
                    items.push({src:arr[i],w:964,h:750});
                    // console.log(arr[i]);
                }

                // define options (if needed)
                var options = {
                    // history & focus options are disabled on CodePen        
                    history: false,
                    focus: false,

                    showAnimationDuration: 0,
                    hideAnimationDuration: 0

                };

                var gallery = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);
                gallery.init();
            };

            // document.getElementById('btn').onclick = openPhotoSwipe;
            $('body').on('click','.imgList img',function(){
                openPhotoSwipe()
            })
        },
        nearby:function(){
            $.ajaxEx({
                url: '/pub/seller/round-merchant?id=' + id +'&type_pid='+type_pid,
                data: '',
                success: function (data) {
                    $('#loading').hide();
                    var listData={data:data};
                    var reNum=!$.isEmptyObj(data)?data.length:0;
                    $('.reNum').html(reNum);
                    $('#nearby').append($.Template($('#nearby_templ').html(),listData));
                },
                error: function () {
                    showMsg('网络连接不可用,请稍后重试');
                }

            })
        }
    }
})


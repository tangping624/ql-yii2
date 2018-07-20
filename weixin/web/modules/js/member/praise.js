define(function(require, exports, module){
    $.Scroll=require("/mobiend/js/mod/scroll.js");
    var template = require("../../../mobiend/js/lib/art-template.js");
    
    var activityScroll,dataType;
    module.exports={
        urlKey : '',
        loadMore : function(type){
            var me=this;
            urlKey = "/me/member/ajax-my-praise";
            $('#loading').html('正在加载...');
            $('#loading').css('padding-top','50px');
            if(activityScroll){
                activityScroll.scroll&&activityScroll.scroll.destroy();
            }
            activityScroll = new $.Scroll(function(start,reqLen,callback){
                $.ajaxEx({
                    'type' : 'get',
                    'url' : $.path("/me/member/ajax-my-praise"),
                    'data':'page='+start+'&pagesize='+reqLen+'&type='+type,
                    'success' : function(data) {
                        var list = data.items;
                        if(list.length>0){
                            var len = list?list.length:0;
                            var listData={
                                list:list
                            };
                            listData.list.splice(10);
                            $('#loading').html('');
                            $('#loading').css('padding-top',0);
                            if($('.noData').length != 0){
                                $('.noData').remove();
                                $('#menu').append(template('collectionList',listData));
                                dataType = $('.cur').attr('type');
                                if(dataType == '2'){
                                    $('.agent ').hide();
                                }
                            }else{
                                $('#menu').append(template('collectionList',listData));
                                dataType = $('.cur').attr('type');
                                if(dataType == '2'){
                                    $('.agent ').hide();
                                }
                            }
                            callback(len); 
                        }else{
                            if(data.page==1){
                                $('#menu').html('<p class="noData" style="text-align:center;padding-top:20px;">暂无数据!</p>');
                                $('#LoadMore').hide();
                            }
                            $('#LoadMore').html('没有更多数据了！');
                        }
                          
                    },
                    error:function(data){return data.msg;}
                });
            },'LoadMore');
        },
        bindEvent:function(){
            $(document).on('click','#menu>li',function(){
                var self = $(this);
                if($(this).find('.DeleteIcn').css('display') == 'none'){
                    if(dataType == '1'){
                        var seller_id = $(this).attr('seller_id');
                        var appcode = $(this).attr('appcode');
                        location.href = '/pub/seller/details?id='+seller_id+'&appcode='+appcode;
                    }
                    if(dataType == '2'){
                        var product_id = $(this).attr('product_id');
                        var seller_id = $(this).attr('seller_id');
                        location.href = '/pub/goods/product-details?product_id='+product_id+'&seller_id='+seller_id;
                    }
                    if(dataType == '3'){
                        var id = $(this).attr('mid');
                        location.href = '/lobby/lobby/details?id='+id;
                    }
                }else{
                    $.ajaxEx({
                            url:$.path('/me/member/delete'),
                            type:'get',
                            data:{id:self.attr('id')},
                            success:function(data){
                                if(data.result){
                                    self.remove();
                                }else{
                                    // $.Env.showMsg('删除失败！');
                                }
                            },
                            error:function(data){
                                return data.msg;
                            }
                        });
                }
                
            });
            $(".J-delete").on('click',function(){
                if($('.prolist1 .noData').length==0){
                      if($(this).html()=="删除"){
                           $(this).html("完成");
                           $(".DeleteIcn").show()
                      }else if($(this).html()=="完成"){
                          // $.Env.showMsg('删除成功！');
                          $('.cur').click();
                          $(this).html("删除");
                          $(".DeleteIcn").hide()
                      }
                }
           });
        },
        curClick:function(){
            this.loadMore($('.cur').attr('type'));
        },
        init:function(){
            var me=this;
            this.curClick();
            $('.flex_div').click(function(){
                $(".J-delete").html("删除");
                $(this).addClass("cur").siblings().removeClass("cur");
                 if($('#menu').html() == ''){
                   me.curClick(); 
                }else{
                   $('#menu').html('');
                   me.curClick();
                }
            });
            this.bindEvent();
        }
    };
})



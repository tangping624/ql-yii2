define(function(require, exports, module){
    $.Scroll=require("/mobiend/js/mod/scroll.js");
    var template = require("../../../mobiend/js/lib/art-template.js");
    
    var activityScroll,dataType;
    module.exports={
        urlKey : '',
        loadMore : function(type){
            var me=this;
            urlKey = "/me/member/ajax-my-track";
            $('#loading').html('正在加载...');
            $('#loading').css('padding-top','50px');
            if(activityScroll){
                activityScroll.scroll&&activityScroll.scroll.destroy();
            }
            activityScroll = new $.Scroll(function(start,reqLen,callback){
                $.ajaxEx({
                    'type' : 'get',
                    'url' : $.path("/me/member/ajax-my-track"),
                    'data':'page='+start+'&pagesize='+reqLen+'&type=1',
                    'success' : function(data) {
                        var list = data.items;
                        if(list.length>0){
                            var len = list?list.length+1:0;
                            var map = {},
                                        dest = [],
                                        arr = data.items;
                                        for(var i = 0; i < arr.length; i++){
                                            var ai = arr[i];
                                            if(!map[ai.created_on.substring(0,10)]){
                                                dest.push({
                                                    created_on: ai.created_on.substring(0,10),
                                                    data: [ai]
                                                });
                                                map[ai.created_on.substring(0,10)] = ai;
                                            }
                                            else{
                                                for(var j = 0; j < dest.length; j++){
                                                    var dj = dest[j];
                                                    if(dj.created_on == ai.created_on.substring(0,10)){
                                                        dj.data.push(ai);
                                                        break;
                                                    }
                                                }
                                            }
                                        };
                            var listData={
                                list:dest
                            };
                            listData.list.splice(10);
                            $('#loading').html('');
                            $('#loading').css('padding-top',0);
                            if($('.noData').length != 0){
                                $('.noData').remove(); 
                                detailFun(listData,start); 
                            }else{
                                detailFun(listData,start);
                                //console.log(dataType);
                            }
                            callback(len); 
                        }else{
                            if(data.page==1){
                                $('#menu').html('<p class="noData" style="text-align:center;padding-top:20px;">暂无足迹!</p>');
                                $('#LoadMore').hide();
                            }
                            $('#LoadMore').html('没有更多足迹了！');
                        }
                          
                    },
                    error:function(data){return data.msg;}
                });
            },'LoadMore');
            var detailFun = function(listData,start){
                $('#menu').append(template('trackList',listData));
                dataType = $('.cur').attr('type');
                if(start>1){
                    if($($('.Viewtime')[1]).html() == $($('.Viewtime')[0]).html()){
                        $($('.Viewtime')[1]).remove();
                    }
                }
                // if($('.cur').attr('type') == '2'){
                //     $('.agent ').hide();
                // }
            }
        },
        bindEvent:function(){
            $(document).on('click','.prolist1>li',function(){
                var self = $(this);
                if($(this).find('.DeleteIcn').css('display') == 'none'){
                    // if(dataType == '1'){
                        var seller_id = $(this).attr('seller_id');
                        var appcode = $(this).attr('appcode');
                        location.href = '/pub/seller/details?id='+seller_id+'&appcode='+appcode;
                    // }
                    // if(dataType == '2'){
                    //     var product_id = $(this).attr('product_id');
                    //     var seller_id = $(this).attr('seller_id');
                    //     location.href = '/pub/goods/product-details?product_id='+product_id+'&seller_id='+seller_id;
                    // }
                }else{
                    $.ajaxEx({
                            url:$.path('/me/member/delete'),
                            type:'get',
                            data:{id:$(this).attr('id')},
                            success:function(data){
                                if(data.result){
                                    if($('.prolist1 li').length == 1){
                                        $('#menu').html('');
                                    }else{
                                        self.remove();
                                    }
                                }else{
                                    // $.Env.showMsg('删除失败！');
                                    return false;
                                }
                            },
                            error:function(data){
                                return data.msg;
                            }
                        });
                }
                
                
            });
            $('.reload').on('click',function(){
                location.reload();
            });
            $(".J-delete").on('click',function(){
                if($('#menu .noData').length==0){
                      if($(this).html()=="删除"){
                           $(this).html("完成");
                           $(".DeleteIcn").show()
                      }else if($(this).html()=="完成"){
                          // $.Env.showMsg('删除成功！');
                          $('.cur').click();
                          $(this).html("删除");
                          $(".DeleteIcn").hide();
                      }
                }
           });
        },
        curClick:function(){
            this.loadMore($('.cur').attr('type'));
        },
        init:function(){
            var me=this;
            this.bindEvent();
            this.curClick();
            $('.flex_div').click(function(){
                //$(".J-delete").html("删除");
                $(this).addClass("cur").siblings().removeClass("cur");
                if($('#menu').html() == ''){
                   me.curClick(); 
               }else{
                   $('#menu').html('');
                   me.curClick();
               }
                
            });
            
        }
    };
})



define(function(require, exports, module){
    $.Template = require('../../../mobiend/js/mod/template');
    $.Scroll=require("../../../mobiend/js/mod/scroll.js");
    var activityScroll,wx_type,type;
    module.exports={
        // 分页
        loadMore : function(type,seach){
            var me=this;
            $('#loading').html('<div class="align-c" style="padding-top:40%;"><img src="/images/loading.gif" width="35"/></div>');
            $('#loading').css('padding-top','50px');
            if(activityScroll){
                activityScroll.scroll&&activityScroll.scroll.destroy();
            }
            activityScroll = new $.Scroll(function(start,reqLen,callback){
                $.ajaxEx({
                    'type' : 'get',
                    'url' : $.path("/home/home/ajax-get-search-list"),
                    'data':'page='+(start)+'&pagesize='+(reqLen)+'&type='+type+'&keywords='+seach,
                    'success' : function(data) {
                        var list = data.items?data.items:'';
                        var len = list?list.length:0;
                        var listData={
                            list:list
                        };
                        listData.list.splice(10);
                        $('#loading').html('');
                        $('#loading').css('padding-top',0);
                        // 判断选择模板
                        if(type == 1){
                            $('#menu').append($($.Template($('#product_tmpl').html(), listData)));
                        }else if(type == 2){
                            $('#menu').append($($.Template($('#shop_tmpl').html(), listData)));
                        }else if(type == 3){
                            for(var i=0;i<list.length;i++){
                                list[i].content=list[i].content.replace(/<\/?[^>]*>/g,'');
                            }
                            listData={
                                list:list
                            };
                            $('#menu').append($($.Template($('#text_tmpl').html(), listData)));
                        }
                        callback(len);  
                    }
                });
            },'LoadMore');
        },
        // 查看详情
        bindEvent:function(){
            $(document).on('click','.lists',function(){
                console.log('s');
                if(type == 1){
                    var id= $(this).attr('data-id');
                    var code= $(this).attr('data-appcode');
                    var seller_id=$(this).attr('data-sellerid');
                    window.location.href = $.path('pub/goods/product-details?product_id='+id+'&seller_id='+seller_id+'&appcode='+code);
                }else if(type == 2){
                    var id= $(this).attr('data-id');
                    var code= $(this).attr('data-appcode');
                    window.location.href = $.path('pub/seller/details?id='+id+'&appcode='+code);
                }else if(type == 3){
                    var id= $(this).attr('data-id');
                    window.location.href = $.path('lobby/lobby/details?id='+id);
                }
            });
        },
        // 点击切换
        curClick:function(seach){
            $('#menu').html('');
            $('#LoadMore').hide();
            type = $('.cur').attr('data-type');
            window.sessionStorage.type = type;
            window.sessionStorage.seach = seach;
            this.loadMore(type,seach);
        },
        init:function(){
            var me=this;
            if(window.sessionStorage.seach||window.sessionStorage.type){
                var search = window.sessionStorage.seach;
                var types = window.sessionStorage.type?window.sessionStorage.type:1;
                type = types;
                $('.flex_div').removeClass('cur');
                $($('.flex_div')[types-1]).addClass('cur');
                $('#seach').val(search);
                this.loadMore(types,search);
            }
            $('#seach').keyup(function(){
                me.curClick($('#seach').val());
                //document.activeElement.blur();
            });
            $('.flex_div').click(function(){
                $('.flex_div').removeClass('cur');
                $(this).addClass('cur');
                me.curClick($('#seach').val());
            });
            this.bindEvent();
        }
    };
});

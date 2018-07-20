/**
 * Created by tx-03 on 2017/3/29.
 */
define(function(require, exports, module){
    $.Template = require('../../../mobiend/js/mod/template');
    $.Scroll=require("../../../mobiend/js/mod/scroll.js");

    var activityScroll;
    module.exports={
        urlKey : '',
        loadMore : function(){
            var me=this;
            // var id = $.getQuery('id')||'';
            // var keywords = $.getQuery('keywords');
            urlKey = "/lobby/lobby/ajax-index";
            $('#loading').html('正在加载...');
            $('#loading').css('padding-top','50px');
            if(activityScroll){
                activityScroll.scroll&&activityScroll.scroll.destroy();
            }
            activityScroll = new $.Scroll(function(start,reqLen,callback){
                $.ajaxEx({
                    'type' : 'get',
                    'url' : $.path("lobby/lobby/ajax-index"),
                    'data':'page='+(start)+'&pagesize='+(reqLen),
                    'success' : function(data) {
                        var list = data.items;
                        var total=data.total;
                        var len = list?list.length:0;
                        for(var i=0;i<list.length;i++){
                            list[i].content=list[i].content.replace(/<\/?[^>]*>/g,'');
                        }
                        var listData={
                            list:list,
                            total:total
                        };
                        listData.list.splice(10);
                        $('#loading').html('');
                        $('#loading').css('padding-top',0);
                        $('#menu').append($($.Template($('#menu_tmpl').html(), listData)));
                        callback(len);   
                    }
                });
            },'LoadMore');
        },
        bindEvent:function(){
            $('#menu').on('click','.list',function(){
                var id= $(this).attr('data-id');
                window.location.href = $.path(api+'details?id='+id);
            });
        },

        init:function(){
            this.loadMore();
            this.bindEvent();
        }
    };
})



/**
 * Created by tx-03 on 2017/3/29.
 */
define(function(require, exports, module){
    $.Template = require('../../../mobiend/js/mod/template');
    $.Scroll=require("../../../mobiend/js/mod/scroll.js");
     function getUrlParam(name) {
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
            var r = window.location.search.substr(1).match(reg);  //匹配目标参数
            if (r != null) return unescape(r[2]); return null;
        }
    var id=getUrlParam("id");
    var activityScroll;
    module.exports={
        urlKey : '',
        loadMore : function(){
            var me=this;
            // var id = $.getQuery('id')||'';
            // var keywords = $.getQuery('keywords');
            urlKey = "/news/news/ajax-news-index";
            $('#loading').html('正在加载...');
            $('#loading').css('padding-top','50px');
            if(activityScroll){
                activityScroll.scroll&&activityScroll.scroll.destroy();
            }
            activityScroll = new $.Scroll(function(start,reqLen,callback){
                $.ajaxEx({
                    'type' : 'get',
                    'url' : $.path("/news/news/ajax-news-list"),
                    'data':'page='+(start)+'&pageSize='+(reqLen)+'&id='+id ,
                    'success' : function(data) {
                        var list = data.items;
                        var total=data.total;
                        var len = list?list.length:0;
                        for(var i=0;i<list.length;i++){
                            list[i].content=list[i].content.replace(/<\/?[^>]*>/g,'');
                            list[i].created_on=list[i].created_on.split(' ')[0];
                            
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



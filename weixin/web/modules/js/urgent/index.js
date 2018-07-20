/**
 * Created by tx-03 on 2016/8/12.
 */
define(function(require, exports, module){
    $.Template = require('../../../mobiend/js/mod/template');
    $.Scroll=require("../../../mobiend/js/mod/scroll.js");

    var activityScroll;
    module.exports={
        urlKey : '',
        loadMore : function(){
            var me=this;
             // var _page=1;
             // var _pagesize=
            // var id = $.getQuery('id')||'';
            // var keywords = $.getQuery('keywords');
            urlKey = "/urgent/urgent/ajax-index"
            $('#loading').html('正在加载...');
            $('#loading').css('padding-top','50px');
            if(activityScroll){
                activityScroll.scroll&&activityScroll.scroll.destroy();
            }
            activityScroll = new $.Scroll(function(start,reqLen,callback){
                // page++;
                $.ajaxEx({
                    'type' : 'get',
                    'url' : $.path("urgent/urgent/ajax-index"),
                    'data':'page='+(parseInt(start/10)+1)+'&pagesize='+(reqLen-1),
                    'success' : function(data) {
                        // console.log(data);

                        var list = data.items;
                        var len = list?list.length+1:0;
                        var total=data.total;
                        var listData={
                            type:1,
                            list:list,
                            total:total
                        }
                        listData.list.splice(10);
                         $('#loading').html('');
                        $('#loading').css('padding-top',0);
                        $('#menu').append($($.Template($('#menu_tmpl').html(), listData)));
                        //  this.Autosize();
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
            // $('#search').on('keydown',function(e){
            //     var e = e || window.event;
            //     if(e.keyCode == 13){
            //         e.preventDefault();
            //         var id = $.getQuery('id');
            //         var keywords = ($('input[type=search]').val());
            //         urlKey = id ? 'id='+id+'&keywords='+keywords:
            //                 'keywords='+keywords;
            //         if(keywords) {
            //             window.location.href = $.path(api+'list?'+ urlKey+ '&public_id=' + $.getQueryStr('public_id'));
            //         }
            //     }
            // })
        },
        init:function(){
            this.loadMore();
            this.bindEvent();
            // this.Autosize();
        }
    };
})



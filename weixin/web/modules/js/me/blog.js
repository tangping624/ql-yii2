/**
 * Created by tx-03 on 2017/3/29.
 */
define(function(require, exports, module){
    $.Template = require('../../../mobiend/js/mod/template');
    $.Scroll=require("../../../mobiend/js/mod/scroll.js");

    var activityScroll;var slf="";
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
                    'url' : $.path("me/member/ajax-lobby-index"),
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
            $('#menu').on('click','.list',function(e){
                var id= $(this).attr('data-id');
                slf=$(this);
                if($(e.target).attr("class")!="remove-icon"){
                
                window.location.href = $.path(api+'details?id='+id);
                 }else if($(e.target).attr("class")=="remove-icon"){
                    // alert("0000");
                    $.ajaxEx({
                    'type' : 'get',
                    'url' : $.path("me/member/delete-lobby"),
                    'data':{'id':id},
                    'success':function(data){
                       if(data.result){
                           if($('.menu li').length == 1){
                                location.reload();
                            }else{
                                slf.remove();
                            }
                        // location.reload();
                                // slf.remove();
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
            $(".reload").click(function(){
                location.reload();
            })
            $(".J-delete").click(function(){
                $(".delete-ico").show();
                if($(this).html()=="删除"){
                   $(this).html("完成");
                   $(".delete-ico").show()
                  }else if($(this).html()=="完成"){
                      // $.Env.showMsg('删除成功！');
                      // $('.cur').click();
                      $(this).html("删除");
                      $(".delete-ico").hide();
                }
            });
            // $(".delete-ico").click(function(){
            //     var id=$(this).closest("li").attr("data-id");
                
            //      // $.ajaxEx({
            //      //    'type' : 'get',
            //      //    'url' : $.path("me/member/delete-lobby"),
            //      //    'data':{id:id},
            //      //    'success':function(data){
                           
            //      //    }
            // })
        },

        init:function(){
            $('#add').click(function(){
                window.location="/me/member/lobby-add";
            });
            this.loadMore();
            this.bindEvent();
        }
    };
});



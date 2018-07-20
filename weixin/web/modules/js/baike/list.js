/**
 * Created by tx-04 on 2017/3/29.
 */
define(function(require,exports,module) {
    $.Template=require('/mobiend/js/mod/template');
    $.Scroll=require("/mobiend/js/mod/scroll.js");
    // require("/mobiend/js/mod/app.js");
    var activityScroll,typeid='',page=1;
    var liWidth;
    var getQueryString=function(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = location.search.substr(1).match(reg);
        if (r != null) return unescape(decodeURI(r[2])); return null;
    }
    var keywords=getQueryString('keywords');
    module.exports= {
        init: function () {
            this.LoadList();
            this.bindEvent();
        },
        LoadList:function(){
            me=this;
            $('#loading').html('正在加载...');
            if(activityScroll){
                activityScroll.scroll&&activityScroll.scroll.destroy();
            }
            activityScroll = new $.Scroll(function(start,reqLen,callback){
                $.ajaxEx({
                    url:'/wiki/wiki/ajax-wiki?id='+typeid,
                    type:'get',
                    data:{
                        'page':page,
                        'keywords':keywords
                    },
                    success:function(data){
                        var list = data.items;
                        var len = list?list.length:'';
                        var listData={data:list}
                        $("#LoadMore").hide();
                        if (data.total>0) {
                            $('#menu').append($.Template($('#baikeList').html(),listData));
                        } else {
                            $('#menu').html($.Template($('#empty_tmpl').html()));
                        }
                        callback(len);
                    }
                })
                page++;
            },'LoadMore')

        },
        bindEvent:function(){
            $('body').on('click','.prolist1 li a',function(){
                var _id=$(this).data('id');
                location.href='/wiki/wiki/details?id='+_id;
            })
        },

    }
})

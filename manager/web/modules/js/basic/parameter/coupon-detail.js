define(function (require, exports, module) {
	var template = require('../../../../frontend/js/lib/template');
	 require('../../../../frontend/js/plugin/grid.js');
	 var detailGrid = $('#detail_grid');
	  function getQueryString (name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);  //获取url中"?"符后的字符串并正则匹配
        var context = "";
        if (r != null)
            context = r[2];
        reg = null;
        r = null;
        return context == null || context == "" || context == "undefined" ? "" : decodeURIComponent(context);
    }
    var _id=getQueryString("id");
    var cache={

    };
    detailGrid.show();
    var load=function(queryType){
        detailGrid.find('tbody').html('<tr><td colspan="'+detailGrid.find('thead tr.on th').length+'"class="align-c" style="height:70px;">正在加载数据...</td></tr>');
        if(cache.view){
            cache.view.search();
        }else {
            cache.view = detailGrid.grid({
                idField: 'id',
                templateid: 'detail_template',
                pagesize: '10',
                emptyText: '无数据',
                method: 'get',
                queryParams: function () {
                

                },
                getReadURL : function(){
                    var strurl = "/basic/coupon/ajax-coupon-detail?id="+_id;
                    return O.path(strurl);
                },
                filter : function(model){
                  
                },
                loaded:function(data){
                    cache.total = data.total;
                }
            });
        }
    };
    load();
});
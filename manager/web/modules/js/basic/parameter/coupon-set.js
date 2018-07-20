define(function (require, exports, module) {
    var utils = require('../../../../frontend/js/lib/utils');
    var template = require('../../../../frontend/js/lib/template');
    require('../../../../frontend/js/lib/dialog');
    require('../../../../frontend/js/plugin/grid.js');
    var searchCon=$("#search_con");
    var couponGrid = $('#coupon_grid');
    var btnSearch=$('#btn_search');
    var _deletedTemp = $('#deleted_info').html();
    var cache={

    };
    var couid;
    var request=function(options){
        return O.ajaxEx(options).error(function(){
            $.topTips({mode:'warning',tip_text:'出现异常'});
        });
    };
    couponGrid.show();
    var load=function(queryType){
        couponGrid.find('tbody').html('<tr><td colspan="'+couponGrid.find('thead tr.on th').length+'"class="align-c" style="height:70px;">正在加载数据...</td></tr>');
        if(cache.view){
            cache.view.search();
        }else {
            cache.view = couponGrid.grid({
                idField: 'id',
                templateid: 'coupon_template',
                pagesize: '10',
                emptyText: '无数据',
                method: 'get',
                queryParams: function () {
                    var paramEls = searchCon.find('.form-control:visible');
                    cache.params = {};
                    paramEls.each(function(i){
                        var el = paramEls.eq(i);
                        cache.params[el.attr('name')]=el.val();
                    });

                    return $.param(cache.params);

                },
                getReadURL : function(){
                    var strurl = "/basic/coupon/ajax-coupon-list";
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
    var bindEvent=function(){
         $('body').on('click', '.js-ioscheck', function() {
            var $this = $(this),
                td = $this.closest('td'),
                checked = $this.prop('checked'),
                id = td.attr('data-id');
            var toggle = function(checked) {
                if(checked) {
                    td.addClass('edit-box').removeClass('noedit-box');
                } else {
                    td.removeClass('tedit-checked edit-box').addClass('noedit-box');
                }
           };
           toggle(checked);
           O.ajaxEx({
               type: 'get',
               data: {'id':id, 'is_enable' : checked ? '1' : '0'},
               url: O.path('/basic/coupon/change'),
               success : function(data){
                    if(data.result == false) {
                        toggle(!checked);
                        $this.prop('checked',!checked);
                        $.showTips(data.msg);
                    }else{
                        load();
                    }
                },
                error: function() {
                    toggle(!checked);
                    $this.prop('checked',!checked);
                    $.showTips('网络错误');
                }
            });
        });
        $('body').on('click', '.opt-deleted', function () {
               couid = $(this).attr("data-id");
               $.pt({
                    target: this,
                    width: 286,
                    position: 'b',
                    align: 'c',
                    autoClose: false,
                    leaveClose: false,
                    content: Template(_deletedTemp)
               });
        });
        //确认删除
        $('body').on('click', '.tips-wrap .deleted-oper', function () {
            var $this = $(this);
            if ($this.hasClass('bg-green')) {
                O.ajaxEx({
                    type: 'get',
                    url: O.path('/basic/coupon/delete-coupon&id=' + couid),
                    success: function (data) {
                        if(data.result){
                            load();
                        }
                        $('.pt').hide();
                    },
                    error: function () {
                        $('.pt').hide();
                    }
                });
           }else {
            $('.pt').hide()
           }
       });
        btnSearch.off('click').on('click',function(){
            load();
        });
    }

    var _showTips = function(tip) {
        var d = $.tips(tip);
        setTimeout(function() {
            d.close().remove();
        }, 5000);
    };
    load();
    bindEvent();
})
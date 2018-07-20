/**
 * Created by tx-02 on 2016/10/9.
 */
define(function (require, exports, module) {
    var utils = require('../../../../frontend/js/lib/utils');
    var template = require('../../../../frontend/js/lib/template');
    require('../../../../frontend/js/lib/dialog');
    // require('../../../../frontend/js/plugin/form.js');
    require('../../../../frontend/js/plugin/grid.js');
    var goodGrid = $('#goods_grid'),
        typeBox = $('#type_box'),
        searchCon = $('#search_con'),
        btnSearch = $('#btn_search'),
        _deletedTemp = $('#deleted_info').html();
    // var $editBox = $('.js-edit-box');
    var curid;
    var cache = {
        type: typeBox.find('.on').data('type')
    };
    var request = function (options) {
        return O.ajaxEx(options).error(function () {
            $.topTips({mode: 'warning', tip_text: '出现异常'});
        });
    };
    var getQueryString=function(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = location.search.substr(1).match(reg);
        if (r != null) return unescape(decodeURI(r[2])); return null;
    }
    var appcode=getQueryString('app_code');
    goodGrid.show();
    var load=function(queryType){
        goodGrid.find('tbody').html('<tr><td colspan="' + goodGrid.find('thead tr.on th').length + '" class="align-c" style="height:70px;">正在加载数据...</td></tr>');
        if(cache.view){
            cache.view.search();
        }else{
            cache.view=goodGrid.grid({
                idField : 'id',
                templateid : 'grid_template',
                pagesize : '10',
                emptyText : '无数据',
                method:'get',
                queryParams : function(){
                    cache.params={};
                    cache.params['keywords']=$('input[name=keywords]').val();
                    cache.params['app_code']=appcode;
                    return $.param(cache.params);
                },
                getReadURL : function(){
                    var strurl = "/shop/shop/ajax-shop-list";
                    return O.path(strurl);
                },
                filter : function(model){
                    model.set('type',cache.type);
                },
                loaded:function(data){
                    // console.log(data);

                    cache.total = data.total;
                }
            });
        }
    };
    $(".table>tbody>tr>td").css("vertical","center");
    var bindEvent=function() {
        $('body').on('click','#addType',function(){
            location.href='/shop/shop/add?app_code='+appcode
        })
        $('body').on('click','#editType',function(){
            location.href='/shop/shop/add?app_code='+appcode+'&id='+$(this).data('id');
        })
        $('body').on('keydown','input',function(e){
            keywords=cache.params['keywords']
            if(e.keyCode==13){
                load(keywords);
            }
        })
        $('body').on('click', '.opt-deleted', function () {
            curid = $(this).attr("data-id");
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
        $('body').on('click', '.tips-wrap .deleted-oper', function () {
            var $this = $(this);
            if ($this.hasClass('bg-green')) {
                O.ajaxEx({
                    type: 'get',
                    url: O.path('/shop/shop/deleted?id=' + curid),
                    success: function (data) {
                        if (data.result) {
                            load();
                            $('.pt').hide();
                        }
                    },
                    error: function () {
                        $('.pt').hide();
                    }
                });
            } else {
                $('.pt').hide();
            }
        });

        var _showTips = function (tip) {
            var d = $.tips(tip);
            setTimeout(function () {
                d.close().remove();
            }, 5000);
        };
    };
    btnSearch.off('click').on('click',function(){
        load();
    });
    var _errorCallback = function() {
        var d = $.tips('网络错误');
        setTimeout(function() {
            d.close().remove();
        }, 2000);
    };
    bindEvent();
    load();
});
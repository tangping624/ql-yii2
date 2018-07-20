define(function(require,exports,module) {
    require('/frontend/js/lib/dialog');
    require('/frontend/js/lib/tooltips/tooltips');
    require('/frontend/js/plugin/grid');
    window.Template = require('/frontend/js/lib/template');
    var _deletedTemp = $('#de_templ').html();
    var curid;
    var typeGrid=$('#type_grid');
    var typeTempl=$('#type_templ').html(),me;
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
    var cache={
        type:''
    }
    var load=function(queryType){
        typeGrid.find('tbody').html('<tr><td colspan="' + typeGrid.find('thead tr.on th').length + '" class="align-c" style="height:70px;">正在加载数据...</td></tr>');
        if(cache.view){
            cache.view.search();
        }else{
            cache.view=typeGrid.grid({
                idField : 'id',
                templateid : 'type_templ',
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
                    var strurl = "/shop/type/ajax-type-list";
                    return O.path(strurl);
                },
                filter : function(model){
                    model.set('type',cache.type);
                },
                loaded:function(data){
                   

                    cache.total = data.total;
                }
            });
        }
    };
    var _bindEvent = function () {
        // 删除
        $('body').on('click', '.opt-deleted', function () {
            me = $(this);
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

        // 删除
        $('body').on('click', '.tips-wrap .deleted-oper', function () {
            O.ajaxEx({
                data: {id:me.data('id')},
                url: O.path('/shop/type/deleted'),
                success: function (data) {
                    $('.pt').hide();
                    if (data.result) {
                        load();
                        showMessage('删除成功','isNormal');
                    } else {
                        showMessage(data.msg);
                    }
                },
                error: function () {
                    $('.pt').hide();
                }
            });
        });
        $('body').on('click','.cancel-btn',function(){
            $('.pt').hide();
        })
        $('body').on('click','#addType',function(){
            location.href='/shop/type/add?app_code='+appcode;
        })
        $('body').on('click','#editType',function(){
            location.href='/shop/type/add?app_code='+appcode+'&id='+$(this).data('id');
        })
        $("#btn_search").click(function(){
            load();
        })
        $('body').on('keydown','input',function(e){
            keywords=cache.params['keywords']
            if(e.keyCode==13){
                load(keywords);
            }
        })
    };
    var _errorCallback = function () {
        var d = $.tips('网络错误');
        setTimeout(function () {
            d.close().remove();
        }, 2000);
    };
    var showMessage=function(message, isNormal) {
        var parent = window.parent || window;
        parent.$.topTips({
            mode: isNormal ? 'normal' : 'warning',
            tip_text: message
        });
    }
    var _init = {
        init: function () {
            load();
            _bindEvent();

        }
    }
    return _init;


})

define(function(require,exports,module) {
    require('/frontend/js/lib/dialog');
    require('/frontend/js/lib/tooltips/tooltips');
    require('/frontend/js/plugin/grid');
    window.Template = require('/frontend/js/lib/template');
    var advertGrid=$('#tour_grid'),
        _deletedTemp = $('#deleted_info').html();

    var curid;
    var cache={};
    var request=function(options){
        return O.ajaxEx(options).error(function(){
            $.topTips({mode:'warning',tip_text:'出现异常'});
        });
    };
    advertGrid.show();
    var load=function(queryType){
        advertGrid.find('tbody').html('<tr style="height:70px;"><td colspan="' + advertGrid.find('thead tr.on th').length + '" class="align-c .empty-td" style="height:70px;border-bottom:none;position:relative;top:40%;left:40%;">正在加载数据...</td></tr>'); //字符串的拼接
        if(cache.view){
             cache.view.search();
        }else{
            cache.view=advertGrid.grid({
                idField : 'id',
                templateid : 'grid_template',
                pagesize : 10,
                emptyText : '无数据',
                method:'get',
                queryParams : function(){
                    cache.params={};
                    cache.params['keywords']=$('input[name=keywords]').val();
                    return $.param(cache.params);
                 },
                getReadURL : function(){
                    var strurl = "/baike/manage/ajax-index";
                    return O.path(strurl);
                },
                // sortEvent : function(){
                //     //detailGrid.hide();
                // },
                // filter : function(model){
                //     model.set('type',cache.type);
                // },
                loaded:function(data){
                    cache.total = data.total;
                }
            });
        }
    };

    var _errorCallback = function() {
        var d = $.tips('网络错误');
        setTimeout(function() {
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
    var bindEvent=function() {
        $('body').on('click','#btn_search',function(){
            keywords=cache.params['keywords']
            load(keywords);
        })
        $('body').on('keydown','.searchinput',function(e){
            keywords=cache.params['keywords']
            if(e.keyCode==13){
                load(keywords);
            }
        })
        //删除
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
        // 删除确认
        $('body').on('click', '.tips-wrap .deleted-oper', function () {
            var $this = $(this);
            var url = '',
                classN = this.className,
                data = {},
                type = 'get', flag, valArr;
            if (classN.indexOf('deleted-oper')) {
                url = flag = 'deleted';
                data = {
                    id: curid
                };
            } else {
                return;

            }
            if ($this.hasClass('bg-green')) {
                // console.log(flag);  //删除 发起ajax
                O.ajaxEx({
                    data: data,
                    type: type,
                    url: O.path('/baike/manage/delete'),
                    success: function () {
                        if (flag == 'deleted') {
                            showMessage("删除成功","isNormal")
                            load();  //数据重新加载
                        }
                        $('.pt').hide();
                    },
                    error: function () {
                        $('.pt').hide();
                        _errorCallback();
                    }
                });
            } else {
                $('.pt').hide();
            }
        });
    }
    var _init={
        init:function(){
            bindEvent()
            load();

        }
    }
    return _init;
    

});

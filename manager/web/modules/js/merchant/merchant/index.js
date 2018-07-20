seajs.use(['utils','dialog','template','grid','form'],function(utils){
    var shopGrid=$('#shop_grid'),
        typeBox=$('#type_box'),
        searchCon=$('#search_con'),
        btnSearch=$('#btn_search'),
        _deletedTemp = $('#deleted_info').html();
    var  $editBox = $('.js-edit-box');
    var curid;
    var cache={
        type:typeBox.find('.on').data('type')
    };
    var request=function(options){
        return O.ajaxEx(options).error(function(){
            $.topTips({mode:'warning',tip_text:'出现异常'});
        });
    };
    shopGrid.show();
    var load=function(queryType){
        shopGrid.find('tbody').html('<tr><td colspan="' + shopGrid.find('thead tr.on th').length + '" class="align-c" style="height:70px;">正在加载数据...</td></tr>');
        if(cache.view){
            cache.view.search();
        }else{
            cache.view=shopGrid.grid({
                idField : 'id',
                templateid : 'grid_template',
                pagesize : '10',
                emptyText : '无数据',
                method:'get',
                queryParams : function(){
                    cache.params={};
                    cache.params['name']=$('input[name=keywords]').val();
                    return $.param(cache.params);
                },
                getReadURL : function(){
                    var strurl = "/merchant/merchant/ajax-seller";
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
   
        var clipboard = new Clipboard('.copy');
            clipboard.on('success', function (e) {
                $.topTips({tip_text:'复制成功'});
            });
            clipboard.on('error', function (e) {

            });
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
            data: {'id':id, 'is_recommend' : checked ? '1' : '0'},
            url: O.path('/merchant/merchant/change'),
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
    //确认删除
    $('body').on('click', '.tips-wrap .deleted-oper', function () {
        var $this = $(this);
        if ($this.hasClass('bg-green')) {
                O.ajaxEx({
                    type: 'get',
                    url: O.path('/merchant/merchant/deleted?id=' + curid),
                    success: function (data) {
                        if(data.result){
                            showMessage('删除成功','isNormal');
                            var type = cache.type;
                            load(type);
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
    var _showTips = function(tip) {
        var d = $.tips(tip);
        setTimeout(function() {
            d.close().remove();
        }, 5000);
    };
    var showMessage=function(message, isNormal) {
        var parent = window.parent || window;
        parent.$.topTips({
            mode: isNormal ? 'normal' : 'warning',
            tip_text: message
        });
    }
    load();
})
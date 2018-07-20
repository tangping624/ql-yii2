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
    var load=function(queryType){
        O.ajaxEx({
            url: O.path('/baike/bai-ke/ajax-index'),
            type: 'get',
            success:function(data){
                if(!$.isEmptyObject(data)){
                    var listData={data:data}
                    typeGrid.find('tbody').html(Template(typeTempl,listData));
                }else{
                    typeGrid.find('tbody').html('<td colspan="7" class="empty-td align-c">无数据</td>');
                }

            },
            error:function () {

            }
        })
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
                url: O.path('/baike/bai-ke/delete'),
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

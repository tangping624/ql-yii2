seajs.use(['utils','dialog','template','grid','form','laydate'], function (utils) {
    var advertGrid=$('#advert_grid'),
        
        //搜索联系人，商家点话  日期
        
        // advertClassify=$('#advert_classify'),
        _deletedTemp = $('#deleted_info').html(),
        _redisabledTemp = $('#redisabled_info').html(),
        _disabledTemp = $('#disabled_info').html();
    var $editBox = $('.js-edit-box');//编辑td

    var curid;
    var cache={
        // type:typeBox.find('.on').data('type')  //广告广告位数据
    };

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
                 },
                getReadURL : function(){
                    var strurl = "/advertise/advert/ajax-adverts";
                    return O.path(strurl);
                },
                loaded:function(data){
                   
                    cache.total = data.total;
                }
            });
        }
    };
//滑动按钮
    $('body').on('click', '.js-ioscheck', function() {
        var $this = $(this),
            td = $this.closest('td'),
            checked = $this.prop('checked'),
            id = td.attr('data-id');
        var toggle = function(checked) {
            if(checked) {
                td.addClass('edit-box').removeClass('noedit-box');
                //td.next('td').find('a:last').css('display','none');
                //td.prev('td').html('已上架');
            } else {
                td.removeClass('tedit-checked edit-box').addClass('noedit-box');
            }
        };
        toggle(checked);
        O.ajaxEx({
            type: 'get',
            data: {'id':id, 'is_shelves' : checked ? '1' : '0'},
            url: O.path('/advertise/advert/change'),
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


    var _errorCallback = function() {
        var d = $.tips('网络错误');
        setTimeout(function() {
            d.close().remove();
        }, 2000);
    };
    //删除
    $('body').on('click', '.opt-deleted', function() {
        curid= $(this).attr("data-id");
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
    $('body').on('click', '.tips-wrap .deleted-oper', function() {
        var $this = $(this);
        var url = '',
            classN = this.className,
            data = {},
            type = 'get',flag,valArr;
        if(classN.indexOf('deleted-oper')){
            url = flag = 'deleted';
            data = {
                id: curid
            };
        }else{
            return;
    
        }
        if($this.hasClass('bg-green')){  //删除 发起ajax
            O.ajaxEx({
                data: data,
                type: type,
                url: O.path('/advertise/advert/deleted'),
                success: function() {
                    if(flag == 'deleted') {
                        showMessage('删除成功','isNormal');
                        // type=cache.type;
                        load();  //数据重新加载
                    }
                    $('.pt').hide();
                },
                error: function() {
                    $('.pt').hide();
                    _errorCallback();
                }
            });
        } else {
            $('.pt').hide();
        }
    });
    load();
    function showMessage(message, isNormal) {
        var parent = window.parent || window;
        parent.$.topTips({
            mode: isNormal ? 'normal' : 'warning',
            tip_text: message
        });
    }

});

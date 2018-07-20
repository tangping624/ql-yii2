seajs.use(['utils','dialog','template','grid','form','laydate'], function (utils) {
    var advertGrid=$('#tour_grid'),
        _deletedTemp = $('#deleted_info').html();
        // _redisabledTemp = $('#redisabled_info').html(),
        // _disabledTemp = $('#disabled_info').html();
    // var $editBox = $('.js-edit-box');//编辑td

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
                    cache.params={};
                    cache.params['keywords']=$('input[name=keywords]').val();
                    return $.param(cache.params);
                },
                getReadURL : function(){
                    var strurl = "/lobby/lobby/ajax-index";
                    return O.path(strurl);
                },
                loaded:function(data){
                    // console.log(data);
                    for(var i=1; i<=data.items.length;i++){
                        $('.content'+i).html(data.items[i-1].content.replace(/<\/?[^>]*>/g,''));
                    }
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
    $('#btn_search').click(function(){
        load();
    })
    $('#name').keyup(function(e){
        if(e.keyCode==13) load();
    })
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
                url: O.path('/lobby/lobby/deleted'),
                success: function() {
                    if(flag == 'deleted') {
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
});

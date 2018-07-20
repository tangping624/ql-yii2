seajs.use(['utils','dialog','template','grid','form','laydate'], function (utils) {
    var emergenGrid=$('#emergency_grid'),
        _deletedTemp = $('#deleted_info').html(),
        searchCon=$("#search_con"),
        btnSearch=$("#btn_search");
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
    emergenGrid.show();
    var load=function(queryType){
        emergenGrid.find('tbody').html('<tr style="height:70px;"><td colspan="' + emergenGrid.find('thead tr.on th').length + '" class="align-c .empty-td" style="height:70px;border-bottom:none;position:relative;top:40%;left:40%;">正在加载数据...</td></tr>'); //字符串的拼接
        if(cache.view){
             cache.view.search();
        }else{
            cache.view=emergenGrid.grid({
                idField : 'id',
                templateid : 'grid_template',
                pagesize : 10,
                emptyText : '无数据',
                method:'get',
                queryParams : function(){
                    var paramEls=searchCon.find('.form-control:visible');  //商家联系人 /日期
                    cache.params={};
                    cache.params['keywords']=$('input[name=keywords]').val();
                //     if (cache.type=='advert'){
                //         cache.params['status']='';
                //     }
                //     else if(cache.type=='all'){         //全部
                //         cache.params['status']='';
                //     }else if (cache.type=='throwing'){      //投放
                //         cache.params['status']='yes';
                //     }else if(cache.type=='notthrow'){        //未投放
                //         cache.params['status']='no';
                //     }
                //     else if(cache.type=='nothrow'){          //已过期
                //         cache.params['status']='due';
                //     }
                //     paramEls.each(function(i){
                //         var el=paramEls.eq(i);
                //         cache.params[el.attr('name')]=el.val();
                //     });
                     return $.param(cache.params);
                 },
                getReadURL : function(){
                    var strurl = "/baike/emergency/ajax-index";
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
//滑动按钮
    // $('body').on('click', '.js-ioscheck', function() {
    //     var $this = $(this),
    //         td = $this.closest('td'),
    //         checked = $this.prop('checked'),
    //         id = td.attr('data-id');
    //     var toggle = function(checked) {
    //         if(checked) {
    //             td.addClass('edit-box').removeClass('noedit-box');
    //             //td.next('td').find('a:last').css('display','none');
    //             //td.prev('td').html('已上架');
    //         } else {
    //             td.removeClass('tedit-checked edit-box').addClass('noedit-box');
    //         }
    //     };
    //     toggle(checked);
    //     O.ajaxEx({
    //         type: 'get',
    //         data: {'id':id, 'is_shelves' : checked ? '1' : '0'},
    //         url: O.path('/advertise/advert/change'),
    //         success : function(data){
    //             if(data.result == false) {
    //                 toggle(!checked);
    //                 $this.prop('checked',!checked);
    //                 $.showTips(data.msg);
    //             }else{
    //               load();
    //             }
    //         },
    //         error: function() {
    //             toggle(!checked);
    //             $this.prop('checked',!checked);
    //             $.showTips('网络错误');
    //         }
    //     });
    // });

    // 取消编辑
    // var _unChecked = function() {
    //     $editBox.removeClass('tedit-checked');
    //     $('.pt').hide();
    // };

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
                url: O.path('/baike/emergency/delete'),
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
    // typeBox.off('click').on('click','li',function(){
    //     var $this=$(this),
    //         type=$this.data('type');
    //         //headSelector='thead>tr.'+type;
    //     if(!$this.hasClass('on')){
    //         $this.addClass('on').siblings().removeClass('on');
    //         cache.type=type;
    //         load(type);
    //     }
    // });
    // typeBox.off('click').on('click','li',function(){
    //     var $this=$(this),
    //         type=$this.data('type'),
    //         headSelector='thead>tr.'+type;
    //     if(!$this.hasClass('on')){
    //         $this.addClass('on').siblings().removeClass('on');
    //         cache.type=type;
    //         load(type);
    //     }
    // });

    //上架下架点击
    // advertClassify.off('click').on('click','li',function(){
    //     var $this=$(this),
    //         type=$this.data('type'),
    //         headSelector='thead>tr.'+type;
    //     if(!$this.hasClass('classify-color')){
    //         $this.addClass('classify-color').siblings().removeClass('classify-color');
    //         cache.type=type;
    //         load(type);
    //     }
    // });


    // var option={
    //     start:{
    //         elem: '#regstar',
    //         format: 'YYYY-MM-DD',
    //         istime: true,
    //         isclear: true,
    //         choose: function(datas){
    //             option.end.start = datas;
    //             option.end.min=datas;
    //         }
    //     },
    //     end:{
    //         elem: '#regend',
    //         format: 'YYYY-MM-DD',
    //         istime: true,
    //         isclear: true,
    //         choose: function(datas){
    //             option.start.max=datas;
    //         }
    //     }
    // };
    // laydate(option.start);
    // laydate(option.end);
    //  $('body').on('click','#laydate_clear',function(){
    //     if(endTime.val()==''){
    //         delete option.start.max;
    //     }
    // });

    // advertGrid.off('click').on('click','tbody tr:visible,.opt-add',function(){   //请先搜索订单
    //     var $this=$(this),
    //         id=$this.find('.id').data('id')||$this.data('id'),
    //         model=cache.view.grid.get(id);
    //     if(model){
    //         var pId=model.get('id');
    //         if($this.hasClass('opt-add')){
    //             dialog([{id:pId}],id,pId,function(point){
    //                 var val=(model.get('integral_total')-'')+point;
    //                 model.set('integral_total',val%1?val.toFixed(2):val);
    //             });
    //         }
    //         $this.addClass('on').siblings().removeClass('on');
    //         id&&detail(id,pId);
    //     }
    // });

    // searchCon.off('keyup').on('keyup','.form-control',function(e){
    //     if(e.keyCode==13){
    //         load();
    //     }
    // });
    load();
    btnSearch.off('click').on('click',function(){
        load();
    });

});

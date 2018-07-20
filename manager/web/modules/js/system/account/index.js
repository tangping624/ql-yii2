 
define(function (require, exports, module) {
    require('/modules/js/public/plugin/userListOpera.js'); 
    require('/modules/js/public/plugin/topTips.js');
    require('/modules/js/public/plugin/confirm.js');
    require('/frontend/js/plugin/grid.js');
    require('/frontend/js/lib/template.js');
    require('/frontend/js/lib/overall.js');
    require('dialog.js');
    require('/frontend/js/lib/dialog');
    var dialogPlus = require('/frontend/js/lib/artDialog/src/dialog-plus.js'); 
    var selectApp = require('/modules/js/public/plugin/selectApp.js');
    var accountGrid=$('#account_grid'),  btnSearch=$('#btn_search'),typeBox=$('#type_box') ,searchCon=$('#search_con'); 
    var _deletedTemp= $('#deleted_info').html();  
    var cache={
        type:typeBox.find('.on').data('type')
    };

    var request=function(options){
        return O.ajaxEx(options).error(function(){
            $.topTips({mode:'warning',tip_text:'出现异常'});
        });
    }; 
      accountGrid.show();
    var load=function(queryType){   
        accountGrid.find('tbody').html('<tr><td colspan="' + accountGrid.find('thead tr.on th').length + '" class="align-c" style="height:70px;">正在加载数据...</td></tr>');
       if(cache.view){
            cache.view.search();
        }else{ 
         cache.view=accountGrid.grid({
            idField : 'id',
            templateid : 'account_template',
            pagesize :'10',
            emptyText : '无数据', 
            method:'get', 
            queryParams : function(){
                var paramEls=searchCon.find('.form-control:visible'); 
                cache.params={}; 
                paramEls.each(function(i){
                    var el=paramEls.eq(i);
                    cache.params[el.attr('name')]=el.val();
                });
                return $.param(cache.params);
            },
            getReadURL : function(){
                var strurl = "/system/account/account-list"; 
                return O.path(strurl);
            },
            sortEvent : function(){
                //detailGrid.hide();
            },
            filter : function(model){
//                model.set('type',cache.type);
                var strPackageType='经济型';
                var iPackageType = parseInt(model.get('package_type'),10);
                if(iPackageType==1){
                    strPackageType='舒适型';
                }else if(iPackageType==2){
                    strPackageType='豪华型';
                }
                model.set('package_type',strPackageType); 
            },
            loaded:function(data){
                cache.total = data.total; 
//                $(".opt-redisabled").filter("[data-isdisabled='1']").show();
//                $(".opt-disabled").filter("[data-isdisabled='0']").show();
 
            }, 
            rowClick: function (model) {
//                accountGrid_RowClick(model);
            },
            rowCheckEvent: function (model) {
//                curSelectedUser = accountGrid.getSelecteds();
//                accountGrid_CheckEvent(model);
            }
        }); 
    }
    }; 
    
    /**
     * 其他绑定事件
     */
    function initBindEvents() {
         // 删除 
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
        // 编辑 
        $('body').on('click', '.tips-wrap .deleted-oper', function() {
            var $this = $(this); 
            var url = '',classN = this.className,data = {}, type = 'get',flag;  
            if(classN.indexOf('deleted-oper')){ 
                 url = flag = 'remove-account'; 
                 data = { 
                     id: curid
                 }; 
            }else{return;
             } 
            if($this.hasClass('bg-green')){
                O.ajaxEx({
                    data: data,
                    type: type,
                    url: O.path('/system/account/'+url),
                    success: function() { 
                        if(flag == 'remove-account') { 
                             type=cache.type; 
                             load(type);
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
         $("body").on('click',".opt-addapp", function () {
            curid= $(this).attr("data-id");
//            var apps = $(".ps-text");
            var appCodes = new Array();
//            for (var i = 0; i < apps.length; i++) {
//                appCodes[i] = apps[i].attributes["appCode"].value;
//            }
            selectApp.show({
                defaultParams: appCodes,
                title: '添加应用权限',
                accountId:curid,
                callback: function (result) {
                    if (result) {
                        var selectedApps = result.data.selectedApps;
                        var unSelectedApps = result.data.unSelectedApps;
                        Util.ajaxEx({
                            type: 'post',
                            data: {selectedApps: selectedApps, unSelectedApps: unSelectedApps},
                            url: Overall.path('/system/account/save-account-app') + '?accountId=' + curid,
                            async: false,
                            success: function (result) {
                                if (result.status) {
                                    $.topTips({
                                        mode: 'normal',
                                        tip_text: result.message
                                    });
                                } else {
                                    $.topTips({
                                        mode: 'warning',
                                        tip_text: result.message
                                    });
                                } 
                            }
                        });
                        return true;
                    }
                    return false;
                }
            });
        });
    }
 
    /**
     * 消息提示
     * @param message
     * @param isNormal
     */
    function showMessage(message, isNormal) {
        $.topTips({
            mode: isNormal ? 'normal' : 'warning',
            tip_text: message
        });
    }
  //切换事件
    typeBox.off('click').on('click','li',function(){
        var $this=$(this),type=$this.data('type'),headSelector='thead>tr.'+type;
        
        if(!$this.hasClass('on')){
            $this.addClass('on').siblings().removeClass('on'); 
            cache.type=type; 
            load(type);  
        } 
    });
    
    
    btnSearch.off('click').on('click',function(){
        load(); 
    });
      accountGrid.off('click').on('click','tbody tr:visible,.opt-add',function(){
        var $this=$(this),
            id=$this.find('.id').data('id')||$this.data('id'),
            model=cache.view.grid.get(id);
        if(model){
            var pId=model.get('id');
            if($this.hasClass('opt-add')){
                dialog([{id:pId}],id,pId,function(point){
                    var val=(model.get('integral_total')-'')+point;
                    model.set('integral_total',val%1?val.toFixed(2):val);
                });
            }
            $this.addClass('on').siblings().removeClass('on');
            id&&detail(id,pId);
        }
    });
 

    searchCon.off('keyup').on('keyup','.form-control',function(e){
        if(e.keyCode==13){
            load();
        }
    });  
    
    /**
     * 初始化
     */
    function init() { 
        //绑定页面事件
        initBindEvents();  
        load(); 
    }

    return {
        init: init
    }
})
;
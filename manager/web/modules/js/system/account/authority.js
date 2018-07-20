 
define(function (require, exports, module) { 
    require('/modules/js/public/plugin/topTips.js');
    require('/modules/js/public/plugin/confirm.js');
    require('/frontend/js/plugin/grid.js');
    require('/frontend/js/lib/template.js');
    require('/frontend/js/lib/overall.js');
    require('dialog.js');
    require('/frontend/js/lib/dialog');
    var dialogPlus = require('/frontend/js/lib/artDialog/src/dialog-plus.js'); 
   var userAdminGrid=$('#user_admin'),  accountId = O.getQueryStr('id'),userOperateGrid=$('#user_operation');;

    var request=function(options){
        return O.ajaxEx(options).error(function(){
            $.topTips({mode:'warning',tip_text:'出现异常'});
        });
    }; 
    var defaultUserAdmins = [];
    userAdminGrid.show();
    var loadAdmin=function(){   
        userAdminGrid.find('tbody').html('<tr><td colspan="' + userAdminGrid.find('thead tr.on th').length + '" class="align-c" style="height:70px;">正在加载数据...</td></tr>');
        userAdminGrid.grid({
            idField : 'id',
            templateid : 'user_gridrow_template',
            pagesize : 100,
            emptyText : '无数据', 
            method:'get', 
            queryParams : function(){ 
            },
            getReadURL : function(){
                var strurl = "/system/user/account-user?account_id="+accountId+'&level=2'; 
                return O.path(strurl);
            },
            sortEvent : function(){ 
            },
            filter : function(model){  
            },
            loaded:function(data){ 
                defaultUserAdmins=[];
                var dataAdminUsers = data.items;
                for (var i = 0; i < dataAdminUsers.length; i++) {
                     defaultUserAdmins.push({text: dataAdminUsers[i].name ,value: dataAdminUsers[i].id}); 
                }
            } 
        });  
    }; 
    userOperateGrid.show();
    var defaultUserOperates = [];
    var loadOperate=function(){   
        userOperateGrid.find('tbody').html('<tr><td colspan="' + userOperateGrid.find('thead tr.on th').length + '" class="align-c" style="height:70px;">正在加载数据...</td></tr>');
        userOperateGrid.grid({
            idField : 'id',
            templateid : 'user_gridrow_template',
            pagesize : 100,
            emptyText : '无数据', 
            method:'get', 
            queryParams : function(){ 
            },
            getReadURL : function(){
                var strurl = "/system/user/account-user?account_id="+accountId+'&level=3'; 
                return O.path(strurl);
            },
            sortEvent : function(){ 
            },
            filter : function(model){  
            },
            loaded:function(data){ 
                defaultUserOperates=[];
                var dataOperateUsers = data.items;
                for (var i = 0; i < dataOperateUsers.length; i++) {
                     defaultUserOperates.push({text: dataOperateUsers[i].name ,value: dataOperateUsers[i].id}); 
                }
            } 
        });  
    }; 
    /**
     * 其他绑定事件
     */
    function initBindEvents() { 
        $('body').on('click','.opt-setadmin',function(){
            showAddAdmin();
        });
        
        $('body').on('click','.opt-setoperate',function(){
            showAddOperate();
        });
         //添加管理员用户
        $('body').on('click','.opt-addadmin', function () {
            addUser(2);
        });
        //添加操作员用户
        $('body').on('click','.opt-addoperation', function () {
            addUser(3);
        });
    }
    
    
    var DialogAddUser = window.DialogAddUser = {
        dialog: null,
        save: null,
        ok: function (result) {
            //执行保存后操作
            this.save(result);
            //关闭Dialog
            this.cancel();
        },
        cancel: function () {
            this.dialog && this.dialog.close().remove();
        },
        defaultParams: null,//默认参数 array，
        selectedCheck: function (value) {
            return true;
        }//选中校验
    }; 
   function addUser(level){
       var title = '新增公众号管理员';
       if(level==3){
           title='新增公众号操作员';
       }
         var url = '/system/user/add-account'; 
          url = url + '?accountId=' + accountId+"&level="+level;
          DialogAddUser.save = function (result) { 
           if(level == 2) {  
                loadAdmin();
            } else if(level==3){
               loadOperate();
            }
            return true;
         };
        DialogAddUser.dialog = $.dialog({
                url: url,
                title: title,
                id: 'js_adduser',
                width: 725,
                height: 400,
                onshow: function () { 
                },
                onclose: function () {
                    this.close().remove();
                    var iframe = document.getElementsByName('js_adduser')[0];
                    iframe && iframe.parentNode.removeChild(iframe);
                }
            }).showModal();
   }
   /**
     *     /**
     * 显示新增或修改用户组 
     * @param data
     */
    function showAddAdmin( ) {
        var url = '/system/user/select'; 
          url = url + '?id=' + accountId+"&level=2";

        DialogAddUser.save = function (result) { 
            if (result !== null) {
                defaultUserAdmins=[];
                var data = result.data;
                for (var i = 0; i < data.length; i++) {
                     defaultUserAdmins.push({text: data[i].text ,value: data[i].value}); 
                }   
                saveAccountUsers(defaultUserAdmins,2);
                return true;
            }
        };
        DialogAddUser.defaultParams=defaultUserAdmins;
        DialogAddUser.dialog = $.dialog({ 
            url: Overall.path(url),
            title: '公众号管理员',
            id: 'js_admin',
            width: 725,
            height:500,
            onshow: function () {
            },
            onclose: function () {
                this.close().remove();
                var iframe = document.getElementsByName('js_admin')[0];
                iframe && iframe.parentNode.removeChild(iframe);
            }
        }).showModal();
    }
      /**
     *     /**
     * 显示新增或修改用户组 业务人员
     * @param data
     */
    function showAddOperate( ) {
        var url = '/system/user/select'; 
          url = url + '?id=' + accountId+"&level=3";

        DialogAddUser.save = function (result) { 
            if (result !== null) {
                defaultUserOperates=[];
                var data = result.data;
                for (var i = 0; i < data.length; i++) {
                     defaultUserOperates.push({text: data[i].text ,value: data[i].value}); 
                }   
                saveAccountUsers(defaultUserOperates,3);
                return true;
            }
        };
        DialogAddUser.defaultParams=defaultUserOperates;
        DialogAddUser.dialog = $.dialog({ 
            url: Overall.path(url),
            title: '公众号操作员',
            id: 'js_operate',
            width: 725,
            height:500,
            onshow: function () {
            },
            onclose: function () {
                this.close().remove();
                var iframe = document.getElementsByName('js_operate')[0];
                iframe && iframe.parentNode.removeChild(iframe);
            }
        }).showModal();
    }
    function saveAccountUsers(usersdata,opt){
        var postdata =  JSON.stringify(usersdata);
         O.ajaxEx({
            data: {'userdata':postdata},
            type: 'post',
            url: O.path('/system/account/save-account-users?level='+opt+'&account_id='+accountId),
            success: function(data) {
               if(data.result == true){
                    if(opt == 2) {  
                         loadAdmin();
                    } else if(opt==3){
                        loadOperate();
                    }
                    $('.pt').hide();
                }
            },
            error: function() {
                $('.pt').hide();
                _errorCallback();
            }
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
    
    /**
     * 初始化
     */
    function init() { 
        //绑定页面事件
        initBindEvents();  
        loadAdmin(); 
        loadOperate();
    }

    return {
        init: init
    }
})
;
'use strict';
/*global O,$,define*/
define(function (require, exports, module) {
    require('../../../../frontend/js/plugin/grid.js');
    require('../../../../frontend/js/lib/dialog');
    require('../../../../frontend/js/plugin/search-bar');
    var userGrid = $("#view-table");

//    $('#view-table').grid({
//        url: O.path('/basic/fan-admin/get-fan-admins'),
//        delurl: O.path('/basic/fan-admin/del-admin'),
//        idField: 'id',
//        templateid: 'admin_template',
//        pagesize: 10,
//        searchText: true,
//        emptyText: '暂无公众号运营人员'
//    });
     var load=function(){   
        userGrid.find('tbody').html('<tr><td colspan="' + userGrid.find('thead tr.on th').length + '" class="align-c" style="height:70px;">正在加载数据...</td></tr>'); 
         userGrid.grid({
             url: O.path('/basic/fan-admin/get-fan-admins'),
            delurl: O.path('/basic/fan-admin/delete'),
            idField: 'id',
            templateid: 'admin_template',
            pagesize: 10,
            searchText: true,
            emptyText: '暂无公众号运营人员'   
        });  
    }; 
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
   function addUser(id ){ 
         var url = '/basic/fan-admin/fan-admin-add?id='+id;
          DialogAddUser.save = function (result) { 
              load();
              return true;
         };
        DialogAddUser.dialog = $.dialog({
                url: url,
                title: "公众号运营人员",
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
   $('body').on('click', '.edit', function() {  
        var curid= $(this).attr("data-id"); 
        addUser(curid);
    });
    $('#add_admin_btn').off('click').on('click', function () {
        addUser('');
    });
    load();
});


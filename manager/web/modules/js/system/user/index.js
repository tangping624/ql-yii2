 
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
   var userGrid=$('#user_grid'),  btnSearch=$('#btn_search'),typeBox=$('#type_box') ,searchCon=$('#search_con'); 
    // 对子页面访问
    var DialogAddUser = window.DialogAddUser = {
        dialog: null,
        save: function () {
            //保存后刷新
            cache.view.refresh();
            //关闭用户详情面板
            curUserDetailsPanel._hide();
        },
        ok: function () {
            //执行保存后操作
            this.save();
            //关闭Dialog
            this.cancel();
        },
        cancel: function () {
            this.dialog && this.dialog.close().remove();
        }
       
    };
 

    /**
     * 用户详细面板
     */
    var curUserDetailsPanel = null;

    var curSelectedUser = [];

 
  var cache={
        type:typeBox.find('.on').data('type')
    };

    var request=function(options){
        return O.ajaxEx(options).error(function(){
            $.topTips({mode:'warning',tip_text:'出现异常'});
        });
    }; 
      userGrid.show();
    var load=function(queryType){   
        userGrid.find('tbody').html('<tr><td colspan="' + userGrid.find('thead tr.on th').length + '" class="align-c" style="height:70px;">正在加载数据...</td></tr>');
       if(cache.view){
            cache.view.search();
        }else{ 
         cache.view=userGrid.grid({
            idField : 'id',
            templateid : 'user_gridrow_template',
            pagesize : 100,
            emptyText : '无数据', 
            method:'get', 
            queryParams : function(){
                var paramEls=searchCon.find('.form-control:visible'); 
                cache.params={};
                if(cache.type=='all'){
                   cache.params['enabled']='';
                }else if (cache.type=='normal'){
                    cache.params['enabled']=1;
                }else if(cache.type=='disables'){
                    cache.params['enabled']=0; 
                } 
                paramEls.each(function(i){
                    var el=paramEls.eq(i);
                    cache.params[el.attr('name')]=el.val();
                });
                return $.param(cache.params);
            },
            getReadURL : function(){
                var strurl = "/system/user/user-list"; 
                return O.path(strurl);
            },
            sortEvent : function(){
                //detailGrid.hide();
            },
            filter : function(model){
                model.set('type',cache.type);
                model.set('enabled', model.get('enabled') == 1 ? '' : '禁用'); 
            },
            loaded:function(data){
                cache.total = data.total; 
                $(".opt-redisabled").filter("[data-isdisabled='1']").show();
                $(".opt-disabled").filter("[data-isdisabled='0']").show();
 
            }, 
            rowClick: function (model) {
                userGrid_RowClick(model);
            },
            rowCheckEvent: function (model) {
                curSelectedUser = userGrid.getSelecteds();
                userGrid_CheckEvent(model);
            }
        }); 
    }
    }; 

    /**
     * 用户列表复选框点击事件
     * @param model
     */
    function userGrid_CheckEvent(model) {
        if (curSelectedUser.length !== 0) {
            curUserDetailsPanel._hide(); 
        }  
    }

    /**
     * 用户列表行点击事件
     * @param mode
     */
    function userGrid_RowClick(mode) {
        //用来控制
        if (curSelectedUser.length === 0) {
            //curUserMultiplePanel._hide();
            showUserDetails(mode);
        }
    }

    /**
     * 显示用户详细面板
     * @param mode
     */
    function showUserDetails(mode) {
        $("#ud_id").val(mode.get('id'));  
        $("#ud_name").text(mode.get('name'));
        $("#ud_account").text(mode.get('account'));
         $("#groupname").text(mode.get('groupname'));
        $("#ud_mobile").text(mode.get('mobile'));
        $("#ud_email").text(mode.get('email'));   
        if (mode.get('enabled') === '禁用') {
            $("#ud_enable_btn").show();
            $("#ud_disable_btn").hide();
        } else {
            $("#ud_disable_btn").show();
            $("#ud_enable_btn").hide();
        }
        //初始化详细信息
        curUserDetailsPanel._show();
    }
    
 
  
    /**
     * 初始化右侧滑出面板
     * @param string containerId
     * @return panel 面板对象
     */
    function initUserListOpera(containerId) {
        var _this = null;
        $('#' + containerId).userListOpera({
            customEvent: function () {
                _this = this;
            }
        });
        return _this;
    }

    /**
     * 其他绑定事件
     */
    function initBindEvents() {
        //添加用户
        $('#add_user_btn').bind('click', function () {
            DialogAddUser.dialog = $.dialog({
                url: Overall.path('/system/user/add' ),
                title: '新增用户',
                id: 'js_map',
                width: 725,
                height: 400,
                onshow: function () {
                    //console.log('__destruct');
                },
                onclose: function () {
                    this.close().remove();
                    var iframe = document.getElementsByName('js_map')[0];
                    iframe && iframe.parentNode.removeChild(iframe);
                }
            }).showModal();
        });

        //用户详细面板-关闭面板
        $("#ud_close").bind('click', function () {
            //移除当前行样式
            $("tr[class*='row-bg']", userGrid.el).removeClass('row-bg');
            curUserDetailsPanel._hide();
        });

        //用户详细面板-修改用户
        $("#ud_edit_btn").bind('click', function () {
            var uid = $("#ud_id").val();
            if (uid) {
                DialogAddUser.dialog = $.dialog({
                    url: Overall.path('/system/user/edit?oid=' + uid),
                    title: '修改用户',
                    id: 'js_map',
                    width: 725,
                    height: 400,
                    onshow: function () {
                        //console.log('__destruct');
                    },
                    onclose: function () {
                        this.close().remove();
                        var iframe = document.getElementsByName('js_map')[0];
                        iframe && iframe.parentNode.removeChild(iframe);
                    }
                }).showModal();
            }
        });

       
        //用户详细面板-禁用用户
        $("#ud_disable_btn").bind('click', function () {
            $.confirm({
                title: '温馨提示',
                content: '你确定要禁用' + $("#ud_name").text() + '吗？',
                ok: function () {
                    var id = $("#ud_id").val();
                    Util.ajaxEx({
                        type: 'get',
                        url: Overall.path('/system/user/disable?id=' + id),
                        async: true,
                        success: function (data) {
                            if (data.status) {
                                curUserDetailsPanel._hide();
                                cache.view.refresh();
                            }
                            showMessage(data.message, data.status);
                        }
                    });
                }
            });
        });

        //用户详细面板-重置用户密码
        $("#ud_reset_password_btn").on('click', function () {
             var uid = $("#ud_id").val();
            if (uid) {
                DialogAddUser.dialog = $.dialog({
                    url: Overall.path('/system/user/chgpassword?oid=' + uid),
                    title: '修改密码',
                    id: 'js_map',
                    width: 725,
                    height: 250,
                    onshow: function () {
                        //console.log('__destruct');
                    },
                    onclose: function () {
                        this.close().remove();
                        var iframe = document.getElementsByName('js_map')[0];
                        iframe && iframe.parentNode.removeChild(iframe);
                    }
                }).showModal();
            } 
        });

        //用户详细面板-启用用户
        $("#ud_enable_btn").bind('click', function () {
            $.confirm({
                title: '启用用户',
                content: '你确定要启用' + $("#ud_name").text() + '吗？',
                ok: function () {
                    var id = $("#ud_id").val();
                    Util.ajaxEx({
                        type: 'get',
                        url: Overall.path('/system/user/enable?id=' + id),
                        async: true,
                        success: function (data) {
                            if (data.status) {
                                curUserDetailsPanel._hide();
                                cache.view.refresh();
                            }
                            showMessage(data.message, data.status);
                        }
                    });
                }
            });
        });

        //用户详细面板-删除用户
        $("#ud_delete_btn").bind('click', function () {
            $.confirm({
                title: '温馨提示',
                content: '你确定要删除所选的用户吗？',
                tip: '你不能撤销此操作',
                ok: function () {
                    var id = $("#ud_id").val();
                    Util.ajaxEx({
                        type: 'get',
                        url: Overall.path('/system/user/delete?id=' + id),
                        async: true,
                        success: function (data) {
                            if (data.status) {
                                curUserDetailsPanel._hide();
                                cache.view.refresh();
                            }
                            showMessage(data.message, data.status);
                        }
                    });
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
         curUserDetailsPanel._hide();
    });
    
    
    btnSearch.off('click').on('click',function(){
        load(); 
    });
      userGrid.off('click').on('click','tbody tr:visible,.opt-add',function(){
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
        //初始化用户详细面板
        curUserDetailsPanel = initUserListOpera('user_details_panel');
        curUserDetailsPanel._hide(); 
        load(); 
    }

    return {
        init: init
    }
})
;
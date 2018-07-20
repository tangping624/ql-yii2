'use strict';
define(function (require, exports, module) {
  require('/modules/js/public/plugin/tree.js');
  require('/modules/js/public/plugin/topTips.js');
  require('/modules/js/public/plugin/confirm.js');
  require('/frontend/js/lib/overall.js');
  require('dialog.js');
  $.Template = require('/frontend/js/lib/template.js')
  require('/frontend/js/lib/dialog');

  /**
     * 当前员工树
     * @type object
     */
    var userTree = null;
    var curNode = null;
    var curIndex = null;
    var _showTips = function(tip) {
        var d = $.tips(tip);
        setTimeout(function() {
            d.close().remove();
        }, 5000);
    };
    /**
     * 加载员工树
     */
    function loadUserTree() {
        var treeDataUrl = Overall.path('/type/type/ajax-list');
        $.ajax({
            type: 'post',
            url: treeDataUrl,
            success: function (data) {
                // if(!data.data[0].treeText){
                //     data.data[0].treeText='添加新用户'
                // }
                // console.log(data);
                userTree = $('.js-tree').tree({
                    data: data,
                    iClick: function (n, o, d) {
                        var userId = d.id;
                        curIndex = ($(o).data('index'));
                        // console.log(curIndex);
                        if(userId) {loadUserList(userId);}
                        curNode = n;
                    },
                    nodeMouseover: userTreeNodeMouseOver ,
                    nodeMouseout: userTreeNodeMouseOut
                });
                $($('.tree-node')[0]).addClass('node-bg');
                data[0].id && loadUserList(data[0].id);
                $('.js-tree>.tree-list>li').each(function(i,v){
                    $(v).attr('data-code',data[i].code)
                })
            }
        });
    }

    /**
     * 组织树节点MouseOver显示操作按钮
     * @param data
     * @param node
     */
    function userTreeNodeMouseOver(data, node) {
        var isTopCategory=$(node).parent().attr('data-code');

        if(isTopCategory&&($(node).text() == '全部分类'||$(node).text() == '携程'||$(node).text() == '百科'||$(node).text() == '游说')){
             $(node).append('<div class="tree-opera"><span class="fonticon fonticon-edit" mode="edit" data-title="修改分类"></span></div>');
        }else if(isTopCategory){
            $(node).append('<div class="tree-opera"><span class="fonticon fonticon-edit" mode="edit" data-title="修改分类"></span><span class="fonticon fonticon-plus" mode="add" data-title="添加分类"></span></div>');
        }else{
            $(node).append('<div class="tree-opera"><span class="fonticon fonticon-delete" mode="delete" data-title="删除分类"></span><span class="fonticon fonticon-edit" mode="edit" data-title="修改分类"></span></div>');
        }
        //添加事件
        var $treeOpera = $(node).find('.tree-opera');
        $treeOpera.on('click', '.fonticon-plus', function () {
           
            showUserEditor(node,data);
            return false;
        });
        $treeOpera.on('click', '.fonticon-delete',function(){
            curNode=$(this).closest('.tree-node')
            var id = data.id;
            if(id) {
                $.confirm({
                    title: '温馨提示',
                    content: '确定删除该分类？',
                    tip: '没有子分类才可以被删除。',
                    ok: function () {
                        if(curIndex!=0) {
                            O.ajaxEx({
                                url: Overall.path('/type/type/delete?id='+ id),
                                async: false,
                                success: function (data) {
                                    if (!data.result) {
                                        showMessage(data.msg);
                                    } else {
                                        showMessage(data.msg,'isNormal');
                                        userTree._removeNode(curNode);
                                    }
                                }
                            });
                        }else{
                            showMessage('无法删除！')
                        }
                    }
                });
            return false;
            }
        });

        $treeOpera.on('click', '.fonticon-edit',function(){
            var id = data.id;
            if(isTopCategory){
                var status=1;
               editUser(data,id,node,status); 
            }else{
                // console.log("000");
                
               editUser(data,id,node); 
            }
            
            return false;
        });
    }

    /**
     * 组织树节点MouseOut移除操作按钮
     * @param node
     */
    function userTreeNodeMouseOut(node) {
        var $treeOpera = $(node).find('.tree-opera');
        if ($treeOpera.length) {
            $treeOpera.remove();
        }
    }

    var DialogEditUser = window.DialogEditUser = {
        dialog: null,
        save: null,
        ok: function (result) {
            //执行保存后操作
            this.save(result);
            if(result.id){loadUserList(result.id)};
            //关闭Dialog
            this.cancel();
        },
        cancel: function () {
            this.dialog && this.dialog.close().remove();
        }
    };

    /**
     * 显示新增或修改员工
     * @param Dom treeNode 结构树节点
     */
    function showUserEditor(treeNode, data) {
        var url = '/type/type/add';
        var parent_id = data.id;
        url = url + '?parent_id=' + parent_id;
        DialogEditUser.save = function (result) {
            if (result !== null) {
                if(data.treeText=='添加新用户') {
                    data.treeText = result.name;
                    data.id = result.id;
                    userTree._updateNode(data, treeNode);
                    return true;
                }else{
                    userTree._addNode({
                        treeText: result.name,
                        id: result.id,
                    },treeNode);
                    location.reload();
                    return true;
                }
            }
        };

        DialogEditUser.dialog = $.dialog({
            url: Overall.path(url),
            title: '新增分类',
            id: 'js_map',
            width: 725,
            height:375,
            onshow: function () {
            },
            onclose: function () {
                this.close().remove();
                var iframe = document.getElementsByName('js_map')[0];
                iframe && iframe.parentNode.removeChild(iframe);
               
            }
        }).showModal();
    }
    /**
     * 加载用户列表
     */
    function loadUserList(type) {
        var listDataUrl = Overall.path('/type/type/show?id='+type);
        O.ajaxEx({
            type: 'get',
            url: O.path(listDataUrl),
            success : function(data){
                var list =  data;
                var listData = {list:list};
                $('#user_grid').html($($.Template($('#grid_template').html(), listData)));
            },
            error: function() {
                showMessage('网络错误');
            }
        });
    }
    
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
            data: {'id':id, 'is_display' : checked ? '1' : '0'},
            url: O.path('/type/type/set-display'),
            success : function(data){
                if(data.result == false) {
                    toggle(!checked);
                    $this.prop('checked',!checked);
                  showMessage(data.msg);
                }else{
                    showMessage(data.msg,"isNormal");
                }
            },
            error: function() {
                toggle(!checked);
                $this.prop('checked',!checked);
                 showMessage(data.msg);
            }
        });
    });
   
    //修改用户
    function editUser(data,id,curNode,status){
        // console.log(status);
        var url=status?'/type/type/add?id='+id+'&status='+status:'/type/type/add?id='+id;
        DialogEditUser.dialog = $.dialog({
            url: Overall.path(url),
            title: '修改分类',
            id: 'js_map',
            width: 725,
            height: 375,
            onclose: function () {
                this.close().remove();
                var iframe = document.getElementsByName('js_map')[0];
                iframe && iframe.parentNode.removeChild(iframe);
             
            }
        }).showModal();
        DialogEditUser.save = function (result) {
            if (result !== null) {
                data.treeText = result.name;
                data.id = result.id;
                userTree._updateNode(data, curNode);
                location.reload();
                return true;
            }
        };
    }
    function showMessage(message, isNormal) {
        var parent = window.parent || window;

        parent.$.topTips({
            mode: isNormal ? 'normal' : 'warning',
            tip_text: message
        });
    }
    loadUserTree();

});
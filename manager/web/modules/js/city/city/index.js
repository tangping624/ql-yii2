'use strict';
define(function (require, exports, module) {
  require('/modules/js/public/plugin/tree.js');
  require('/modules/js/public/plugin/topTips.js');
  require('/modules/js/public/plugin/confirm.js');
  require('/frontend/js/lib/overall.js');
  require('/frontend/js/lib/public.js');
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
    var id = $.getUrlParam('id')?$.getUrlParam('id'):'';

    /**
     * 加载员工树
     */
    function loadUserTree() {
        var treeDataUrl = Overall.path('/city/city/show-list');
        $.ajax({
            type: 'post',
            url: treeDataUrl,
            success: function (data) {
                // console.log(data);
                if(!$.isEmptyObject(data.data)){
                    if(!data.data[0].treeText){
                        data.data[0].treeText='添加新用户';
                    }
                    for(var i=0;i<data.data.length;i++){
                        if(data.data[i].is_default=='1'){
                           data.data[i].treeText=data.data[i].treeText+'(默认)'; 
                        }
                    }
                    userTree = $('.js-tree').tree({
                        data: data.data,
                        iClick: function (n, o, d) {
                            // console.log(n);
                            var userId = d.id;
                            curIndex = ($(o).data('index'));
                            if(userId) {loadUserList(userId);}
                            curNode = n;
                        },
                        nodeMouseover: userTreeNodeMouseOver ,
                        nodeMouseout: userTreeNodeMouseOut
                    });
                if(id){
                    $('node-bg').remove('node-bg');
                    for(var i=0;i<$('.tree-node').length;i++){
                        if($($('.tree-node')[i]).find(':last-child').attr('data-id') == id){
                            $($('.tree-node')[i]).addClass('node-bg');
                            loadUserList(id);
                            break;
                        }
                    }
                }else{
                    $($('.tree-node')[0]).addClass('node-bg');
                    data.data[0].id && loadUserList(data.data[0].id); 
                }
                    $('.js-tree>.tree-list>li').each(function(i,v){
                        $(v).attr('data-code',data.data[i].code);
                        // $(v).find(".glyphicon-home").attr('data-default',data.data[i].is_default);
                    })
                }
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
        var def=data.is_default;
        if(isTopCategory&&def=='0'){
            $(node).append('<div class="tree-opera"><span class="glyphicon glyphicon-home" mode="default" data-title="设为默认城市" style="color:#b0b0ae;top:4px;margin-right:5px;"></span><span class="fonticon fonticon-plus" mode="add" data-title="添加区域"></span><span class="fonticon fonticon-edit" mode="edit" data-title="修改"></span><span class="fonticon fonticon-delete" mode="delete" data-title="删除城市"></div>');
        }else if(isTopCategory&&def=='1'){
            $(node).append('<div class="tree-opera"><span class="fonticon fonticon-plus" mode="add" data-title="添加区域"></span><span class="fonticon fonticon-edit" mode="edit" data-title="修改"></span><span class="fonticon fonticon-delete" mode="delete" data-title="删除城市"></div>');
        } else {
            $(node).append('<div class="tree-opera"><span class="fonticon fonticon-delete" mode="delete" data-title="删除区域"></span><span class="fonticon fonticon-edit" mode="edit" data-title="修改"></span></span></div>');
        }
        //添加事件
        var $treeOpera = $(node).find('.tree-opera');
        $treeOpera.on('click', '.fonticon-plus', function () {
            showUserEditor(node,data);
            return false;
        });
         $treeOpera.on('click', '.glyphicon-home', function () {
            curNode=$(this).closest('.tree-node');
            var id = data.id;
            // console.log(id);
            if(id) {
                // $.confirm({
                //     title: '温馨提示',
                //     content: '确定将该城市设为默认城市？',
                    
                //     ok: function () {
                //         if(curIndex!=0) {
                            O.ajaxEx({
                                url: Overall.path('/city/city/set-default-city?id=' + id),
                                async: false,
                                success: function (data) {
                                    if (!data.result) {
                                        showMessage('设为默认失败');
                                    } else {
                                        showMessage(data.msg,'isNormal');
                                        setTimeout("location.reload()",1000);
                                    }
                                }
                            });
                //         }else{
                //             showMessage('无法设为默认！');
                //         }
                //     }
                // });
                return false;
            }
        });
         
        $treeOpera.on('click', '.fonticon-delete',function(){
            curNode=$(this).closest('.tree-node');
            var id = data.id;
            if(id) {
                $.confirm({
                    title: '温馨提示',
                    content: '确定删除该城市？',
                    tip: '没有下级城市才可以被删除。',
                    ok: function () {
                        if(curIndex!=0) {
                            O.ajaxEx({
                                url: Overall.path('/city/city/delete?id=' + id),
                                async: false,
                                success: function (data) {
                                    if (!data.result) {
                                        showMessage('删除失败');
                                    } else {
                                        showMessage(data.msg,'isNormal');
                                        userTree._removeNode(curNode);
                                    }
                                }
                            });
                        }else{
                            showMessage('无法删除！');
                        }
                    }
                });
                return false;
            }
        });
        $treeOpera.on('click', '.fonticon-edit',function(){
            var id = data.id;
            editUser(data,id,node);
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
    $('#addType').click(function(){
        var url = '/city/city/add';
        var index=$('.js-tree .tree-list li').length;
        $('.node-bg').removeClass('node-bg');
        DialogEditUser.save = function (result) {
            if (result !== null) {
                //$('.js-tree>.tree-list').append('<li class="tree-item open"><div class="tree-node node-bg"><span class="tree-blank"></span><span class="tree-text" data-index="'+index+'">'+result.name+'</span></div></li>');
                window.location="/city/city/index?id="+result.id;
            }
        };

        DialogEditUser.dialog = $.dialog({
            url: Overall.path(url),
            title: '新增城市',
            id: 'js_map',
            width: 725,
            height:880,
            onshow: function () {
            },
            onclose: function () {
                this.close().remove();
                var iframe = document.getElementsByName('js_map')[0];
                iframe && iframe.parentNode.removeChild(iframe);
            }
        }).showModal();

    })
    function showUserEditor(treeNode, data) {
        var url = '/city/city/add';
        var parent_id = data.id;
        // var fullcode = data.fullcode;
        url = url + '?parent_id=' + parent_id ;
        DialogEditUser.save = function (result) {
            if (result !== null) {
                if(data.treeText=='添加新用户') {
                    data.treeText = result.name;
                    data.id = result.id;
                    // data.fullcode = result.fullcode;
                    userTree._updateNode(data, treeNode);
                    return true;
                }else{
                    userTree._addNode({
                        treeText: result.name,
                        id: result.id,
                        // fullcode: result.fullcode
                    },treeNode);
                    return true;
                }
            }
        };

        DialogEditUser.dialog = $.dialog({
            url: Overall.path(url),
            title:'新增区域',
            id: 'js_map',
            width: 725,
            height:880,
            onshow: function () {
            },
            onclose: function () {
                this.close().remove();
                var iframe = document.getElementsByName('js_map')[0];
                iframe && iframe.parentNode.removeChild(iframe);
            }
        }).showModal();
    }
    //修改用户
    function editUser(data,id,node){
        DialogEditUser.dialog = $.dialog({
            url: Overall.path('/city/city/add?id='+id),
            title:'修改城市',
            id: 'js_map',
            width: 725,
            height: 880,
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
                userTree._updateNode(data, node);
                return true;
            }
        };
    }
    /**
     * 加载用户列表
     */
    function loadUserList(city) {
        var listDataUrl = Overall.path('/city/city/show?id='+city);
        O.ajaxEx({
            type: 'get',
            url: O.path(listDataUrl),
            success : function(data){
                var list =  data.data;
                var listData = {list:list};
                $('#user_grid').html($($.Template($('#grid_template').html(), listData)));
                $('.ueedit-box').html(list.content);
                
                try{
                    var myCenter = list.latitudes ? new google.maps.LatLng(list.latitudes,list.longitudes) : new google.maps.LatLng(35.1923177,33.3623828);
                    var mapProp = {
                        center: myCenter,
                        zoom: 10,
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                    };
                //在指定DOM元素中嵌入地圖  
                    var map = new google.maps.Map(document.getElementById("l-map"), mapProp);
                    var marker = new google.maps.Marker({  
                        position: myCenter //經緯度  
                    });
                    marker.setMap(map);
                }
                catch(err){
                    $.topTips({
                        mode: 'warning',
                        tip_text: 'google地图加载失败，请打开vpn并刷新'
                    });
                }
            },
            error: function() {
                showMessage('网络错误');
            }
        });
    }
    //删除员工
    $('body').on('click','#deleted',function(){
        var id = $(this).parent().data('id');
        if(id) {
            $.confirm({
                title: '温馨提示',
                content: '确定删除该员工？',
                tip: '没有子员工才可以被删除。',
                ok: function () {
                    if(curIndex!=0) {
                        O.ajaxEx({
                            url: Overall.path('/missions/officers/delete?id=' + id),
                            async: false,
                            success: function (data) {
                                if (!data.result) {
                                    showMessage('删除失败');
                                } else {
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
    $('#submit_btn').on('click',function(){
        var id = $(this).parent().data('id');
        if(id) {
            $.confirm({
                title: '温馨提示',
                content: '确定删除该员工？',
                tip: '没有子员工才可以被删除。',
                ok: function () {
                    if(curIndex!=0) {
                        O.ajaxEx({
                            url: Overall.path('/missions/officers/delete?id=' + id),
                            async: false,
                            success: function (data) {
                                if (!data.result) {
                                    showMessage('删除失败');
                                } else {
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
    function showMessage(message, isNormal) {
        var parent = window.parent || window;

        parent.$.topTips({
            mode: isNormal ? 'normal' : 'warning',
            tip_text: message
        });
    }
    loadUserTree();

});
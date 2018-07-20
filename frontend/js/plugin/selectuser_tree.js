//树插件
define(function(require, exports, module) {
    jQuery.fn.tree = function (options) {
        var defaults = {
            iClick : function(o){
                //alert('good');
            }
        }
        var options = $.extend({}, defaults, options);
        return this.each(function () {
            var o = new tree(this,options);
            o.init();
        });
    };
    function tree(o,v) {
        this.o = o;
        for(var i in v){
            this[i] = v[i];
        }
    }

    tree.prototype = {
        init: function() {
            this.layout();
            this.fold();
            this.nodeClick();
        },
        //生成树
        layout:function(){
            var getTreeData = treeData;
            var html = [];
            var blank_i = 0;
            this.createTreeNode(getTreeData,html,blank_i);
            $(this.o).append(html.join(''));
        },
        //创建树节点
        createTreeNode:function (nodes,html,blank_i){
            html.push(this.createNodesWrapBegin());
            var len = nodes.length;
            for (var i = 0; i<len; i++) {
                html.push(this.createNodeBegin());
                html.push('<div class="tree-node">');
                //添加空白
                if(blank_i){
                    for(var j=0;j<blank_i;j++){
                        html.push('<span class="tree-blank"></span>');
                    }
                }
                //添加折叠图标
                if(nodes[i].childNode && nodes[i].childNode.length>0){
                    html.push('<i class="tree-arrow"></i>');
                }
                html.push(this.createNodeText(nodes[i]));
                html.push('</div>');
                //判断是否有子节点
                var empty_i = blank_i;
                if(nodes[i].childNode && nodes[i].childNode.length>0){
                    empty_i++;
                    this.createTreeNode(nodes[i].childNode,html,empty_i);
                }
                html.push(this.createNodeEnd());
            };
            html.push(this.createNodesWrapEnd());
        },
        createNodeBegin : function(){
            return '<li class="tree-item">';
        },
        createNodeEnd : function(){
            return '</li>';
        },
        createNodeText : function(node){
            return '<span class="tree-text" value="'+node.value+'">'+node.treeText+'</span>';
        },
        createNodesWrapBegin : function(){
            return '<ul class="tree-list">';
        },
        createNodesWrapEnd : function(){
            return '</ul>';
        },
        //节点点击事件
        nodeClick:function(){
            var _this = this;
            $(document).on('click','.tree-text',function(){
                _this.iClick(this);
            });
        },
        //折叠
        fold:function(){
            $(document).on('click','.tree-arrow',function(){
                var $parent = $(this).parent().parent();
                if(!$parent.hasClass('open')){
                    $parent.addClass('open');
                }else{
                    $parent.removeClass('open');
                }
            });
        }
    }
});

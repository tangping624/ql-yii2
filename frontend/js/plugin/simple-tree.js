/**
 * Created by weizs on 2015/6/19.
 */
define(function (require, exports, module) {
    require('../../3rd/ztree/js/jquery.ztree.all-3.5.min');
    require('../../css/plugin/simple-tree.css');

    //simple setting
    //仅支持单选、复选（单选样式需要根据需要定义）
    var defaultSetting = {
        data: {
            key: {
                name: 'treeText',
                title: 'treeText',
                children: 'childNode'
            }
        },
        view: {
            expandSpeed: "",
            dblClickExpand: false,
            showIcon: false,
            showLine: false//simple-tree 不支持zTree线条
        }
    };

    var SimpleTree = {
        tree: {},
        zTree: {},
        init: function (node, options) {
            if (node.is('ul')) {
                this.node = node;
            } else {
                this.node = $('<ul>');
                node.append(this.node);
            }
            var nodeId = this.node.attr('id');
            if (!nodeId) {
                nodeId = 'tree_id_' + (new Date - 0);
                this.node.attr('id', nodeId);
            }
            this.node.addClass('simple-tree');
            return this.create(nodeId, options);
        },
        create: function (nodeId, options) {
            var setting = $.extend(true, {}, defaultSetting, {
                check: {
                    enable: options.is_checked
                },
                callback: {
                    beforeClick: function (treeId, treeNode) {
                        var tree = $.fn.zTree.getZTreeObj(nodeId);
                        if (tree) tree.checkNode(treeNode, !treeNode.checked, true, true);
                        return true;
                    },
                    onClick: function (event, treeId, treeNode) {
                        options.onClick && options.onClick.call(SimpleTree.tree[nodeId], treeNode);
                    }
                }
            });

            this.tree[nodeId] = new this.SimpleTree(nodeId, this.node, setting, options.data);

            if (options.selected) {
                var tree = $.fn.zTree.getZTreeObj(nodeId);
                $.each(options.selected.value, function (i, value) {
                    if (value && value != '')
                        tree.checkNode(tree.getNodeByParam('value', value), true, true, true);
                });
            }

            return this.tree[nodeId];
        },
        SimpleTree: function (nodeId, node, setting, data) {
            $.fn.zTree.init(node, setting, data);
            this.nodeId = nodeId;
            this.getSelected = function () {
                var tree = $.fn.zTree.getZTreeObj(this.nodeId);
                return tree ? tree.getCheckedNodes() : [];
            }
        }
    };

    $.fn.extend(true, {
        tree: function (options) {
            return SimpleTree.init.call(SimpleTree, $(this), options);
        }
    });

});
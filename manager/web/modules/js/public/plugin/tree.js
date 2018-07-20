/*
 * 组织架构树插件
 * yuanl02 2015-3-20
 */
//define(function (require, exports, module) {
;
(function ($) {
    $.fn.tree = function (options) {
        var defaults = {
            data: [],      //外部json
            is_checked: false,
            validateChecked: function (data) {
                return true;
            },
            iClick: function (node, node_text, data) {   //节点点击事件
            },
            nodeMouseover: null,         //节点上鼠标悬停事件
            nodeMouseout: null,          //节点上鼠标移开事件
            filter: null                 //权限过滤
        }
        var options = $.extend({}, defaults, options);
        var o = new tree(this, options);
        o.init();
        return o;
    };
    /*
     * tree 类
     * @param string index_i 索引
     * @param array params 存储自定义属性
     */
    function tree(o, v) {
        this.o = o;
        for (var i in v) {
            this[i] = v[i];
        }
        this.index_i = 0;
        this.params = [];
    }

    tree.prototype = {
        init: function () {
            this.layout();
            this.fold();
            //this.autoUnfoldFirstRow();
            if (this.is_checked) {
                this.textClick();
                this.checkBoxClick();
            } else {
                this.nodeClick();
            }
            var strArr = [];
            if (this.is_checked) {
                strArr.push('<div class="tree-opera">');
                if (this.is_checked) {
                    strArr.push('<span class="fonticon fonticon-checkbox"></span>');
                }
                strArr.push('</div>');
                this.str = strArr.join('');
            }
            this.nodeEach();

            $(this.o).on('selectstart', function () {
                return false;
            })
        },
        //生成树
        layout: function () {
            var getTreeData = this.data;
            if (getTreeData.length) {
                var html = [];
                var blank_i = 0;
                this.createTreeNode(getTreeData, html, blank_i);
                $(this.o).append(html.join(''));
            }
        },
        saveData: function (dataJson) {
            var tempJson = {};
            for (x in dataJson) {
                if (x !== 'childNode') {
                    tempJson[x] = dataJson[x];
                }
            }
            this.params.push(tempJson);
        },
        /*
         * 创建树节点
         * @param josn nodes 外部数据
         * @param array html
         * @param number blank_i 填充空白数
         */
        createTreeNode: function (nodes, html, blank_i) {
            html.push(this.createNodesWrapBegin());
            var len = nodes.length;
            for (var i = 0; i < len; i++) {
                html.push(this.createNodeBegin());
                this.saveData(nodes[i]);
                if (this.filter) {
                    this.filter(this.params[this.index_i]);
                }
                //节点禁用
                if (this.params[this.index_i].disable) {
                    html.push('<div class="tree-node disable">');
                } else {
                    html.push('<div class="tree-node">');
                }

                //添加空白
                if (blank_i) {
                    for (var j = 0; j < blank_i; j++) {
                        html.push('<span class="tree-blank"></span>');
                    }
                }
                //添加折叠图标
                if (nodes[i].childNode && nodes[i].childNode.length > 0) {
                    html.push('<i class="tree-arrow"></i>');
                } else {
                    html.push('<span class="tree-blank"></span>');
                }
                html.push(this.createNodeText(nodes[i], this.index_i++));
                html.push('</div>');
                //判断是否有子节点
                var empty_i = blank_i;
                if (nodes[i].childNode && nodes[i].childNode.length > 0) {
                    empty_i++;
                    this.createTreeNode(nodes[i].childNode, html, empty_i);
                }
                html.push(this.createNodeEnd());
            }
            ;
            html.push(this.createNodesWrapEnd());
        },
        createNodeBegin: function () {
            return '<li class="tree-item">';
        },
        createNodeEnd: function () {
            return '</li>';
        },
        /*
         * 节点文本
         * @param josn data
         */
        createNodeText: function (data, data_index) {
            return '<span class="tree-text" data-id="'+data.id+'" data-index="' + data_index + '">' + data.treeText + '</span>';
        },
        createNodesWrapBegin: function () {
            return '<ul class="tree-list">';
        },
        createNodesWrapEnd: function () {
            return '</ul>';
        },
        //节点点击事件
        nodeClick: function () {
            var _this = this;
            $(this.o).on('click', '.tree-node', function () {
                var $nodeText = $(this).find('.tree-text');
                var data_index = $nodeText.attr('data-index');
                var dataJson = _this.params[data_index];
                if(!dataJson.disable){
                    $(_this.o).find('.tree-node').removeClass('node-bg');
                    $(this).addClass('node-bg');
                    _this.iClick(this, $nodeText[0], dataJson);
                }
                return false;
            });
        },
        //节点文本事件
        textClick: function () {
            var _this = this;
            $(this.o).on('click', '.tree-text', function () {
                var $node = $(this).parent('.tree-node');
                _this.checkboxOpera($node, $(this));
            });
        },
        checkBoxClick: function () {
            var _this = this;
            $(this.o).on('click', '.fonticon-checkbox', function () {
                var $node = $(this).parent().parent('.tree-node');
                var $nodeText = $node.find('.tree-text');
                _this.checkboxOpera($node, $nodeText);
            });
        },
        checkboxOpera: function ($node, $nodeText) {
            var data_index = $nodeText.attr('data-index');
            var dataJson = this.params[data_index];
            if (this.validateChecked(dataJson) === false) {
                return;
            }
            var isChecked = false;
            if ($node.hasClass('selected')) {
                isChecked = false;
            } else {
                isChecked = true;
            }
            dataJson['is_checked'] = isChecked;
            $node.removeClass('selected');
            if (isChecked) {
                $node.addClass('selected');
            }
            this.iClick($node[0], $nodeText[0], dataJson);
            return false;
        },
        addCheckbox: function (o, str) {
            $(o).append(str);
        },
        //节点遍历
        nodeEach: function () {
            var _this = this;
            var $node = $(this.o).find('.tree-node');
            if ($node) {
                $node.each(function (i, el) {
                    if (_this.is_checked) {
                        _this.addCheckbox(el, _this.str);
                    }
                    if (_this.nodeMouseover && _this.nodeMouseout) {
                        _this.addOpera(el);
                    }
                });
            }
        },
        //添加节点操作按钮
        addOpera: function (o) {
            var _this = this;
            $(o).hover(function () {
                var data_index = $(this).find('.tree-text').attr('data-index');
                var data = _this.params[data_index];
                $(_this.o).find('.tree-node').removeClass('node-hover');
                $(this).addClass('node-hover');
                _this.nodeMouseover(data, o);
            }, function () {
                $(this).removeClass('node-hover');
                _this.nodeMouseout(o);
            });
        },
        /*
         * 创建树节点
         * @param string html 单个节点html
         * @param number len 空白数
         * @param josn data 外部数据
         */
        addHtml: function (html, len, data) {
            $('.node-bg').removeClass('node-bg');
            html.push('<li class="tree-item"><div class="tree-node node-bg">');
            for (var i = 0; i < len; i++) {
                html.push('<span class="tree-blank"></span>');
            }
            html.push('<span class="tree-text"  data-index=' + (this.index_i++) + '>' + data.treeText + '</span></div></li>');
            this.saveData(data);
        },
        /*
         * 添加节点
         * @param josn data 外部数据
         * @param Dom o 树节点('.tree-node')
         */
        _addNode: function (data, o) {
            var $item = $(o).parent('.tree-item');
            var len = $item.children().length;
            var html = [];

            if (len > 1) {
                var blank_len = $(o).find('.tree-blank').length + 2;
                this.addHtml(html, blank_len, data);
                var $ul = $item.find('>ul');
                if ($ul.length) {
                    $ul.append(html.join(''));
                    var $arrow = $(o).find('.tree-arrow');
                    if ($arrow.length) {
                        $(o).parent('.tree-item').addClass('open');
                    }
                }
            } else {
                var blank_len = $(o).find('.tree-blank').length + 1;
                html.push('<ul class="tree-list">');
                this.addHtml(html, blank_len, data);
                html.push('</ul>');
                $item.append(html.join(''));
                $item.addClass('open');
                $(o).find('.tree-text').prev().remove();

                $(o).find('.tree-text').before('<i class="tree-arrow"></i>');
            }
            if (this.nodeMouseover && this.nodeMouseout) {
                var $treeNode = $item.find('> .tree-list > .tree-item:last > .tree-node');
                if ($treeNode.length) {
                    this.addOpera($treeNode[0]);
                }
            }
        },
        /*
         * 修改节点
         * @param josn data 外部数据
         * @param Dom o 树节点('.tree-node')
         */
        _updateNode: function (data, o) {
            $(o).find('.tree-text').text(data.treeText);
            var data_index = $(o).find('.tree-text').attr('data-index');
            var dataJson = this.params[data_index];
            dataJson.treeText = data.treeText;
        },
        /*
         * 删除节点
         * @param Dom o 树节点('.tree-node')
         */
        _removeNode: function (o) {
            var $currItem = $(o).parent();
            var len = $currItem.siblings().length;
            $prevItem = null;
            if (len === 0) {
                var $currList = $currItem.parent();
                $prevItem = $currList.parent();
                $prevItem.find('.tree-arrow').before('<span class="tree-blank"></span>').remove();
                $currList.remove();
            } else {
                if($currItem.prev().length){
                    $prevItem = $currItem.prev();
                }else{
                    $prevItem = $currItem.next();
                }
                $currItem.remove();
            }
            var $node = $prevItem.children('.tree-node');
            var $nodeText = $node.find('.tree-text');
            var data_index = $nodeText.attr('data-index');
            this.lockRow($node, $nodeText, data_index);
        },
        /*
         * 锁定行
         * @param dom item (tree-item)
         */
        lockRow: function ($node, $nodeText, data_index) {
            $(this.o).find('.tree-node').removeClass('node-bg');
            $node.addClass('node-bg');
            this.posScrollBar($node);
            var dataJson = this.params[data_index];
            this.iClick($node[0], $nodeText[0], dataJson);
        },
        /*
         * 根据值来锁定行
         * @param string data_index 树节点下文本附带的自定义data-index属性值('.tree-node .tree-text["data-index"]')
         */
        _lockRowByValue: function (value) {
            var data_index = 0;
            for (var i = 0; i < this.params.length; i++) {
                if (this.params[i].value === value) {
                    data_index = i;
                    break;
                }
            }
            var $nodeText = $(this.o).find('.tree-text[data-index="' + data_index + '"]');
            if ($nodeText.length) {
                var $node = $nodeText.parent('.tree-node');
                this.lockRow($node, $nodeText, data_index);
            }
        },

        /**
         * 选中行
         * @param value
         */
        _checkedNodeByValue: function (value) {
            var data_index = -1;
            for (var i = 0; i < this.params.length; i++) {
                if (this.params[i].value === value) {
                    data_index = i;
                    break;
                }
            }
            if (data_index === -1) {
                return;
            }
            var $nodeText = $(this.o).find('.tree-text[data-index="' + data_index + '"]');
            if ($nodeText.length) {
                var $node = $nodeText.parent('.tree-node');
                this.checkboxOpera($node, $nodeText);
            }
        },
        /*
         * 添加节点时锁定新增行
         * @param Dom o 树节点('.tree-node')
         */
        posScrollBar: function (o) {
            // var tree_h = $(this.o).outerHeight();      //树的高度(包含padding)
            // var tree_scroll_top = $(this.o).scrollTop();
            // var tree_top = $(this.o).offset().top;
            // var o_h = $(o).height();                //节点高度
            // var o_top = $(o).offset().top;
            // var m_y = Math.ceil((tree_h) / 2);            //中心位置


            // var result = o_top - tree_top - tree_h;

            // if (result > 0) {
            //     $(this.o).animate({
            //         scrollTop: Math.floor(tree_scroll_top + tree_h + result) + 'px'
            //     }, 500);
            // }
        },
        // //默认展开第一级
        // autoUnfoldFirstRow: function () {
        //     var $firstNode = $('> .tree-list > .tree-item', $(this.o));
        //     $firstNode.addClass('open');
        // },
        /*
         * 展开
         * @param Dom o 树节点('.tree-node')
         */
        unfold: function (o) {
            var $item = $(o).parent('.tree-item').parent('.tree-list').parent('.tree-item');
            if ($item.length) {
                if (!$item.hasClass('open')) {
                    $item.addClass('open');
                }
                var $node = $item.find('> .tree-node');
                this.unfold($node[0]);
            }
        },
        //收起
        fold: function () {
            $(this.o).on('click', '.tree-arrow', function () {
                var $parent = $(this).parent().parent();
                if (!$parent.hasClass('open')) {
                    $parent.addClass('open');
                } else {
                    $parent.removeClass('open');
                }
                return false;
            });
        }
    }
    //})
})(jQuery);

/*
 * tree插件
 */
define(function (require, exports, module) {
    jQuery.fn.tree = function (options) {
        var defaults = {
            data: [],      //外部json
            is_checked: false,
            isShowUnfoldPanel: false,
            validateChecked: function (data) {
                return true;
            },
            //节点点击事件
            iClick: null,
            //节点上鼠标悬停事件
            nodeMouseover: null,
            //节点上鼠标移开事件
            nodeMouseout: null,
            //节点禁用事件
            filter: null,
            //节点操作事件
            nodeOpera: null,
            //新增修改节点是否默认进行排序
            enableSort: false,
            //新增修改节点后排序方式
            sortMode: 'ASC',
            //排序字段
            sortField: null,
            //排序前操作 node添加或修改后的节点;data当前节点数据;opera[add|update];
            sortBefore: function (node, data, opera) {
            }
        }
        var options = $.extend({}, defaults, options);
        options.maxLevel = 0;
        var o = new tree(this, options);
        o.init(options.data);
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
        init: function (data) {
            this.layout(data);
            this.fold();

            this.loadOpera();

            //禁止选中文本
            $(this.o).on('selectstart', function () {
                return false;
            });

            //只绑定一次的事件，写在此处
            if (this.isShowUnfoldPanel) {
                this.bindUnfoldPanelEvent();
            }
        },
        loadOpera: function () {
            //默认根展开节点
            this.autoUnfoldFirstRow();

            //添加checkbox操作
            if (this.is_checked) {
                this.textClick();
                this.checkBoxClick();

                var strArr = [];
                strArr.push('<div class="tree-opera">');
                if (this.is_checked) {
                    strArr.push('<span class="fonticon fonticon-checkbox"></span>');
                }
                strArr.push('</div>');
                this.str = strArr.join('');
            } else {
                this.nodeClick();
            }
            this.nodeEach();
        },
        refresh: function (data) {
            $(this.o).html('');
            this.index_i = 0;
            this.params = [];
            this.layout(data);
            this.loadOpera();
        },
        /*
         * 生成树
         * @param array data
         */
        layout: function (data) {
            if (data.length) {
                var html = [];
                var blank_i = 0;
                this.createTreeNode(data, html, blank_i);
                if (this.isShowUnfoldPanel) {
                    this.createUnfoldPanel();
                }
                $(this.o).append(html.join(''));
            }
        },
        //存储数据
        saveData: function (data) {
            var temp = {};
            for (x in data) {
                if (x === 'childNode') {
                    if (data.childNode.length) {
                        temp.hasChildNode = true;
                        temp.childNode = [];
                        for (var i = 0; i < data.childNode.length; i++) {
                            var sonTemp = {};
                            for (var j in data.childNode[i]) {
                                if (j != 'childNode') {
                                    sonTemp[j] = data.childNode[i][j];
                                }
                            }
                            temp.childNode.push(sonTemp);
                        }
                    } else {
                        temp.hasChildNode = false;
                    }
                } else {
                    temp[x] = data[x];
                }
            }
            this.params.push(temp);
        },
        /*
         * 创建树节点
         * @param json nodes 外部数据
         * @param array html
         * @param number blank_i 填充空白数
         */
        createTreeNode: function (nodes, html, blank_i, level) {
            if (!level) {
                level = 1;
            }
            if (level > this.maxLevel) {
                this.maxLevel = level;
            }
            html.push(this.createNodesWrapBegin());
            var len = nodes.length;
            for (var i = 0; i < len; i++) {
                html.push(this.createNodeBegin(level));
                this.saveData(nodes[i]);
                if (this.filter) {
                    this.filter(this.params[this.index_i]);
                }
                var hasChild=nodes[i].childNode && nodes[i].childNode.length > 0;
                //节点禁用
                //增加最下层标识，仅处理首次渲染，后面方法需额外修复
                if (this.params[this.index_i].disable) {
                    html.push('<div class="tree-node disable'+(hasChild?'':' leaf')+'">');
                } else {
                    html.push('<div class="tree-node'+(hasChild?'':' leaf')+'">');
                }

                //添加空白
                if (blank_i) {
                    for (var j = 0; j < blank_i; j++) {
                        html.push('<span class="tree-blank"></span>');
                    }
                }
                //添加折叠图标
                if (hasChild) {
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
                    this.createTreeNode(nodes[i].childNode, html, empty_i, level + 1);
                }
                html.push(this.createNodeEnd());
            }
            ;
            html.push(this.createNodesWrapEnd());
        },
        createNodeBegin: function (level) {
            return '<li class="tree-item" level="' + level + '">';
        },
        createNodeEnd: function () {
            return '</li>';
        },
        /*
         * 节点文本
         * @param josn data
         */
        createNodeText: function (data, data_index) {
            return '<span class="tree-text" data-index="' + data_index + '">' + data.treeText + '</span>';
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
                if (dataJson.hasOwnProperty("disable") && dataJson.disable) {
                    return false;
                }
                $(_this.o).find('.tree-node').removeClass('node-bg');
                $(this).addClass('node-bg');
                if (_this.iClick) {
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
            if (this.iClick) {
                this.iClick($node[0], $nodeText[0], dataJson);
            }

            return false;
        },
        //添加复选框
        addCheckbox: function (o, str) {
            $(o).append(str);
        },
        //节点遍历
        nodeEach: function () {
            var _this = this;
            var $node = $(this.o).find('.tree-node');
            if ($node.length) {
                $node.each(function (i, el) {
                    if (_this.is_checked || _this.nodeOpera || (_this.nodeMouseover && _this.nodeMouseout)) {
                        _this.addOpera(el);
                    }
                });
            }
        },
        /*
         * 添加节点操作按钮
         * @param Dom o ('.tree-node')
         */
        addOpera: function (o) {
            var _this = this;
            if (this.is_checked) {
                this.addCheckbox(o, this.str);
            } else if (this.nodeOpera) {
                var data_index = $(o).find('.tree-text').attr('data-index');
                var data = _this.params[data_index];
                this.nodeOpera(data, o);
            } else if (this.nodeMouseover && this.nodeMouseout) {
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
            }
        },
        /*
         * 创建树节点
         * @param string html 单个节点html
         * @param number len 空白数
         * @param josn data 外部数据
         * @param number 当前层级
         */
        addHtml: function (html, len, data, level) {
            html.push('<li class="tree-item" level="' + level + '"><div class="tree-node">');
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
            this.refreshUnfoldPanelByNode($item, 1);
            var len = $item.children().length;
            var html = [];
            var curLevel = parseInt($($item).attr('level')) + 1;
            if (len > 1) {
                var blank_len = $(o).find('.tree-blank').length + 2;
                this.addHtml(html, blank_len, data, curLevel);
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
                this.addHtml(html, blank_len, data, curLevel);
                html.push('</ul>');
                $item.append(html.join(''));
                $item.addClass('open');
                $(o).find('.tree-text').prev().remove();

                $(o).find('.tree-text').before('<i class="tree-arrow"></i>');
            }
            var $treeNode = $item.find('> .tree-list > .tree-item:last > .tree-node');
            if ((this.nodeMouseover && this.nodeMouseout) || this.nodeOpera) {
                if ($treeNode.length) {
                    this.addOpera($treeNode[0]);
                }
            }
            //节点排序
            if (this.enableSort && this.sortField !== null) {
                this.sortBefore($treeNode, data, 'add');
                this.sort($treeNode, this.sortField, this.sortMode);
            }
            return $treeNode;
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
            //节点排序
            if (this.enableSort && this.sortField !== null) {
                this.sortBefore(o, dataJson, 'update');
                this.sort(o, this.sortField, this.sortMode);
            }
        },
        /*
         * 删除节点
         * @param Dom o 树节点('.tree-node')
         */
        _removeNode: function (o) {
            var $currItem = $(o).parent();
            this.refreshUnfoldPanelByNode($currItem, -1);
            var len = $currItem.siblings().length;
            $prevItem = null;
            if (len === 0) {
                var $currList = $currItem.parent();
                $prevItem = $currList.parent();
                $prevItem.find('.tree-arrow').before('<span class="tree-blank"></span>').remove();
                $currList.remove();
            } else {
                $prevItem = $currItem.prev();
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
            var data = this.params[data_index];
            if (data && !data.disable) {
                $(this.o).find('.tree-node').removeClass('node-bg');
                $node.addClass('node-bg');
                this.unfold($node[0]);
                this.posScrollBar($node[0]);
                if (this.iClick) {
                    this.iClick($node[0], $nodeText[0], data);
                }
            }
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
            var tree_h = $(this.o).outerHeight();      //树的高度(包含padding)
            var tree_scroll_top = $(this.o).scrollTop();
            var tree_top = $(this.o).offset().top;
            var o_h = $(o).height();                //节点高度
            var o_top = $(o).offset().top;
            var m_y = Math.ceil((tree_h) / 2);            //中心位置

            var result = o_top - tree_top + o_h - tree_h;

            if (result > 0) {
                $(this.o).animate({
                    scrollTop: Math.floor(tree_scroll_top + tree_h + result - m_y) + 'px'
                }, 500);
            }
        },
        refreshUnfoldPanelByNode: function (node, levelStep) {
            if (node && $(node).attr('level')) {
                var isRefresh = false;
                var iTempMaxLevel = parseInt($(node).attr('level')) + levelStep;
                if (iTempMaxLevel > this.maxLevel) {
                    isRefresh = true;
                } else if (iTempMaxLevel < this.maxLevel) {
                    //判断该级别的节点
                    if ($(this.o).find("li[level='" + (iTempMaxLevel + 1) + "'],[level='" + (iTempMaxLevel + 2) + "']").length === 1) {
                        isRefresh = true;
                    }
                }
                if (isRefresh) {
                    this.maxLevel = iTempMaxLevel;
                    this.createUnfoldPanel();
                }
            }
        },
        createUnfoldPanel: function () {
            if (this.isShowUnfoldPanel === false) {
                return;
            }
            if (this.maxLevel <= 0) {
                return;
            }
            $(this.o).find('.unfold-panel').remove();
            var elArray = ['<div class="unfold-panel" style="display: inline-block;">', '<ul>'];
            for (var i = 1; i <= this.maxLevel; i++) {
                elArray.push('<li style="list-style: none;float: left;margin: 0px 10px;"><a class="item" href="javascript:void(0);" unfoldLevel="' + i + '">' + i + '</a></li>')
            }
            elArray.push('</ul></div>');
            $(this.o).prepend(elArray.join(''));
        },
        bindUnfoldPanelEvent: function () {
            var _this = this;
            $(this.o).on('click', '.unfold-panel .item', function () {
                if ($(this).attr('unfoldLevel')) {
                    console.log('s');
                    _this.unfoldByLevel(parseInt($(this).attr('unfoldLevel')));
                }
            });
        },
        //默认展开第一级
        autoUnfoldFirstRow: function () {
            var $firstNode = $(this.o).children('.tree-list').children('.tree-item:first');
            $firstNode.addClass('open');
        },
        unfoldByLevel: function (level) {
            if (level) {
                if (!$(this.o).has("li[level='" + level + "']")) {
                    level = 1;
                }
                $(this.o).find('li').removeClass('open');
                for (var i = 0; i < level - 1; i++) {
                    $(this.o).find("li[level='" + (i + 1) + "']").addClass('open');
                }
            }
        },
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
                var $node = $item.children('.tree-node');
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
        },
        /**
         * 排序
         * @param node 树节点('.tree-node')
         * @param sortField 排序字段
         * @param sortMode 排序方式 ASC升序 DESC降序
         */
        sort: function (node, sortField, sortMode) {
            var _this = this;
            sortMode = sortMode.toUpperCase();
            var parent = $(node).parent().parent();
            //获取同级节点
            var sortArray = [];
            $(node).parent().parent().children('li').each(function (i, item) {
                var dIdx = parseInt($(item).find('.tree-text').attr('data-index'));
                sortArray.push({key: _this.params[dIdx][sortField], item: $(item)});
            });
            //排序
            sortArray.sort(function (x, y) {
                    if (sortMode === 'ASC') {
                        return x.key > y.key ? 1 : -1;
                    } else if (sortMode === 'DESC') {
                        return x.key > y.key ? -1 : 1;
                    }
                    return -1;
                }
            );
            $(sortArray).each(function (i, d) {
                $(d.item).appendTo(parent);
            });
            return sortArray;
        }
    }
})
;

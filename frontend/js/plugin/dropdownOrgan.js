/*
 * 单选操作插件(用于选择部门)
 * yuanl02 2015-4-1
 */
define(function (require, exports, module) {
    require('./tree');

    var objArr = [];  //对象数组
    var $curr;        //当前下拉框
    var isMatch;      //是否匹配

    $.fn.dropdownOrgan = function (options) {
        var defaults = {
            cName: 'open',
            defaultParams: []
        }
        var options = $.extend({}, defaults, options);
        var o = new dropdownOrgan(this, options);
        o.init();
        return o;
    }

    $(document).on('click', function (e) {
        var el = e.target;
        isMatch = false;
        for (var i = 0; i < objArr.length; i++) {
            if (objArr[i].matchEl($(el))) {
                //判断是否已经有打开的下拉
                if ($curr) {
                    $curr.isOpen(false);
                }
                $curr = objArr[i];         //记录打开的下拉框
                $curr.isOpen(true);        //打开下拉框
                isMatch = true;
                return;
            }
        }
        if (!isMatch) {
            if ($curr) {
                $curr.isOpen(false);
            }
        }
    });

    function dropdownOrgan(o, v) {
        this.o = o;
        for (var i in v) {
            this[i] = v[i];
        }
    }

    dropdownOrgan.prototype = {
        init: function () {
            if ($(this.o).length !== 1 || $(this.o).attr('id') === "") {
                return;
            }
            //创建选中对象数组
            if (window.selectedOrganArray == null) {
                window.selectedOrganArray = [];
            }
            $(this.o).append('<ul class="msc-list"></ul><div class="js-tree"></div>');
            var $msc_list = $(this.o).find('.msc-list');
            var $tree = $(this.o).find('.js-tree');
            var curData = {id: $(this.o).attr('id'), data: []};
            $(this.defaultParams).each(function (i, item) {
                curData.data.push(item);
                $msc_list.append('<li class="msc-item"><span class="msc-text">' + item.text + '</span><span class="fonticon fonticon-remove" title="删除"></span></li>');
            });
            window.selectedOrganArray.push(curData);
            var _this = this;
            //加载树控件
            var treeDataUrl = Overall.path('/organization/manage/organ_tree');
            Util.ajaxEx({
                type: 'post',
                url: treeDataUrl,
                success: function (data) {
                    $tree.tree({
                        data: data,
                        iClick: function (n, o, d) {
                            if (d.auth) {
                                _this.setSelected(d);
                                /*
                                //添加HTML
                                $msc_list.html('<li class="msc-item"><span class="msc-text">' + d.treeText + '</span><span class="fonticon fonticon-remove" title="删除"></span></li>');
                                $msc_list.parent().removeClass(_this.cName);
                                _this.bindItemEvent($msc_list);
                                //添加到选中集合
                                var data = _this.getCurData();
                                data.data = [];
                                data.data.push({text: d.treeText, value: d.value});
                                */
                            }
                        }
                    });
                }
            });

            if (this.loadTree) {
                this.loadTree($tree, $msc_list);
            }
            this.bindItemEvent($msc_list);
            objArr.push(this);
        },
        getCurData: function () {
            var _this = this;
            var selectOrgan = $(window.selectedOrganArray).filter(function (i, item) {
                return item.id == $(_this.o).attr('id');
            });
            return selectOrgan[0];
        },
        bindItemEvent: function (msc_list) {
            var _this = this;
            $(msc_list).on('click', '.msc-item', function () {
                return false;
            });
            $(msc_list).on('click', '.fonticon-remove', function () {
                $(this).parent().remove();
                _this.getCurData().data = [];
                return false;
            });
        },
        matchEl: function ($el) {
            return $el[0] === this.o[0] || $el.parents('.multi-select-control')[0] == this.o[0];
        },
        isOpen: function (isCurr) {
            if (isCurr) {
                $(this.o).addClass(this.cName);
            } else {
                $(this.o).removeClass(this.cName);
            }
        },
        getSelectedValue: function () {
            var data = this.getCurData().data;
            var valueArray = [];
            for (var i = 0; i < data.length; i++) {
                valueArray.push(data[i].value);
            }
            return valueArray.join(',');
        },
        getSelectedText: function () {
            var data = this.getCurData().data;
            var valueArray = [];
            for (var i = 0; i < data.length; i++) {
                valueArray.push(data[i].text);
            }
            return valueArray.join('；');
        },
        setSelected: function (dataNew) {
            var _this = this;
            var $msc_list = $(this.o).find('.msc-list');

            if (dataNew && dataNew.value) {
                //添加HTML
                $msc_list.html('<li class="msc-item"><span class="msc-text">' + dataNew.treeText + '</span><span class="fonticon fonticon-remove" title="删除"></span></li>');
                $msc_list.parent().removeClass(_this.cName);
                _this.bindItemEvent($msc_list);
                //添加到选中集合
                var dataSel = _this.getCurData();
                dataSel.data = [];
                dataSel.data.push({text: dataNew.treeText, value: dataNew.value});
            } else {
                $msc_list.empty();
                $msc_list.parent().removeClass(_this.cName);
                _this.getCurData().data = [];
            }
        }
    };
});
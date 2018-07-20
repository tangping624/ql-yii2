$.event.special.valuechange = {
    teardown: function (namespaces) {
        $(this).unbind('.valuechange');
    },

    handler: function (e) {
        $.event.special.valuechange.triggerChanged($(this));
    },

    add: function (obj) {
        $(this).on('keyup.valuechange cut.valuechange paste.valuechange input.valuechange', obj.selector, $.event.special.valuechange.handler)
    },
    triggerChanged: function (element) {
        var current = element[0].contentEditable === 'true' ? element.html() : element.val()
            , previous = typeof element.data('previous') === 'undefined' ? element[0].defaultValue : element.data('previous')
        if (current !== previous) {
            element.trigger('valuechange', [element.data('previous')])
            element.data('previous', current)
        }
    }
}


define(function (require, exports, module) {
//    require('/modules/js/public/plugin/tree.js');
    require('overall.js');
     

    /**
     * 用户列表
     * @type {grid}
     */
    var curUserGrid = null;

   

    /**
     * 已选择的用户集合
     * @type {Array}
     */
    var selectedArray = [];


    /**
     * 用户列表选择框样式
     * @type {string}
     */
    var userListCheckedControlClass = 'fonticon-checkbox';

    /**
     * 用户列表选择框选中样式
     * @type {string}
     */
    var userListCheckedClass = 'selected';

    /**
     * 输入查询timeout
     * @type {Timeout}
     */
    var inputSelectControlTimeOut = null;

    /**
     * 查询延时(ms)
     * @type {number}
     */
    var inputSelectControlTimeOutDelay = 300;

    /**
     * 上次查询是否有查询到结果
     * @type {boolean}
     */
    var hasISCResult = false;

    /**
     * 关键字
     * @type {string}
     */
    var perISCKeyword = "";

    /**
     * 已选面高度
     * @type {number}
     */
    var selectedUserPanelHeight = 0;
    /**
     * 表格面板top值
     * @type {number}
     */
    var gridTop = 0;

    /**
     * 高度值差
     * @type {number}
     */
    var perHeight = 0;

    /**
     * 预选项当前索引
     * @type {number}
     */
    var curISCISelectedIndex = -1;

    /**
     * 预选项总数
     * @type {number}
     */
    var curISCICount = 0;

    /**
     * 加载组织机构树
     */
//    function loadOrganTree() {
//        var treeDataUrl = Overall.path('/organization/manage/organ-tree');
//        Util.ajaxEx({
//            type: 'post',
//            url: treeDataUrl,
//            success: function (data) {
//                if (data.length > 0) {
//                    curSelOrgId = data[0].disable === false ? (data[0].value) : (data[0].childNode.length > 0 && data[0].childNode[0].disable === false ? data[0].childNode[0].value : '');
//                }
//                curOrganTree = $('.js-tree').tree({
//                    data: data,
//                    iClick: function (n, o, d) {
//                        //点击后刷新列表
//                        if (d.disable === false) {
//                            curSelOrgId = d.value;
//                            if (curUserGrid) {
//                                curUserGrid.refresh();
//                            } else {
//                                //加载用户列表
//                                loadUserList();
//                            }
//                        }
//                    }
//                });
//                curOrganTree._lockRowByValue(curSelOrgId);
//            }
//        });
//    }

    /**
     * 加载用户列表
     */
    function loadUserList() {
        var listDataUrl = Overall.path('/system/user/user-list-account');
        seajs.use('grid', function () {
            curUserGrid = $('#user_grid').grid({
                url: listDataUrl,     // 数据加载地址
                idField: 'id',                             // 主键
                templateid: 'user_gridrow_template',            // 网格行模板
                pagesize: 100,                              // 每页记录数
                emptyText: '无相关用户！',             // 数据为空提示
                scrollLoad: true,
                scrollWrapId: 'user_grid',
                filter: function (model) {                   // 网格渲染前处理数据
                    model.set('selected', isExistsSelectedArray(model.get('id')) ? userListCheckedClass : '');
                },
                queryParams: function () {                   // 设置查询参数
                    return  'enabled=1&level='+ userLevel;
                }
            })
        });
    }

    /**
     * 绑定用户列表中的复选框事件
     */
    function bindUserListCheckedEvent() {
        $("#user_grid").on('click', '.' + userListCheckedControlClass, function () {
            var isAdd = $(this).hasClass(userListCheckedClass) === false;
            if (isAdd) {
                addSelectedItem($(this).attr('uname'), $(this).attr('uid'), true);
            } else {
                removeSelectedItem($(this).attr('uid'));
                $(this).removeClass(userListCheckedClass);
            }
        });
    }

    /**
     * 绑定已选面板中选中项移除事件
     */
    function bindSelectedPanelRemoveItemEvent() {
        $("#selected_user_panel").on('click', '.fonticon-remove', function () {
            var index = $(this).parent().parent().children().index($(this).parent());
            if (index != -1) {

                var curValue = selectedArray[index].value;
                selectedArray.splice(index, 1);
                //删除EL
                $(this).parent().remove();
                //取消列表选中样式
                $("#user_grid").find("." + userListCheckedControlClass + "[uid='" + curValue + "']").each(function (i, item) {
                    $(item).removeClass(userListCheckedClass);
                });
                setGridHeight();
                focusInputSelectControl();
            }
        });
    }

    /**
     * 绑定输入查询事件
     */
    function bindInputSelectControlEvent() {
        $("#input_select input").bind('valuechange', function (e, previous) {
            var inputText = $(this).val().replace(/(^\s*)|(\s*$)/g, "");
            clearTimeout(inputSelectControlTimeOut);
            inputSelectControlTimeOut = setTimeout(function () {
                if (inputText) {
                    searchUser(inputText);
                } else {
                    hideInputSelectResultPanel();
                }
            }, inputSelectControlTimeOutDelay)
        });

        //绑定 ↑↓及回车键事件
        $("#input_select input").bind('keydown', function (e) {
            if ((e.keyCode !== 13 && e.keyCode !== 38 && e.keyCode !== 40)
                || $("#iscr_items").is(":visible") === false
                || curISCICount === 0) {
                return;
            }
            if (e.keyCode === 13) {
                $("#iscr_items>li[class*='curr']").eq(0).click();
                return;
            } else {
                var curIndex = getISCICurMoveIndex(e.keyCode === 40 ? 'down' : 'up');
                var curItem = $("#iscr_items>li").eq(curIndex);
                curItem.siblings().removeClass('curr');
                curItem.removeClass('curr').addClass('curr');
            }
        });
    }


    /**
     * 重新设置索引与预选项总数
     */
    function resetISCISIndex() {
        curISCISelectedIndex = -1;
        curISCICount = $("#iscr_items>li[class*='sr-item']").length;
    }

    /**
     * 获取当前移动选中项的索引
     * @param mode
     * @returns {number}
     */
    function getISCICurMoveIndex(mode) {
        if (mode === 'up') {
            curISCISelectedIndex--;
            if (curISCISelectedIndex <= -1) {
                curISCISelectedIndex = curISCICount - 1;
            }
        }
        else if (mode === 'down') {
            curISCISelectedIndex++;
            if (curISCISelectedIndex >= curISCICount) {
                curISCISelectedIndex = 0;
            }
        }
        else {
            curISCISelectedIndex = curISCISelectedIndex <= -1 ? 0 : curISCISelectedIndex;
        }
        return curISCISelectedIndex;
    }

    /**
     * 绑定查找结果选项点击事件
     */
    function bindInputSelectControlItemEvent() {
        $("#iscr_items").on('click', '.sr-item', function () {
            var _this = $(this);
            if (_this.attr("uid") === "" || _this.attr("uname") === "") {
                return;
            }
            addSelectedItem(_this.attr("uname"), _this.attr("uid"), true);
            //点击后隐藏预选项面板
            hideInputSelectResultPanel();
        });
        $("#iscr_items").on('mouseover', '.sr-item', function () {
            $(this).siblings().removeClass('curr');
            $(this).removeClass('curr').addClass('curr');
        });
        $("#iscr_items").on('mouseout', '.sr-item', function () {
            $(this).removeClass('curr');
        });
    }

    /**
     * 绑定页面按钮事件
     */
    function bindBtnsEvent() {
        $('#btn_ok').on('click', confirmEvent);
        $('#btn_cancel').on('click', cancelEvent);
    }

    /**
     * 确定按钮事件
     */
    function confirmEvent() {
        var result = {};
        result.data = selectedArray;
        result.getSelectValue = getSelectValue;
        result.getSelectText = getSelectText;
        //callback 返回true 则关闭dialog
        if (parent && parent.DialogAddUser && parent.DialogAddUser.ok(result)) {
            parent && parent.DialogAddUser && parent.DialogAddUser.dialog.close().remove();
        }
    }

    /**
     * 给返回结果添加取值函数
     * @returns {Array}
     */
    function getSelectValue() {
        var tempArray = [];
        for (var i = 0; i < this.data.length; i++) {
            tempArray.push(this.data[i].value);
        }
        return tempArray;
    }

    /**
     * 给返回结果添加取名称函数
     * @returns {Array}
     */
    function getSelectText() {
        var tempArray = [];
        for (var i = 0; i < this.data.length; i++) {
            tempArray.push(this.data[i].text);
        }
        return tempArray;
    }

    /**
     * 取消按钮事件
     */
    function cancelEvent() {
        parent && parent.DialogAddUser && parent.DialogAddUser.dialog.close().remove();
    }

    /**
     * 查找用户
     * @param text 用户名称或账号
     */
    function searchUser(text) {
        //如果没有上一结果没有搜索到，则直接显示没有结果(减少请求)
        if (perISCKeyword !== "" && text.indexOf(perISCKeyword) === 0 && hasISCResult === false) {
            //显示没有搜索到结果
            perISCKeyword = text;
            showInputSelectNotResultPanel();
            return;
        }
        perISCKeyword = text;
        var searchUrl = Overall.path('/system/user/top-search?level='+userLevel);
        Util.ajaxEx({
            type: 'post',
            data: {keyword: text},
            url: searchUrl,
            success: function (data) {
                //加载搜索列表面板
                if (data.length > 0) {
                    hasISCResult = true;
                    showInputSelectResultPanel(data);
                } else {
                    hasISCResult = false;
                    showInputSelectNotResultPanel();
                }
            }
        });
    }

    /**
     * 显示匹配到的用户结果
     * @param data
     */
    function showInputSelectResultPanel(data) {
        var items = $("#iscr_items");
        items.children().remove();
        $(data).each(function (i, item) {
            items.append('<li class="sr-item" uid="' + item.id + '" uname="' + item.name + '">' + item.name + '（' + item.account + '）</li>')
        });
        $("#input_select_control_result").css({'top': ($(".multi-select-control").outerHeight()) + 'px'}).show();
        resetISCISIndex();
    }

    /**
     * 显示未匹配到用户结果
     * @param data
     */
    function showInputSelectNotResultPanel(data) {
        var items = $("#iscr_items");
        items.children().remove();
        items.append('<li>无搜索结果</li>');
        $("#input_select_control_result").css({'top': ($(".multi-select-control").outerHeight()) + 'px'}).show();
    }

    /**
     * 隐藏用户结果面板
     */
    function hideInputSelectResultPanel() {
        if ($("#iscr_items").is(":visible")) {
            perISCKeyword = '';
            $._data($("#input_select input")[0]).data.previous = '';
            $("#input_select input").val('');
            $("#input_select_control_result").hide();
        }
    }

    /**
     * 输入选择控件获得焦点
     */
    function focusInputSelectControl() {
        $("#input_select input").focus();
    }


    /**
     * 添加已选项
     * @param text 用户名称
     * @param value 用户ID
     * @returns {boolean}
     */
    function addSelectedItem(text, value, isSelectChecked) {
        if (isExistsSelectedArray(value)) {
            return false;
        } else {
            if (isSelectChecked && parent && parent.DialogAddUser && parent.DialogAddUser.selectedCheck(value) === false) {
                return false;
            }
            selectedArray.push({text: text, value: value});
            //输入选择控件
            var input_select_control = $("#input_select");
            if (input_select_control) {
                //创建EL
                input_select_control.before('<li class="msc-item"><span class="msc-text">' + text + '</span><span class="fonticon fonticon-remove" title="删除"></span></li>');
                focusInputSelectControl();
            }
            //添加列表选中样式
            $("#user_grid").find("." + userListCheckedControlClass + "[uid='" + value + "']").each(function (i, item) {
                if ($(item).hasClass(userListCheckedClass) === false) {
                    $(item).addClass(userListCheckedClass);
                }
            });
            //重新计算用户列表高度
            setGridHeight();
            return true;
        }
    }


    /**
     * 判断是否已选
     * @param value
     * @returns {boolean}
     */
    function isExistsSelectedArray(value) {
        var isExists = false;
        if (selectedArray && selectedArray.length > 0) {
            for (var i = 0; i < selectedArray.length; i++) {
                if (selectedArray[i].value.toLowerCase() === value.toLowerCase()) {
                    isExists = true;
                    break;
                }
            }
        }
        return isExists;
    }

    /**
     * 根据值删除已选项
     * @param value
     * @returns {boolean}
     */
    function removeSelectedItem(value) {
        var result = false;
        for (var i = 0; i < selectedArray.length; i++) {
            if (selectedArray[i].value.toLowerCase() === value.toLowerCase()) {
                selectedArray.splice(i, 1);
                $($("#selected_user_panel li")[i]).remove();
                setGridHeight();
                result = true;
                break;
            }
        }
        return result;
    }

    /**
     * 加载默认值
     */
    function initDefaultParams() {
        //附加默认选中参数
        if (parent && parent.DialogAddUser && parent.DialogAddUser.defaultParams) {
            var data = parent.DialogAddUser.defaultParams;
            for (var i = 0; i < data.length; i++) {
                addSelectedItem(data[i].text, data[i].value, false);
            }
        }
    }


    /**
     * 初始化当前控件的相关高度
     */
    function initControlHeight() {
        selectedUserPanelHeight = $("#selected_user_panel").outerHeight();
        //gridTop = $(".public-panel").position().top;
        gridTop = parseInt($(".public-panel").css('top'));
    }

    /**
     * 重设grid高度
     */
    function setGridHeight() {
        var cur = selectedUserPanelHeight - $("#selected_user_panel").outerHeight();
        if (cur != perHeight) {
            $(".public-panel").css("top", (gridTop - cur) + 'px');
            perHeight = cur;
        }
    }


    /**
     * 绑定document点击事件，点击不在文本框或预选项面板，则隐藏预选项面板
     */
    function bindDocumentClickEvent() {
        $(document).on('click', function (e) {
            if (e.target != $("#input_select input")[0] && $(e.target).hasClass('sr-item') == false) {
                hideInputSelectResultPanel();
            }
        });
    }

    /**
     * 设置搜索框暗提示
     */
    function initInputSelectPlaceholder() {

        //没有默认选中项添加输入框暗提示
        if (selectedArray.length === 0) {
            $("#input_select input").attr('placeholder', '输入姓名或帐号搜索用户');
        }
    }

    /**
     * 初始化方法
     */
    function init() {
        initControlHeight();
        bindInputSelectControlEvent();
        bindInputSelectControlItemEvent();
        bindSelectedPanelRemoveItemEvent();
        bindUserListCheckedEvent();
        bindBtnsEvent();
        bindDocumentClickEvent(); 
        initDefaultParams();
        initInputSelectPlaceholder(); 
        //加载用户列表
        loadUserList();
   
    }

    return {
        init: init
    };
})
;
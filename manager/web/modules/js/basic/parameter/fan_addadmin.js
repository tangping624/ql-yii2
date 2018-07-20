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
    var form = require('/frontend/js/plugin/form.js');
//    require('/modules/js/public/plugin/tree.js');
    require('overall.js'); 
    var _id = $("#id").val();
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
 
//    /**
//     * 绑定用户列表中的复选框事件
//     */
//    function bindUserListCheckedEvent() {
//        $("#user_grid").on('click', '.' + userListCheckedControlClass, function () {
//            var isAdd = $(this).hasClass(userListCheckedClass) === false;
//            if (isAdd) {
//                addSelectedItem($(this).attr('uname'), $(this).attr('uid'));
//            } else {
//                removeSelectedItem($(this).attr('uid'));
//                $(this).removeClass(userListCheckedClass);
//            }
//        });
//    }

    /**
     * 绑定已选面板中选中项移除事件
     */
    function bindSelectedPanelRemoveItemEvent() {
        $("#selected_user_panel").on('click', '.fonticon-remove', function () {
            var index = $(this).parent().parent().children().index($(this).parent());
            if (index != -1) { 
                //删除EL
                $(this).parent().remove(); 
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
            addSelectedItem(_this.attr("uname"), _this.attr("uid"));
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
        var searchUrl = Overall.path('/basic/fan-admin/top-search-fan');
        Util.ajaxEx({
            type: 'post',
            data: {nick_name: text},
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
            items.append('<li class="sr-item" uid="' + item.id + '" uname="' + item.nick_name + '">' + item.nick_name + '</li>')
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
        $("#input_select").show();
        $("#input_select input").focus();
    }
   function focusInputSelectControlHide() {
        $("#input_select").hide();
        $("#input_select input").val('');
    }

    /**
     * 添加已选项
     * @param text 用户名称
     * @param value 用户ID
     * @returns {boolean}
     */
    function addSelectedItem(text, value) { 
            //输入选择控件
            var input_select_control = $("#input_select");
            if (input_select_control) {
                //创建EL
                input_select_control.before('<li class="msc-item"><span class="msc-text">' + text + '</span><span class="fonticon fonticon-remove" title="删除"></span></li>');
                focusInputSelectControlHide(); 
            } 
            _nick_name=text;
             _fanid=value;
            return true;
 
    }
 

    /**
     * 根据值删除已选项
     * @param value
     * @returns {boolean}
     */
    function removeSelectedItem(value) { 
        $($("#selected_user_panel li")).remove(); 
        _nick_name='';
        _fanid='';
       return true;
    }

    /**
     * 加载默认值
     */
    function initDefaultParams() {
        if(_fanid.length>0){
            addSelectedItem(_nick_name, _fanid); 
        } 
    }


    /**
     * 初始化当前控件的相关高度
     */
    function initControlHeight() {
        selectedUserPanelHeight = $("#selected_user_panel").outerHeight(); 
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
        $("#input_select input").attr('placeholder', '输入粉丝昵称搜索用户'); 
    }
     /**
     * 消息提示
     * @param message
     * @param isNormal
     */
    function showMessage(message, isNormal) {
        var parent = window.parent || window;

        parent.$.topTips({
            mode: isNormal ? 'normal' : 'warning',
            tip_text: message
        });
    }

    /**
     * 功能：保存用户数据
     * @param data 用户表单信息
     * */
    var saveAdmin = function (data) {
        var result = false; 
        Util.ajaxEx({
            type: 'post',
            data: data,
            url: Overall.path('/basic/fan-admin/save-admin?id='+ _id),
            async: false,
            success: function (data) {
                if (data.result) {
                    result = true; 
                    showMessage(data.msg, true);
                } else {
                    showMessage(data.msg, false);
                }
            }
        });
        return result;
    }
    getData=function(){
        return {'name':$('#name').val(),
                 'mobile':$("#mobile").val(),
                 'fanid':_fanid
            };
    }
    //模块的初始化
   function bindBtnsEvent() {
        //保存新增
        $('#user_form').form({
            submitbtns: [$('#btn_ok')],
            rules: [
                {
                    id: 'name',
                    required: true,
                    msg: {'required': '请输入姓名', limit: '姓名最多20个字符'},
                    fun: function (field, ele) {
                        if (field.value.length > 20) {
                            return 'limit';
                        }
                    }
                },
                {
                    id: 'mobile',
                    required: true,
                    msg: {'required': '请输入手机号码',validator: '手机号码格式错误'},
                    fun: function (field, ele) {
                        if (field.value && !(/^0?1[3|4|5|8][0-9]\d{8}$/gi.test(field.value))) {
                            return 'validator';
                        }
                    }
                } 
            ],
            validate: function () { //添加其他验证规则 
                if(_fanid==""){
                    showMessage("请选择粉丝");
                    return false;
                }
                return true;
            },
            submit: function () { 
                data=getData();
                if (saveAdmin(data)) { 
                    //点确定后操作
                    parent && parent.DialogAddUser && parent.DialogAddUser.ok();
                     
                }
            } 
            
        });
    

        //对话框取消按钮
        $('#btn_cancel').bind('click', function () {
            //取消
            parent && parent.DialogAddUser && parent.DialogAddUser.cancel();
        }); 
        
       
//
//    /**
//     * 确定按钮事件
//     */
//    function confirmEvent() {
//        var result = {};
//         var searchUrl = Overall.path('/basic/fan-admin/save-admin');
//         Util.ajaxEx({
//            type: 'post',
//            data: {nick_name: text},
//            url: searchUrl,
//            success: function (data) {
//                //加载搜索列表面板
//                if (data.length > 0) {
//                    hasISCResult = true;
//                    showInputSelectResultPanel(data);
//                } else {
//                    hasISCResult = false;
//                    showInputSelectNotResultPanel();
//                }
//            }
//        });
//       
//        //callback 返回true 则关闭dialog
//        if (parent && parent.DialogAddUser && parent.DialogAddUser.ok(result)) {
//            parent && parent.DialogAddUser && parent.DialogAddUser.dialog.close().remove();
//        }
//    }
// 
//
//    /**
//     * 取消按钮事件
//     */
//    function cancelEvent() {
//        parent && parent.DialogAddUser && parent.DialogAddUser.dialog.close().remove();
//    }
    }
    /**
     * 初始化方法
     */
    function init() {
        initControlHeight();
        bindInputSelectControlEvent();
        bindInputSelectControlItemEvent();
        bindSelectedPanelRemoveItemEvent(); 
        bindBtnsEvent();
        bindDocumentClickEvent(); 
        initDefaultParams();
        initInputSelectPlaceholder();   
    }

    return {
        init: init
    };
})
;
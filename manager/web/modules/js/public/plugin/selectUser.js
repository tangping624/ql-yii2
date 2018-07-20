
define(function (require, exports, module) {
    require('/frontend/js/lib/dialog.js');

    var CSS =
        '.ui-dialog-header{background-color:#F4F5F9 !important;}' +
        '.ui-dialog-title{color:black;font-family: "Helvetica Neue","Hiragino Sans GB","Microsoft YaHei","\9ED1\4F53",Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal}' +
        '.ui-dialog-body{padding:0;}';

    $('head').append($('<style type="text/css">' + CSS + '</style>'));

    //在调用页面创建SelectOrgan对象
    var SelectUserProxy = window.SelectUserProxy = {
        dialog: null,//弹出框样式
        ok: function (result) {
        },
        defaultParams: null,//默认参数 array，
        selectedCheck: function (value) {
            return true;
        }//选中校验
    };
    var selectUser = function (args) {
        if ($.type(args) === 'string') {
            args = {params: args, title: ''};
        }

        var params = args.params || '';

        if ($.isPlainObject(params)) {
            params = $.param(params);
        }

        //附加回调函数
        SelectUserProxy.ok = args.callback;

        //附加选中校验事件
        if (args.selectedCheck) {
            SelectUserProxy.selectedCheck = args.selectedCheck;
        }

        //附加默认参数
        SelectUserProxy.defaultParams = args.defaultParams;
        var d = SelectUserProxy.dialog = $.dialog({
            url: '/organization/user/select' + '?' + params,
            title: args.title || '选择用户',
            id: 'select_user',
            width: args.width || 725,
            height: args.height || 560,
            onclose: function () {
                this.close().remove();
                var iframe = document.getElementsByName('select_user')[0];
                iframe && iframe.parentNode.removeChild(iframe);
            }
        }).showModal();
        return d;
    };
    exports.show = selectUser;
});
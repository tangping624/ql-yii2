
define(function (require, exports, module) {
    require('/frontend/js/lib/dialog.js');

    var CSS =
        '.ui-dialog-header{background-color:#F4F5F9 !important;}' +
        '.ui-dialog-title{color:black;font-family: "Helvetica Neue","Hiragino Sans GB","Microsoft YaHei","\9ED1\4F53",Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal}' +
        '.ui-dialog-body{padding:0;}';

    $('head').append($('<style type="text/css">' + CSS + '</style>'));

    //在调用页面创建SelectApp对象
    var SelectAppProxy = window.SelectAppProxy = {
        dialog: null,//弹出框样式
        ok: function (data) {
        },
        defaultParams: null//默认参数 array
    };
    var app = function (args) {
        if ($.type(args) === 'string') {
            args = {params: args, title: ''};
        }

        var params = args.params || '';

        if ($.isPlainObject(params)) {
            params = $.param(params);
        } 
        var url = '/system/account/select-app?accountId='+args.accountId; 
        //附加回调函数
        SelectAppProxy.ok = args.callback;

        //附加默认参数
        SelectAppProxy.defaultParams = args.defaultParams;

       

        var d = SelectAppProxy.dialog = $.dialog({
            url: url,
            title: args.title || '添加应用权限',
            id: 'select_app',
            width: args.width || 725,
            height: args.height || 420,
            onclose: function () {
                this.close().remove();
                var iframe = document.getElementsByName('select_app')[0];
                iframe && iframe.parentNode.removeChild(iframe);
            }
        }).showModal();
        return d;
    };
    exports.show = app;
});
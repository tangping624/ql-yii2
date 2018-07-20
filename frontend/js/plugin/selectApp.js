
define(function (require, exports, module) {
    require('../lib/dialog.js');

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

        //附加回调函数
        SelectAppProxy.ok = args.callback;

        //附加默认参数
        SelectAppProxy.defaultParams = args.defaultParams;

        var url = '/authority/manage/select_app';

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
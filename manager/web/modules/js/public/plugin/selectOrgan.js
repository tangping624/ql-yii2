
define(function (require, exports, module) {
    require('/frontend/js/lib/dialog.js');

    var CSS =
        '.ui-dialog-header{background-color:#F4F5F9 !important;}' +
        '.ui-dialog-title{color:black;font-family: "Helvetica Neue","Hiragino Sans GB","Microsoft YaHei","\9ED1\4F53",Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal}' +
        '.ui-dialog-body{padding:0;}';

    $('head').append($('<style type="text/css">' + CSS + '</style>'));

    //在调用页面创建SelectOrgan对象
    var SelectOrganProxy = window.SelectOrganProxy = {
        dialog: null,//弹出框样式
        ok: function (data) {
        },
        defaultParams: null//默认参数 array
    };
    var organ = function (args) {
        if ($.type(args) === 'string') {
            args = {params: args, title: ''};
        }

        var params = args.params || '';

        if ($.isPlainObject(params)) {
            params = $.param(params);
        }

        //附加回调函数
        SelectOrganProxy.ok = args.callback;

        //附加默认参数
        SelectOrganProxy.defaultParams = args.defaultParams;

        var url = (args.params && args.params.range === 'company')
            ? '/organization/manage/select-company' + '?' + params
            : '/organization/manage/select' + '?' + params;

        var d = SelectOrganProxy.dialog = $.dialog({
            url: url,
            title: args.title || '选择从属部门',
            id: 'select_organ',
            width: args.width || 725,
            height: args.height || 410,
            onclose: function () {
                this.close().remove();
                var iframe = document.getElementsByName('select_organ')[0];
                iframe && iframe.parentNode.removeChild(iframe);
            }
        }).showModal();
        return d;
    };
    exports.show = organ;
});
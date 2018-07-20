define(function(require, exports, module) {               
    require('../lib/dialog.js');

     // 对子页面访问

    var Proxy = window.ProxyUser = {
        dialog: null,
        ok: function(data) {
            O.emit('select:user', data);
        },
        // 刷新iframe页面高度
        refrushHeight: function(height) {
            document.getElementById('content:select_user').style.height = 640 + height + 'px';
        },
        cancel: function() {
            this.dialog && this.dialog.close().remove();
        }
    };

    O.on('select:user', function() {
        Proxy.cancel();
    });

    var user = function(args) {
        if($.type(args) === 'string') {
            args = {params: args, title: ''};
        }

        var params = args && args.params || '';

        if($.isPlainObject(params)) {
            params = $.param(params);
        }
        ProxyUser.defaultParams = args && args.defaultParams || [];
        if (args && args.multiSelect != undefined) {
            ProxyUser.multiSelect = args.multiSelect;
        }
        else {
            ProxyUser.multiSelect = true;
        }
        var d = ProxyUser.dialog = $.dialog({
            url: '/widgets/selectuser' + '?___token=' +  O.getToken() + (params == '' ? '' : '&'+params),
            title: args.title || '选择用户',
            id: 'select_user',
            width: 970,
            height: 640,
            onclose: function() {
                this.close().remove();
                var iframe = document.getElementsByName('select_user')[0];
                iframe && iframe.parentNode.removeChild(iframe);
            }
        }).showModal();

        return d;
    };

    exports.user = user;
});
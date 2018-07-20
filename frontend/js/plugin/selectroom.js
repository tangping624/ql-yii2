define(function(require, exports, module) {
    require('../lib/dialog.js');

     // 对子页面访问
    var Proxy = window.ProxyRoom = {
        dialog: null,
        ok: function(data) {
            O.emit('select:room', data);
        }
    };

    O.on('select:room', function() {
        ProxyRoom.dialog && ProxyRoom.dialog.close().remove();
    });

    var room = function(args) {
        if($.type(args) === 'string') {
            args = {params: args, title: ''};
        }

        var params = args.params || '';

        if($.isPlainObject(params)) {
            params = $.param(params);
        }

        var d = ProxyRoom.dialog = $.dialog({
            url: '/widgets/selectroom' + '?' + params + '&___token=' + O.getToken(),
            title: args.title || '选择房产',
            id: 'select_fc',
            width: 780,
            height: 645,
            skin:'art-box',
            onclose: function() {
                this.close().remove();
                var iframe = document.getElementsByName('select_fc')[0];
                iframe && iframe.parentNode.removeChild(iframe);
            }
        }).showModal();

        return d;
    };

    exports.room = room;
});
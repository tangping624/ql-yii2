
define(function (require, exports, module) {
    var dialog = require('/frontend/js/lib/artDialog/src/dialog-plus.js');
    $.confirm = function (args) {
        var d = dialog({
            title: args.title || '温馨提示',
            padding: 10,
            width: 725,
            content: '<div class="prompt-popup">' +
            '<div class="prompt-popup-inner">' +
            '<div class="prompt-container prompt-delete">' +
            '<span class="fonticon fonticon-prompt-delete"></span>' +
            '<p class="prompt-tit">' + args.content + '</p>' +
            (args.tip ? '<p class="prompt-tips">' + args.tip + '</p>' : '') +
            '</div>' +
            '</div>' +
            '</div>',
            skin: 'confirm-dialog',
            button: [{
                value: '取消',
                callback: function () {
                    this.close();
                }

            }, {
                value: '确定',
                callback: function () {
                    if (args.ok) {
                        args.ok();
                    }
                    this.close();
                },
                autofocus: true
            }]
        });
        d.showModal();
        return d;
    }
});
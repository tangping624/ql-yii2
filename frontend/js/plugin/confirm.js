 
define(function (require, exports, module) {
    var dialog = require('../lib/artDialog/src/dialog-plus.js');
    $.confirm = function (args) {
        var d = dialog({
            title: args.title || '温馨提示',
            content: '<div class="art-box-content">' +
            '<div class="arttip-wrap clearfix">' +
            '<span class="arttip '+(args.icon ? args.icon : 'arttip-warn')+' pull-left"></span>' +
            '<div class="arttip-content pull-overflow"><p class="arttip-tit '+(args.tip?'':'arttip-only-tit')+'">' + args.content + '</p>' +
            (args.tip ? '<p class="arttip-tips">' + args.tip + '</p>' : '') +
            '</div></div>' +
            '</div>',
            skin: 'art-box',
            width: args.width||500,
            height: 'auto',
            button: [{
                value: '确定',
                callback: function () {
                    if (args.ok) {
                        return args.ok();
                    } else {
                        return true;
                    }
                },
                autofocus: true
            },{
                value: '取消',
                callback: function () {
                    return true;
                }
            }]
        });
        d.showModal();
        return d;
    };
});
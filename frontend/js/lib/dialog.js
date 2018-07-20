'use strict';
/*global define*/
define(function (require, exports, module) {
    var dialog = $.dialog = require('./artDialog/src/dialog-plus.js');
    require('tooltips');

    var tipsUtils = {
        _cls: {
            normal: 'msg-tips-pop tips-success',
            tips: 'msg-tips-pop tips-error',
            warning: 'msg-tips-pop tips-error'
        },
        _init: function (options) {
            var opts = $.extend(true, {mode: 'normal', delay: 1000}, options);
            if (opts.mode !== 'normal') {
                opts.delay = 5000;
            }
            return opts;
        },
        _getDom: function () {
            var tips = $('#js_tips_wrap');
            if (!tips.length) {
                tips = $('<div id="js_tips_wrap">').appendTo($('body'));
            }
            return tips;
        },
        _layout: function (dom, options) {
            dom.html($('<div class="tips-content">').html(options.tip_text));
            dom.removeClass().addClass(this._cls[options.mode]);
            dom.css({
                marginLeft: -Math.ceil(dom.outerWidth() / 2),
                marginTop: options.top ? (dom.addClass('pos-top') && 0) : -Math.ceil(dom.outerHeight() / 2)
            });
        },
        _display: function (visible, dom, options) {
            dom = dom || this._getDom();
            if (visible) {
                options = options || {};
                var delay = options.delay,
                    callback = options.callback || function () {
                        };
                if (delay) {
                    dom.hide();
                    dom.fadeIn(500);
                    this.delay = setTimeout(function () {
                        dom.fadeOut(800, function () {
                            callback.call(dom);
                        });
                    }, delay);
                } else {
                    dom.show();
                }
            } else {
                dom.hide();
            }
        },
        show: function (options) {
            clearTimeout(this.delay);
            var dom = this._getDom();
            options = this._init(options);
            this._layout(dom, options);
            this._display(true, dom, options);
        }
    };

    $.message = function (arg) {
        arg = arg || {};

        var btns = arg.btns || [
                ['取消', function () {
                    this.close();
                }],
                ['确定', function () {
                    this.close();
                }]
            ];

        var cfg = {
            title: '标题',
            padding: 10,
            width: 440,
            content: '内容',
            skin: 'art-dialog',
            cancelValue: '关闭',
            button: [{
                value: btns[0][0],
                callback: btns[0][1]

            }, {
                value: btns[1][0],
                callback: btns[1][1],
                autofocus: true
            }]
        };

        var d = dialog($.extend(cfg, arg));
        d.showModal();

        return d;
    };

    var CLOSE = '<span class="ui-dialog-close cust-dialog-close">×</span>';

    var loading_img = 'data:image/gif;base64,R0lGODlhgACAAKIEAPHx8eTk5NbW1v///////wAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh/wtYTVAgRGF0YVhNUDw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNS1jMDE0IDc5LjE1MTQ4MSwgMjAxMy8wMy8xMy0xMjowOToxNSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFRDY4MDIzNTM2QjYxMUU1OUE4NUY2MjU5ODU5QjZEQSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFRDY4MDIzNjM2QjYxMUU1OUE4NUY2MjU5ODU5QjZEQSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkVENjgwMjMzMzZCNjExRTU5QTg1RjYyNTk4NTlCNkRBIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkVENjgwMjM0MzZCNjExRTU5QTg1RjYyNTk4NTlCNkRBIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+Af/+/fz7+vn49/b19PPy8fDv7u3s6+rp6Ofm5eTj4uHg397d3Nva2djX1tXU09LR0M/OzczLysnIx8bFxMPCwcC/vr28u7q5uLe2tbSzsrGwr66trKuqqainpqWko6KhoJ+enZybmpmYl5aVlJOSkZCPjo2Mi4qJiIeGhYSDgoGAf359fHt6eXh3dnV0c3JxcG9ubWxramloZ2ZlZGNiYWBfXl1cW1pZWFdWVVRTUlFQT05NTEtKSUhHRkVEQ0JBQD8+PTw7Ojk4NzY1NDMyMTAvLi0sKyopKCcmJSQjIiEgHx4dHBsaGRgXFhUUExIREA8ODQwLCgkIBwYFBAMCAQAAIfkEBQUABAAsAAAAAIAAgAAAA/9Iutz+MMpJq7046827/6A2jENonmhGkmnruuv6znQXs3WuT/e4/8BFrxQs5obG5AypbBIAUMCH2RFYBU5NNOqhbq7X7GW7tfWq4LCYQuZyvJm0ei1pu0Xnr9xKn9jvGHAWe3N9EH9SeDd6e4Zsf4oxGoRYjn6QKnkYlJaPdpmLm4Sdnp8XghKUlaR1mBaoEZysl64UsA+qs6Vkp5oUsrq0ba++qaPBu2UVtw3AyK2mPMUQzs8RiIkSzAq51smAENsE1d6HiNKhseTlD+foOMaNdAH0ASHu2vDq8h9gH/X1QOALsS5Omg4AE36oBeIYGn4ZEkrswLAfRIMFKUjcyGH/WAo5HFQVwrCR4wZlLfwxEomw5ER21ESO1ODSJExuMmdyqPmyXE6dHngq9PaTjwuhAJEVXTUDaUBSS384pdcpapCpjoo2QZpV5hqeXTMmqRn24ryehsw6SgoV6M23cOPKnUu3rt27ePPq3cu3L9+l3XwCdshgcOBnhqslPqxrsTPHhBtDBrlgMmWlllXizGwUM2em4zgLzozLMkzSflOrXs26tevXsGPLnk279gzNlnBXvZyWdx/GYoA7YdlbLJCcZY3X0FrcaxCryYnrgJ6bOQ3qrLCf0C7550frRMF74B7e+3jzc8WLcj7JrcXOGKXHVx5TN8GDK+mX1r8fv4nI0OsBOIiAAdp3nm/tqTWggfMxGBKBBYKWoIT5OViZe5shuNJvEF5oYYb+1SVcYQp6WOJNynUI4oejnbgifPW5WJ6GDqj4Ioxv6WfjjRSyo6OMPMrF345B5jgkkDz26BmNMbJIIpLVEXljhE5GyWSTGNYoJYdQmljlk1fu1uWUDWbZXIgXbOnll1mMOIGaSS6JZppjrmlmcHXaqeQveRoBp54PzdkWm1qG+aag2d25D6J04qjan7aRGemEhE4qqaWNMooplo5uGo+inpoY6qieJgAAIfkEBQUABAAsCgACAFcAMAAAA/9Iutz+MD5Agbw46z0r5WAoSp43nihXVmnrdusrv+s332dt4Tyo96OAMJD6oQbIQWs4RBlHySSKyczVTtHoidocPUNZaZAr9F5FYbGI3PWdQWn1mi36buLKFJvojsHjLnshdhl4L4IqbxqGh4gahBJ4eY1kiX6LgDN7fBmQEJI4jhieD4yhdJ2KkZk8oiSqEaatqBekDLKztBG2CqBACq4wJRi4PZu1sA2+v8C6EJexrBAC1AKBzsLE0g/V1UvYR9sN3eR6lTLi4+TlY1wz6Qzr8u1t6FkY8vNzZTxaGfn6mAkEGFDgL4LrDDJDyE4hEIbdHB6ESE1iD4oVLfLAqPETIsOODwmCDJlv5MSGJklaS6khAQAh+QQFBQAEACwfAAIAVwAwAAAD/0i63P4ryACrvTjrNuf+YKh1nWieIumhbFupkivPBEzR+GnnfLj3IoFQwPqdAEhAazhEGUXJJIrJ1MGOUamJ2jQ9QVltkCv0XqFh5IncBX01afGYnDqD40u2z76JK/N0bnxweC5sRB9vF34zh4gjg4uMjXobihWTlJUZlw9+fzSHlpGYhTminKSepqebF50NmTyor6qxrLO0L7YLn0ALuhCwCrJAjrUqkrjGrsIkGMW/BMEPJcphLgPaAxjUKNEh29vdgTLLIOLpF80s5xrp8ORVONgi8PcZ8zlRJvf40tL8/QPYQ+BAgjgMxkPIQ6E6hgkdjoNIQ+JEijMsasNY0RQix4gKP+YIKXKkwJIFF6JMudFEAgAh+QQFBQAEACw8AAIAQgBCAAAD/0gkLPowykmrna3dzXvNmSeOFqiRaGoyaTuujitv8Gx/661HtRv8gd2jlwIChYtc0XjcEUnMpu4pikpv1I71astytkGh9wJGJk3QrXlcKa+VWjeSPZHP4Rtw+I2OW81DeBZ2fCB+UYCBfWRqiQp0CnqOj4J1jZOQkpOUIYx/m4oxg5cuAKYAO4Qop6c6pKusrDevIrG2rkwptrupXB67vKAbwMHCFcTFxhLIt8oUzLHOE9Cy0hHUrdbX2KjaENzey9Dh08jkz8Tnx83q66bt8PHy8/T19vf4+fr6A/3+/wADAjQmsKDBf6AOKjS4aaHDgZMeShzQcKLDhBYPEswoUAJBAgAh+QQFBQAEACxOAAoAMABXAAAD7Ei6vPKiyUkrhdDqfXHm4OZ9YSmNpKmiqVqykbuysgvX5o2HcLxzup8oKLQQix0UcqhcVo5ORi+aHFEn02sDeuWqAuCAkbYLh5/NmnldxajX7LbPBK+PH7K6narfO/t+SIBwfINmUYaHf4lghYyOhlqJWgqDlAuAlwyBmpVnnaChoqOkpaanqKmqKgCtrq+wsbA1srW2ry63urasu764Jr/CAL3Du7nGt7TJsqvOz9DR0tPU1TID2AOl2dyi3N/aneDf4uPklObj6OngWuzt7u/d8fLY9PXr9eFX+vv8+PnYlUsXiqC3c6PmUUgAACH5BAUFAAQALE4AHwAwAFcAAAPpSLrc/k5IAau9bU7MO9GgJ0ZgOI5leoqpumKt+1axPJO1dtO5vuM9yi8TlAyBvSMxqES2mo8cFFKb8kzWqzDL7Xq/4LB4TC6bz9yAes1uu9uzt3zOXtHv8xN+Dx/x/wF6gHt2g3Rxhm9oi4yNjo+QkZKTCgCWAGaXmmOanZhgnp2goaJdpKGmp55cqqusrZuvsJays6mzn1m4uRADvgMvuBW/v8GwvcTFxqfIycA3zA/OytCl0tPPO7HD2GLYvt7dYd/ZX99j5+Pi6tPh6+bvXuTuzujxXens9fr7YPn+7egRI9PPHrgpCQAAIfkEBQUABAAsPAA8AEIAQgAAA/lIutz+UIlJq7026h2x/xUncmD5jehjrlnqSmz8vrE8u7V5z/m5/8CgcEgsGo/IpHLJbDqf0Kh0SghYA9TXdZsdbb/Yrgb8FUfIYLMDTVYz2G13FV6Wz+lX+x0fdvPzdn9WeoJGAIcAN39EiIiKeEONjTt0kZKHQGyWl4mZdREDoQMcnJhBXBqioqSlT6qqG6WmTK+rsa1NtaGsuEu6o7yXubojsrTEIsa+yMm9SL8osp3PzM2cStDRykfZ2tfUtS/bRd3ewtzV5pLo4eLjQuUp70Hx8t9E9eqO5Oku5/ztdkxi90qPg3x2EMpR6IahGocPCxp8AGtigwQAIfkEBQUABAAsHwBOAFcAMAAAA/9Iutz+MMonqpg4682J/V0ojs1nXmSqSqe5vrDXunEdzq2ta3i+/5DeCUh0CGnF5BGULC4tTeUTFQVONYPs4BfoBkZPjFar83rBx8l4XDObSUL1Ott2d1U4yZwcs5/xSBB7dBMAhgAYfncrTBGDW4WHhomKUY+QEZKSE4qLRY8YmoeUfkmXoaKInJ2fgxmpqqulQKCvqRqsP7WooriVO7u8mhu5NacasMTFMMHCm8qzzM2RvdDRK9PUwxzLKdnaz9y/Kt8SyR3dIuXmtyHpHMcd5+jvWK4i8/TXHff47SLjQvQLkU+fG29rUhQ06IkEG4X/Rryp4mwUxSgLL/7IqFETB8eONT6ChCFy5ItqJomES6kgAQAh+QQFBQAEACwKAE4AVwAwAAAD/0i63D5wuEmrvTi3yLX/4MeNUmieITmibEuppCu3sDrfYG3jPKbHveDktxIaF8TOcZmMLI9NyBPanFKJJ4AWIBR4BZlkdqvtfb8+HYpMxp3Pl1qLvXW/vWkli16/3dFxTi58ZRcBhwEYf3hWBIRchoiHiotWj5AVkpIXi4xLjxiaiJR/T5ehoomcnZ+EGamqq6VGoK+pGqxCtaiiuJVBu7yaHrk4pxqwxMUzwcKbyrPMzZG90NGDrh/JH8t72dq3IN1jfCHb3L/e5ebh4ukmxyDn6O8g08jt7tf26ybz+m/W9GNXzUQ9fm1Q/APoSWAhhfkMAmpEbRhFKwsvCsmosRIHx444PoKcIXKkjIImjTzjkQAAIfkEBQUABAAsAgA8AEIAQgAAA/VINNz+8KlJq72Yxs1d/uDVjVxogmQqnaylvkMrT7A63/V47/m2/8CgcEgsGo/IpHLJbDqf0Kh0Sj0BroCqDMvVmrjgrDcTBo8v5fCZki6vCW33Oq4+0832O/at3+f7fICBdzsBhgFGeoWHhkV0P4yMRG1BkYeOeECWl5hXQ5uNIAKjAlKgiKKko1CnqBmqqk+nIbCkTq20taVNs7m1vKAnurtLvb6wTMbHsUq4wrrFwSzDzcrLtknW16tI2tvERt6pv0fi48jh5h/U6Zs77EXSN/BE8jP09ZFA+PmhP/xvJgAMSGBgQINvEK5ReIZhQ3QEMTBLAAAh+QQFBQAEACwCAB8AMABXAAAD50i6PD4syklre87qTbHn4OaNYSmNqKmiqVqyrcvBsazRpH3jmC7yD98OCBF2iEXjBKmsAJsWHDQKmw571l8my+16v+CweEwum88hgHrNbrvbtrd8znbR73MVfg838f8AeoB7doN0cYZvaIuMjY6PkJGSk2gBlgFml5pjmp2YYJ6dX6GeXaShWaeoVqqlU62ir7CXqbOWrLafsrNctjICwAIWvC7BwRWtNsbGFKc+y8fNsTrQ0dK3QtXAYtrCYd3eYN3c49/a5NVj5eLn5u3s6e7x8NDo9fbL+Mzy9/T5+tvUzdN3Zp+GBAAh+QQJBQAEACwCAAoAMABXAAAD60i63P5ryAGrvW1OzLvSmidW4DaeTGmip7qyokvBrUuP8o3beifPPUwuKBwSLcYjiaeEJJuOJzQinRKq0581yoQAvoAelgAG67Dl9K3LSLth7IV7zipV5nRUyILPT/t+UIBvf4NlW4aHVolmhYyIj5CDW3KAlJV4l22EmptfnaChoqOkpaanqKk6Aaytrq+wrzCxtLWuKLa5tSe6vbIjvsEBvMK9uMW2s8ixqs3Oz9DR0tPUzwLXAqPY26Db3tmX396U4t9W5eJQ6OlN6+ZK7uPw8djq9Nft9+Dz9FP3W/0ArtOELtQ7UdysJAAAOw==';

    $('body').on('click', '.cust-dialog-close', function () {
        dialog.getCurrent().close().remove();
    });

    $.warn = function (content, btn) {
        btn = btn || [
                ['取消', function () {
                    this.close();
                }],
                ['确定', function () {
                    this.close();
                }]
            ];

        var d = dialog({
            title: '',
            padding: 10,
            width: 440,
            content: CLOSE + (content || '内容'),
            skin: 'art-dialog warn-dialog',
            autofocus: false,
            button: [{
                value: btn[0][0],
                callback: btn[0][1]

            }, {
                value: btn[1][0],
                callback: btn[1][1],
                autofocus: true
            }]
        });
        d.showModal();

        return d;
    };

    $.loading = function (opts, hasMask) {
        if ($.type(opts) === 'boolean') {
            hasMask = opts;
            opts = null;
        }

        var d = dialog({skin: 'art-loading'});
        hasMask ? d.showModal(opts) : d.show(opts);

        return d;
    };

    $.topLoading = {
        show: function (text) {
            $('body .top-loading').remove();
            var str = '<div class="top-loading"><img src="' + loading_img + '" alt="" />' + text + '</div>';

            $('body').append(str);
            var $tips = $('.top-loading');
            var w = $tips.outerWidth();
            $tips.css('margin-left', -Math.ceil(w / 2) + 'px');
        },
        hide: function (callback) {
            $('.top-loading').remove();
            callback && callback();
        }
    };

    $.tips = function (content) {
        var d = dialog({
            title: '',
            padding: 10,
            width: 260,
            skin: 'art-tips',
            content: CLOSE + (content || '内容')
        });
        d.show();

        return d;
    };

    $.alert = function (content) {
        var d = dialog({
            padding: 10,
            skin: 'art-alert',
            id: 'art-alert',
            fixed: true,
            content: CLOSE + (content || '内容')
        });
        d.__popup[0].style.top = '0px';
        d.show();

        setTimeout(function () {
            d.close().remove();
        }, 3000);
        return d;
    };

    $.box = function (options) {
        var d = dialog({
            content: options.content || '',
            title: options.title || '',
            skin: 'art-box',
            width: options.width || 650,
            height: options.height || 550,
            onshow: options.onshow || function () {
            },
            onclose: options.onclose || function () {
            }
        }).showModal();
        return d;
    };

    $.confirm = function (args) {
        var d = dialog({
            title: args.title || '温馨提示',
            content: '<div class="art-box-content">' +
            '<div class="arttip-wrap clearfix">' +
            '<span class="arttip ' + (args.icon ? args.icon : 'arttip-warn') + ' pull-left"></span>' +
            '<div class="arttip-content pull-overflow"><p class="arttip-tit ' + (args.tip ? '' : 'arttip-only-tit') + '">' + args.content + '</p>' +
            (args.tip ? '<p class="arttip-tips">' + args.tip + '</p>' : '') +
            '</div></div>' +
            '</div>',
            skin: 'art-box',
            width: args.width || 500,
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
            }, {
                value: '取消',
                callback: function () {
                    return true;
                }
            }]
        });
        d.showModal();
        return d;
    };

    var newTips = true;

    //顶部提示信息
    $.topTips = function (options, callback) {
        var defaults = {
            mode: 'normal',
            tip_text: '',
            auto_close: true
        };
        options = $.extend({}, defaults, options, {
            callback: callback
        });
        if (newTips) {
            if (options.auto_close) {
                tipsUtils.show(options);
            } else {
                var isNormal = options.mode === 'normal';
                $.confirm({
                    content: isNormal ? '提示信息' : '出现异常',
                    icon: isNormal ? 'arttip-info' : null,
                    tip: options.tip_text
                });
            }
        } else {
            var o = new TopTips(this, options);
            o.init();
        }
    };

    function TopTips(o, v) {
        this.o = o;
        for (var i in v) {
            this[i] = v[i];
        }
    }

    TopTips.prototype = {
        init: function () {
            this.layout();
        },
        layout: function () {
            $('body .top-tips').remove();
            var str = '',
                cls = this.auto_close ? '' : ' closeable',
                content = this.tip_text + (this.auto_close ? '' : '<span class="close">×</span>');
            if (this.mode === 'normal') {
                str = '<div class="top-tips top-tips-success' + cls + '">' + content + '</div>';
            } else if (this.mode === 'warning') {
                str = '<div class="top-tips top-tips-warning' + cls + '">' + content + '</div>';
            } else if (this.mode === 'tips') {
                str = '<div class="top-tips' + cls + '">' + content + '</div>';
            } else if (this.mode === 'loading') {
                str = '<div class="top-tips top-tips-success"><img src="' + loading_img + '" alt="" />' + this.tip_text + '</div>';
            }
            $('body').append(str);
            var $tips = $('.top-tips');
            var w = $tips.outerWidth();
            $tips.css('margin-left', -Math.ceil(w / 2) + 'px');
            //this.customAnimate($tips, this.mode !== 'warning'?3000:0);
            this.customAnimate($tips, this.auto_close ? (this.mode === 'warning' ? 5000 : (this.mode === 'loading' ? 0 : 3000)) : 0);
        },
        customAnimate: function ($o, t) {
            var $self = this;
            if ($o) {
                $o.fadeIn(500);
            }
            if (t) {
                setTimeout(function () {
                    if ($o) {
                        $o.fadeOut(800, function () {
                            $(this).remove();
                            $self.callback && $self.callback.call && $self.callback.call(this);
                        });
                    }
                }, t);
            } else {
                $o.on('click', '.close', function () {
                    $o.remove();
                });
            }
        }
    };

    /* tipsLayer */
    var layerUtils = {
        tpl: '<div class="pt-content">{content}</div>' +
        '<div class="pt-footer clearfix">' +
        '   <button type="button" class="btn btn-primary pull-left"{ok_id}>确定</button>' +
        '   <button type="button" class="btn btn-secondary pull-right"{cancel_id}>取消</button>' +
        '</div>',
        create: function (node, content, options) {
            var _this = this;
            options = (typeof options === 'function' ? {ok: options} : options) || {};
            options.showButton = options.showButton === undefined ? true : options.showButton;
            var _self = this,
                default_opts = {
                    width: 286,
                    position: 'b',
                    align: 'c',
                    autoClose: false,
                    leaveClose: false
                },
                layer = $.pt($.extend({},default_opts,options,{
                    target: node,
                    time: options.time,
                    content: options.showButton ?
                        _self.tpl.replace('{content}', content)
                            .replace('{ok_id}', options.ok_id ? ' id="' + options.ok_id + '"' : '')
                            .replace('{cancel_id}', options.cancel_id ? ' id="' + options.cancel_id + '"' : '') :
                    '<div class="pt-content' + (options.cls ? ' ' + options.cls : '') + '">' + content + '</div>'
                }));

            if (options.create) {
                options.create.call(_self.obj, layer);
            }

            if (options.leaveClose) {
                clearTimeout(_this.timer);
                node.off('mouseout').on('mouseout', function () {
                    _this.timer = setTimeout(function () {
                        layer.hide();
                    }, 500);
                });

                layer.off('mouseover').on('mouseover', function () {
                    clearTimeout(_this.timer);
                });
            }

            layer.off('click').on('click', '.pt-footer .btn', function () {
                if ($(this).hasClass('btn-primary')) {
                    var ok = options.ok || _self.ok;
                    if (!ok || (ok.call(_self.obj, layer) !== false)) {
                        layer.hide();
                    }
                } else {
                    var cancel = options.cancel || _self.cancel;
                    if (cancel) {
                        cancel.call(_self.obj, layer);
                    }
                    layer.hide();
                }
            });

            return {
                ok: function (fn) {
                    _self.ok = fn;
                    return this;
                },
                cancel: function (fn) {
                    _self.cancel = fn;
                    return this;
                }
            };
        }
    };

    $.fn.extend({
        /**
         * 简化TipsLayer
         * @param content 主体内容
         * @param options 额外参数
         */
        tipsLayer: function (content, options) {
            return layerUtils.create($(this), content, options);
        }
    });
});
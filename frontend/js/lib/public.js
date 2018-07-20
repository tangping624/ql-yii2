$.extend({
    getUrlParam : function (name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return this.deCode((r[2])); return null;
    },
    deCode: function (str) {
        return decodeURIComponent(str);
    },
    enCode: function (str){
        return encodeURIComponent(str);
    },
    tool: {
        on: function (element, type, func) {
            if (element.addEventListener) {
                element.addEventListener(type, func, false); //false 表示冒泡
            } else if (element.attachEvent) {
                element.attachEvent('on' + type, func);
            } else {
                element['on' + type] = func;
            }
        },
        getPageHeight: function () {
            return document.documentElement.scrollHeight || document.body.scrollHeight;
        },
        // 获取页面卷去的高度
        getScrollTop: function () {
            return document.documentElement.scrollTop || document.body.scrollTop;
        },
        // 获取页面可视区域宽度
        getClientHeigth: function () {
            return document.documentElement.clientHeight || document.body.clientHeight;
        }
    }
});

/*
* 用户列表操作插件
* yuanl02 2015-3-23
*/

;(function($){
    $.fn.userListOpera = function (options) {
        var curr = this;
        var defaults = {
            _show:function(){
                curr.stop(true,false).animate({
                    'margin-right':'0'
                },400);
            },
            _hide:function(){
                curr.stop(true,false).animate({
                    'margin-right':'-420px'
                },400);
            },
            customEvent:function(){
                //
            }
        }
        var options = $.extend({}, defaults, options);
        return this.each(function () {
            var o = new userListOpera(this, options);
            o.init();
        });
    }

    function userListOpera(o, v) {
        this.o = o;
        for (var i in v) {
            this[i] = v[i];
        }
    }

    userListOpera.prototype={
        init:function(){
            this.customEvent();
        }
    }
})(jQuery);
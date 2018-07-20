/*
 * 多选列表插件
 * yuanl02 2015-4-7
 */

;(function($){
    $.fn.multiSelectDom = function (options) {
        var defaults = {
            cName : 'selected',
            data : [],
            _loadDom : null,
            _removeItem : null     //删除当前项
        }
        var options = $.extend({}, defaults, options);
        return this.each(function () {
            var o = new multiSelectList(this, options);
            o.init();
        });
    }

    function multiSelectList(o, v) {
        this.o = o;
        for (var i in v) {
            this[i] = v[i];
        }
        this.index_i = 0;
    }

    multiSelectList.prototype = {
        init:function(){
            var $wrap = $(this.o).find('.msc-list');
            var $list = $(this.o).find('.ps-list');
            var _this = this;

            if(this._loadDom){
                this._loadDom($list,$wrap);
            }

            $wrap.on('click','.fonticon-remove',function(){
                var $item = $(this).parent();
                var value = $item.attr('data-value');
                if(_this._removeItem){
                    $item.remove();
                    _this._removeItem(value);
                }
                return false;
            });
        }
    }
})(jQuery);
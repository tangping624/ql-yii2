/*
 * 多选列表插件
 * yuanl02 2015-4-7
 */

;(function($){
    $.fn.list = function (options) {
        var defaults = {
            cName : 'selected',
            data : [],
            defaultSelect : [],
            params : [],
            _innerOpera : null,
            _selectValue : null
        }
        var options = $.extend({}, defaults, options);
        var o = new list(this, options);
        o.init();
        return o;
    }

    function list(o, v) {
        this.o = o;
        for (var i in v) {
            this[i] = v[i];
        }
        this.index_i = 0;
    }

    list.prototype = {
        init:function(){
            this.createItem(this.data,$(this.o));
            if(this.defaultSelect.length){
                this.selectItem(this.defaultSelect);
            }
            this.textClick();
            this.checkBoxClick();
        },
        /*
         * 创建列表项
         * @param josn data 外部数据
         * @param dom $o 列表ul
         */
        createItem : function (data,$o) {
            var html = [];
            if(data.length){
                for(i in data){
                    if(!data[i].is_checked){
                        html.push('<li class="ps-item">');
                    }else{
                        html.push('<li class="ps-item selected">');
                    }
                    html.push('<p class="ps-text" data-index="'+(this.index_i++)+'">'+data[i].text+'</p>');
                    html.push('<div class="ps-opera"><span class="pso-item"><span class="fonticon fonticon-checkbox"></span></span></div>');
                    html.push('</li>');
                    this.saveData(data[i]);
                }
            }
            $o.append(html.join(''));
        },
        saveData : function(data){
            var temp = {};
            for(x in data){
                temp[x] = data[x];
            }
            this.params.push(temp);
        },
        //文本点击事件
        textClick : function(){
            var _this = this;
            $(this.o).on('click', '.ps-text', function () {
                var $item = $(this).parent('.ps-item');
                _this.checkboxOpera($item,$(this));
            });
        },
        checkBoxClick : function(){
            var _this = this;
            $(this.o).on('click', '.pso-item', function () {
                var $item = $(this).parents('.ps-item');
                var $itemText = $item.find('.ps-text');
                _this.checkboxOpera($item,$itemText);
            });
        },
        checkboxOpera : function($item,$itemText){
            var markJson = {};
            if(!$item.hasClass(this.cName)){
                markJson['is_checked'] = true;
                $item.addClass(this.cName);
            }else{
                markJson['is_checked'] = false;
                $item.removeClass(this.cName);
            }
            var data_index = $itemText.attr('data-index');
            var data = this.params[data_index];
            if(this._innerOpera){
                this._innerOpera(data,markJson);
            }
            return false;
        },
        selectItem : function(arr){
            for( var i = 0;i<arr.length;i++){
                for( var j in this.params){
                    if(this.params[j].value === arr[i].value){
                        $(this.o).find('.ps-text[data-index="'+j+'"]').parent().addClass(this.cName);
                        break;
                    }
                }
            }
        },
        deselectItem : function(value){
            for( var i in this.params){
                if(this.params[i].value === value){
                    $(this.o).find('.ps-text[data-index="'+i+'"]').parent().removeClass(this.cName);
                    break;
                }
            }
        }
    }
})(jQuery);
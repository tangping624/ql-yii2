 /*
 * 单选操作插件(用于选择部门)
 * yuanl02 2015-4-1
 */

;(function($){
    $.fn.multiSelectOpera = function (options) {
        var defaults = {
            cName : 'selected',
            loadTree : null,
            //判断是否是选中状态
            isSelected : function(node,node_text,$o,data){
                var data_index = $(node_text).attr('data-index');
                if(!data.is_checked){
                    $(node).addClass(this.cName);
                    $o.append('<li class="msc-item" data-id="'+data_index+'"><span class="msc-text">'+data.treeText+'</span><span class="fonticon fonticon-remove" title="删除"></span></li>');
                }else{
                    $(node).removeClass(this.cName);
                    $o.find('.msc-item[data-id="'+data_index+'"]').remove();
                }
            },
            setHeight:function($o,$o2,h,h2){
                var curr_h = $o.outerHeight(true);
                if(curr_h > h){
                    $o2.css({
                        height: h2 -(curr_h - h)+'px'
                    })
                }else{
                    $o2.css({
                        height: h2+'px'
                    })
                }
            }
        }
        var options = $.extend({}, defaults, options);
        return this.each(function () {
            var o = new multiSelectOpera(this, options);
            o.init();
        });
    }

    function multiSelectOpera(o, v) {
        this.o = o;
        for (var i in v) {
            this[i] = v[i];
        }
    }

    multiSelectOpera.prototype = {
        init:function(){
        	$(this.o).append('<ul class="msc-list"></ul><div class="js-tree"></div>');
            var $msc_list = $(this.o).find('.msc-list');
            var $tree = $(this.o).find('.js-tree');
        	var _this = this;

        	var l_h = $msc_list.outerHeight(true);
        	var t_h = $tree.outerHeight(true);

            if(this.loadTree){
                this.loadTree($tree,$msc_list,l_h,t_h);
            }

            $msc_list.on('click','.fonticon-remove',function(){
                $(this).parent().remove();
                var data_id = $(this).parent().attr('data-id');
                $tree.find('.tree-text[data-index="'+data_id+'"]').parent().removeClass(_this.cName);
                return false;
            });

            //禁止选中文本
            $(this.o).on('selectstart',function(){
            	return false;
            })
        }
    }
})(jQuery);
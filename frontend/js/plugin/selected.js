/**
 * Created by meicj on 2015/4/8.
 */

define(function (require, exports, module) {

    /**
     * 已选择控件
     * @returns {{addSelecteds: Function, removeSelected: Function, getSelectedData: Function}}
     */
    $.fn.selected = function (options) {
        var $o = this;
        var defaults = {
            /**
             * 点击删除按钮事件
             * @param $element {jQuery} 当前项对象
             * @param value {string} 当前项的值
             * @returns {boolean} 是否允许删除,指定false时不执行删除
             */
            onRemoveClick: function ($element, value) {
                return true;
            },
            /**
             * 选择项更改后事件
             * @param $element {jQuery} 当前项对象
             * @param value {string} 当前项的值
             * @returns 
             */
            afterSelectedChange: function ($element, value) {
                return true;
            }
        };
        options = $.extend({}, defaults, options);

        var _selected;
        return _selected = {
            /**
             * 添加选中项
             * @param data {[{value:'',text:''}]}
             */
            addSelecteds: function (data) {
                var html = [], $append;

                var bindRemove = function ($append) {
                    $append.find('.fonticon-remove').click(function () {
                        var $this = $(this), $item = $this.parents('li:first'), value = $item.data('value');

                        if (options.onRemoveClick($item, value) === false) {
                            return;
                        }
                        _selected.removeSelected(value);
                    });
                }

                //1.生成项HTML
                $.each(data, function (i, v) {
                    //TODO:特殊字符处理
                    html.push('<li class="msc-item" data-value="' + v.value + '" data-text="' + v.text + '"><span class="msc-text">' + v.text + '</span><span class="fonticon fonticon-remove" title="删除"></span></li>');
                });
                $append = $(html.join(''));

                //2.绑定删除操作
                bindRemove($append);

                //3.附加到DOM
                $o.append($append);

                 options.afterSelectedChange();
            },
            /**
             * 移除选中项
             * @param value {string|jQuery} 要移除项的值或者对应jQuery对象
             */
            removeSelected: function (value) {
                var $item = typeof value === 'string' ? $o.find('li.msc-item[data-value="' + value + '"]') : $(value);
                $item.remove();

                options.afterSelectedChange();
            },
            /**
             * 获取选中的数据
             * @returns {[{value:'',text:''}]}
             */
            getSelectedData: function () {
                var data = [], text, value;
                $o.find('li.msc-item[data-value][data-text]').each(function (i, v) {
                    value = $(v).data('value');
                    text = $(v).data('text');

                    data.push({value: value, text: text});
                });

                return data;
            }
        };
    };
});

define(function(require, exports, module) {               
    var Form = {
        // 数据自动填充
        autoFill: function(form, data) {
            data = data || O.getQuery();
            var val, that = this,
                eles = this._getElesByForm(form);

            $.each(eles, function(i, v) {
                v = $(v);
                val = data[v.attr('name')];

                if(val) {
                    v[0].tagName.toLowerCase() == 'select' ? 
                        that.setSelect(v, val) : v.val(val);
                }
            });
        },

        // 设置select下拉选中
        setSelect: function(sel, value, able) {
            if(!value) return;

            if ('string' === typeof(sel))
                sel = $('#' + sel);

            var opts = sel.find('option');
                sel = sel[0];

            $.each(opts, function(i, v) {
                if (v.value === value || v.text === value) {
                    sel.selectedIndex = i;
                    return false;
                }
            });

            able = able || (undefined === able ? true : false);
            able || (sel.disabled = "disabled");
        },

        _getElesByForm: function(form) {
            var eles = form[0].elements 
                || form.find('input, select, textarea');

            var map = {
                'undefined': false, 'file': false, 'submit': false,
                'reset': false, 'button': false, 'image': false
            };

            var that = this, prop;

            eles = $.grep(eles, function(v) {
                prop = that._readProperty(v);

                return (map[prop.type] === false
                    || prop.disabled
                    || prop.name === undefined) ? false : true;
            });

            return eles;
        },

        _getValue: function(o) {
            var val  = o.value;
            
            if(o.type == "checkbox" || o.type == 'radio'){
                val = o.checked ? val : undefined;
            }

            return val;
        },

        _readProperty: function(el) {
            el = $(el);
                 
            var o = {
               "id"        : el.attr("id"),
               "value"     : $.trim(el.val()),
               "name"      : el.attr("name"),
               'type'      : el.prop("type") + '',
               'disabled'  : el.prop('disabled'),
               'checked'   : el.prop('checked')
            };

            o.value = this._getValue(o);

            return o;
        },

        /**
         * 表单序列化
         * @param  {jQuery DOM} form 表单元素
         * @param  {Number} type 返回值的类型，1为字符串，其它为对象，默认1
         * @return {String|Json}
         */
        serialize: function(form, type) {
            type = (type === undefined ? 1 : type);

            var eles = this._getElesByForm(form);
            var prop, that = this,
                r = type == 1 ? [] : {};

            $.each(eles, function(k,v) { 
                prop = that._readProperty(v); 

                if(prop.value !== undefined) {
                    if(type == 1) {
                        if(prop.type === 'checkbox') prop.name = prop.name + '[]';
                        r.push(prop.name + "=" + encodeURIComponent(prop.value))
                    } else {
                        if(prop.type === 'checkbox') {
                            !$.isArray(r[prop.name]) &&
                                (r[prop.name] = []);

                            r[prop.name].push(prop.value);
                        } else {
                            r[prop.name] = prop.value;
                        } 
                    }
                }
            });

            return type == 1 ? r.join("&") : r;
        }
    }

    module.exports = Form;
});
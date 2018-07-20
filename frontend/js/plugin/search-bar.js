/**
 * 搜索框
 * Created by weizs on 2015/10/28.
 */
'use strict';
define(function (require, exports, module) {
    /*
     <div class="search-bar width-long">
     <input type="text" class="search-input" placeholder="标题/作者/摘要" id="search_input">
     <span class="x-icon x-icon-clear" id="x_clear">×</span>
     <span class="search-btn search-icon"></span>
     </div>
     */
    var noop = function () {
    };
    var Utils = {
        cache: {},

        create: function ($wrap, callback) {
            var searchBarId = $wrap.data('search-bar-id');
            if (!searchBarId) {
                this.cache[searchBarId] = new this.SearchBar($wrap, callback);
                this.cache[searchBarId].value = $wrap.find('input').val();
                this.bindEvent(this.cache[searchBarId]);
            }
            return this.cache[searchBarId];
        },
        invoke: function (instance, value) {
            var change = instance.value !== value;
            if (change) {
                instance.value = value;
            }
            instance.callback.call(instance, change, value);
        },
        bindEvent: function (instance) {
            var _self = this,
                $wrap = instance.$wrap,
                $clear = $wrap.find('.x-icon,.x-icon-clear,#x_clear'),
                $input = $wrap.find('input');
            //如果没有值，则一开始就隐藏X
            var defaultVal = $input.val();
            if (defaultVal === '' || defaultVal === $input.attr('placeholder')) {
                $clear.hide();
            } else {
                $clear.show();
            }

            $wrap.on('input propertychange', 'input', function () {
                if (this.value === '') {
                    $clear.hide();
                    _self.invoke(instance, this.value);
                } else {
                    $clear.show();
                }
            });
            $wrap.on('keyup', 'input', function (e) {
                if ((e.keyCode || e.which) === 13) {
                    _self.invoke(instance, this.value);
                }
            });
            $wrap.on('click', '.search-btn,.search-icon,.x-icon,.x-icon-clear,#x_clear', function () {
                var $dom = $(this);
                if ($dom.is('.x-icon,.x-icon-clear,#x_clear')) {
                    $dom.hide();
                    $input.val('');
                }
                _self.invoke(instance, $input.val());
            });
        },
        SearchBar: function ($wrap, callback) {
            this.$wrap = $wrap || $();
            this.callback = callback || noop;
        }
    };

    $.fn.extend({
        /**
         * 回调
         * @param callback(isChange,value)
         * @returns {*}
         */
        searchBar: function (callback) {
            return this.each(function () {
                Utils.create($(this), callback);
            });
        }
    });

    return Utils.create;
});
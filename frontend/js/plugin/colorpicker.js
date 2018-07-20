/**
 * 简单colorpicker
 * 后续如需复杂选择建议使用https://github.com/PitPik/tinyColorPicker
 * Created by weizs on 2015/12/21.
 */
'use strict';
/*global $*/
define(function (require, exports, module) {
    require('tooltips');
    var index = 0;
    var publicCache = {};
    var defaultSetting = {
        colors: ['#ffffff', '#ffd7d5', '#ffdaa9', '#fffed5', '#d4fa00', '#73fcd6', '#a5c8ff', '#ffacd5', '#ff7faa', '#d6d6d6', '#ffacaa', '#ffb995', '#fffb00', '#73fa79', '#00fcff', '#78acfe', '#d84fa9', '#ff4f79', '#b2b2b2', '#d7aba9', '#ff6827', '#ffda51', '#00d100', '#00d5ff', '#0080ff', '#ac39ff', '#ff2941', '#888888', '#7a4442', '#ff4c00', '#ffa900', '#3da742', '#3daad6', '#0052ff', '#7a4fd6', '#d92142', '#000000', '#7b0c00', '#ff4c41', '#d6a841', '#407600', '#007aaa', '#021eaa', '#797baa', '#ab1942'],
        color: '',
        clear: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAIAAAAC64paAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNS1jMDIxIDc5LjE1NTc3MiwgMjAxNC8wMS8xMy0xOTo0NDowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTQgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjdBMERFMkEzQTdCQTExRTU5NDY1Q0E1RDYxOUFEQTIyIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjdBMERFMkE0QTdCQTExRTU5NDY1Q0E1RDYxOUFEQTIyIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6N0EwREUyQTFBN0JBMTFFNTk0NjVDQTVENjE5QURBMjIiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6N0EwREUyQTJBN0JBMTFFNTk0NjVDQTVENjE5QURBMjIiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz4/kGjlAAAAh0lEQVR42pzUOw6AIBAEULiFHkLvYuKFNYGCA3iD1VhqZWwQQ/whn12mmuYlUw0HAJaRaWRFyQzWxOzDMLcNCEHGVm5KnZNJ+Jam0/Bb0rAjCfgvsdgrUTgk0zgiEzguYzgpgxgj/RgpPRgvXUySH0yVD86QF+67DGkxByn5uuiqzviiQ4ABAN5WC87dVzi2AAAAAElFTkSuQmCC',
        event: 'click',
        onSelect: function () {
        }
    };

    var utils = {
        _buildColorGrid: function (colors) {
            var html = [];
            for (var i = 0; i < colors.length; i++) {
                html.push('<a title="' + colors[i] + '" href="javascript:;" class="color-item" data-color="' + colors[i] + '" style="background: ' + colors[i] + '"></a>');
            }
            return html.join('');
        },
        //改变值
        _change: function (instance) {
            var color = instance.options.color;
            instance.options.onSelect.call(instance, color);
            var idx = instance.color_cache.indexOf(color);
            if (idx + 1) {
                instance.color_cache.splice(idx, 1);
            }
            instance.color_cache.splice(0, 0, instance.options.color);
            instance.color_cache = instance.color_cache.slice(0, 8);

            instance.renderDom.css('background-color', color);
            instance.layer.hide().remove();
        },
        _show: function (instance) {
            var _self = this;
            instance.color_cache = instance.color_cache || [];
            instance.layer = $.pt({
                target: instance.node,
                width: 260,
                height: 'auto',
                arrow: false,
                position: 'b',
                align: 'l',
                autoClose: false,
                leaveClose: false,
                content: '<div class="tiny-color-picker">' +
                '<div class="color-history clearfix">' +
                '   <div class="gray">最近使用颜色</div>' +
                '   <div class="history">' +
                '       <a title="清除颜色" href="javascript:;" class="color-clear"><img width="100%" height="100%" src="' + instance.options.clear + '"></a>' + utils._buildColorGrid(instance.color_cache) +
                '   </div>' +
                '</div>' +
                '<div class="color-grid clearfix">' + utils._buildColorGrid(instance.options.colors) + '</div>' +
                '<div class="color-preview">' +
                '   <a class="preview" href="javascript:;" style="background: #F00"></a>' +
                '   <button class="btn btn-secondary">确认</button>' +
                '   <div class="color-input"><span>#</span><input type="text" value="FF0000"></div>' +
                '</div>'
            });

            var preview = instance.layer.find('.color-preview .preview');
            var input = instance.layer.find('.color-preview input');

            instance.layer.off('click');
            instance.layer.off('input');

            instance.layer.on('click', '.color-item', function () {
                instance.options.color = $(this).data('color');
                _self._change(instance);
            });

            instance.layer.on('click', '.color-history .color-clear', function () {
                instance.options.color = '';
                _self._change(instance);
            });

            instance.layer.on('input', '.color-preview input', function () {
                preview.css('background', '#' + this.value);
            });

            instance.layer.on('click', '.btn.btn-secondary', function () {
                instance.options.color = '#' + input.val();
                _self._change(instance);
            });

        }
    };

    var TinyColorPicker = function (node, options) {
        var _self = this,
            $node = $(node),
            renderDom = $node.find(options.renderDom);
        _self.options = $.extend(true, defaultSetting, options);
        _self.node = $node;
        _self.renderDom = renderDom.length ? renderDom : $node;
        if (_self.node.length) {
            _self.node.on(_self.options.event, function (e) {
                e.stopPropagation();
                e.preventDefault();
                utils._show(_self);
            });
        }
    };

    var colorPicker = function (node, options) {
        var instance = $(node),
            instanceId = instance.data('tiny-id') || 'tiny_cp_' + index++;
        if (!instance.data('tiny-id')) {
            instance.data('tiny-id', instanceId);
        }
        if (!publicCache[instanceId]) {
            publicCache[instanceId] = new TinyColorPicker(node, options || {});
        }
        return publicCache[instanceId];
    };

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.pt').length) {
            var pt = $('.pt');
            if (pt.find('.tiny-color-picker').length) {
                pt.hide().remove();
            }
        }
    });

    $.fn.colorPicker = function (options) {
        this.each(function () {
            colorPicker(this, options);
        });
    };

    return colorPicker;
});
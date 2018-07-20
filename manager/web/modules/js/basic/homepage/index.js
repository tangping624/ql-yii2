/**
 * Created by weizs on 2015/5/13.
 */
'use strict';
/*global O,$,define*/
define(function (require, exports, module) {
    var template = require('../../../../frontend/js/lib/template.js');
    require('../../../../frontend/js/lib/jquery.ui/jquery.sortable');
    require('../../../../frontend/js/lib/dialog.js');


    var empty_tpl = $('#empty_tpl').html(),
        app_content = $('.content-wrap');


    var list = {
        tips: function (msg, mode) {
            $.topTips({mode: mode || 'normal', tip_text: msg || '操作失败'});
        },
        del: function (url, id) {
            return O.ajaxEx({
                type: 'get',
                data: {id: id},
                url: url
            });
        },
        del_tip: function ($btn, $wrap, id, callback) {
            var that = this;
            $btn.tipsLayer('确定删除？', function () {
                var url = O.path('/basic/homepage/remove-banner');
                if ($btn.closest('.wrap').hasClass('recommend')) {
                    url = O.path('/basic/homepage/remove-group');
                }
                that.del(url, id).then(function (res) {
                    if (res.result) {
                        that.tips('删除成功');
                        $wrap.fadeOut(function () {
                            $wrap.remove();
                            callback && callback();
                        });
                    } else {
                        that.tips(res.msg, 'tips');
                    }
                });
            });

        },
        init: function () {
            var $self = this;
            $('.del').on('click', function () {
                var $this = $(this),
                    $wrap = $this.closest('.column');
                $self.del_tip($this, $wrap, $this.data('id'));
            });

            app_content.on('click', '.close-btn', function () {
                var $wrap = $(this).closest('.app-item');
                $self.del_tip($wrap, $wrap, $wrap.data('id'));
            });

            $('.add-btn').on('click', function (e) {
                e.preventDefault();
                var _this = $(this),
                    wrap = _this.closest('.wrap');
                if (wrap.find('.column').length >= _this.data('max')) {
                    $self.tips(_this.data('msg'), 'tips');
                } else {
                    window.location.href = _this.attr('href') + '&count=' + _this.closest('.wrap').find('.column').length;
                }
            });

            app_content.sortable({
                items: '.move-item',
                cursor: 'move',
                handle: '.v-box',
                placeholder: 'sortable-placeholder',
                update: function () {
                    var cont = $(this),
                        data = [],
                        appItemList = cont.find('.move-item');
                    appItemList.each(function (index) {
                        data.push({
                            id: appItemList.eq(index).data('id'),
                            sort: index + 1
                        });
                    });

                    var isRecommend = cont.closest('.wrap').hasClass('recommend');

                    O.ajaxEx({
                        url: isRecommend ?
                            O.path('/basic/homepage/group-sort') :
                            O.path('/basic/homepage/entry-sort'),
                        type: 'post',
                        data: {
                            sort_list: JSON.stringify(data)
                        }
                    });
                }
            });

            app_content.find('.move-item .v-box').disableSelection();

        }
    };
    list.init();
});
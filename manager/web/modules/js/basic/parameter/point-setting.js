/**
 * Created by weizs on 2015/12/08.
 */
'use strict';
/*global O,$,define*/
define(function (require, exports, module) {
    var template = require('../../../../frontend/js/lib/template');
    require('../../../../frontend/js/lib/dialog');
    require('../../../../frontend/js/plugin/select-box');

    var type_box = $('#type_box'),
        list_tpl = $('#list_tpl').html(),
        data_content = $('#data_content'),
        submit_btn = $('#submit_btn');

    var list = {
        init: function (type) {
            var _self = this;
            _self._render(type).then(function () {
                _self._bindEvent(type);
            });
        },
        _getSetting: function (type) {
            return O.ajaxEx({
                url: O.path('basic/point-setting/get-setting'),
                type: 'get',
                data: {
                    type: type
                }
            });
        },
        _render: function (type) {
            $('select.invisible').selectBoxs('destroy');
            data_content.find('thead th').html('&nbsp;');
            data_content.find('tbody').html('<tr><td class="empty-td align-c" colspan="3">正在加载中...</td></tr>');
            submit_btn.addClass('hide');
            return this._getSetting(type).then(function (res) {
                var readonly = res['read-only'];
                data_content.html(template(list_tpl, {
                    type: type,
                    data: res.data,
                    title: type === '商家' ? ['积分付方', '积分收方'] : ['积分收方', '积分付方'],
                    readonly: readonly,
                    group: res.groupInOptions
                }));
                if (!readonly) {
                    submit_btn.removeClass('hide');
                }
            });
        },
        _getData: function (selects) {
            var data = [];
            if (selects) {
                selects.each(function (i) {
                    var val = selects.eq(i).selectBox().val();
                    if (val) {
                        data.push({
                            code: val.code,
                            type: val.type,
                            relevance: val.id
                        });
                    }
                });
            }
            return JSON.stringify(data);
        },
        _bindEvent: function (type) {
            var _self = this,
                selects = $('select.invisible');

            if (!_self.bind) {
                _self.bind = true;
                type_box.on('click', 'li', function () {
                    _self.init($(this).addClass('on').siblings().removeClass('on').end().data('type'));
                });
            }

            selects.selectBoxs({
                dataKey: ['code', 'type'],
                style: {
                    minWidth: 65
                }
            });

            submit_btn.off('click').on('click', function () {
                O.ajaxEx({
                    node: submit_btn,
                    url: O.path('basic/point-setting/save'),
                    type: 'post',
                    data: {
                        type: type,
                        data: _self._getData(selects)
                    }
                }).then(function (res) {
                    if (res.result) {
                        $.topTips({tip_text: '保存成功'});
                    } else {
                        $.topTips({tip_text: res.msg, mode: 'tips'});
                    }
                });
            });
        }
    };

    list.init(type_box.find('li:eq(0)').data('type'));

});
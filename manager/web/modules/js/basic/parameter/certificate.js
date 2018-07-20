/**
 * Created by weizs on 2015/5/14.
 */
'use strict';
/*global O,$,define*/
define(function (require, exports, module) {
    var template = require('../../../../frontend/js/lib/template');
    require('../../../../frontend/js/lib/dialog');
    require('../../../../frontend/js/plugin/form.js');
    require('../../../../frontend/js/lib/jquery.ui/jquery.sortable');


    //tpl
    var addTpl = '<form id="add_form"><label>证件类型</label><input class="form-control" type="text" id="title" name="title" /></form>',
        rowTpl = $('#row_tpl').html();

    var tips = function (msg, mode) {
        $.topTips({mode: mode || 'normal', tip_text: msg || '操作成功'});
    };

    $('.certificate-form').off('click').on('click', '.opt-btn', function () {
        var $this = $(this);
        if ($this.hasClass('opt-del')) {
            $this.tipsLayer('确定删除？', function () {
                O.ajaxEx({
                    url: O.path('basic/certificate-type/delete'),
                    type: 'post',
                    data: {
                        id: $this.data('id')
                    }
                }).then(function (res) {
                    if (res.result) {
                        tips('删除成功');
                        var row = $this.closest('tr');
                        row.fadeOut(function () {
                            row.remove();
                        });
                    } else {
                        tips(res.msg, 'tips');
                    }
                });
            });
        } else if ($this.hasClass('opt-modify')) {
            var input = $this.closest('tr').addClass('edit').find('input');
            input.val(input.val());
            input.focus();
        } else if ($this.hasClass('opt-cancel')) {
            $this.closest('tr').removeClass('edit');
        } else if ($this.hasClass('opt-save')) {
            O.ajaxEx({
                url: O.path('basic/certificate-type/edit'),
                type: 'post',
                data: {
                    id: $this.data('id'),
                    title: $this.closest('tr').find('.opt-input-title').val(),
                    value: $this.closest('tr').find('.opt-input-value').val()
                }
            }).then(function (res) {
                if (res.result) {
                    tips('修改成功');
                } else {
                    tips(res.msg, 'tips');
                }
                var $new_title = $this.closest('tr').find('.opt-input-title').val(),
                    $new_value = $this.closest('tr').find('.opt-input-value').val();

                $this.closest('tr').find('.opt-input-title').val($new_title).end().find('.v-text-title').html($new_title).end().removeClass('edit');
                $this.closest('tr').find('.opt-input-value').val($new_value).end().find('.v-text-value').html($new_value).end().removeClass('edit');
            });
        } else if ($this.hasClass('add-certificate-type')) {

            var close = false;

            $this.tipsLayer(addTpl, {
                ok_id: 'submit_btn',
                create: function (layer) {
                    layer.find('#title').focus();
                    layer.find('#add_form').form({
                        submitbtn: 'submit_btn',
                        formName: 'add_form',
                        rules: [{
                            id: 'title',
                            required: true,
                            msg: {required: '请填写证件类型名称'}
                        }],
                        submit: function (param, _data) {
                            close = true;

                            var tBody = $('.table').find('tbody'),
                                num = [0],
                                idNum = tBody.find('tr td .idx-num');
                            idNum.each(function (i) {
                                num.push(idNum.eq(i).text() - '');
                            });
                            _data.sort = Math.max.apply(this, num) + 1;

                            O.ajaxEx({
                                url: O.path('basic/certificate-type/add'),
                                type: 'post',
                                data: _data
                            }).then(function (res) {
                                if (res.result) {
                                    tips(res.msg);
                                    tBody.append(template(rowTpl, {
                                        title: _data['title'],
                                        index: _data.sort,
                                        id: res.id
                                    }));
                                } else {
                                    tips(res.msg, 'tips');
                                }
                            });
                        }
                    });
                },
                ok: function () {
                    return close;
                }
            });
        } else if ($this.hasClass('opt-dosort')) {
            $('.sort_list').sortable({item: 'tr', placeholder: 'sortable-placeholder'}).sortable('enable');
            $('#view-table').addClass('sort');
        } else if ($this.hasClass('opt-docancle')) {
            $('.sort_list').sortable('disable');
            $('#view-table').removeClass('sort');
        } else if ($this.hasClass('opt-finish')) {
            var sortList = [];
            $.each($('.sort_list tr'), function () {
                var item = $(this);
                sortList.push({
                    id: item.find('a.opt-sort').data('id'),
                    sort: item.index() + 1
                });
            });
            O.ajaxEx({
                url: O.path('basic/certificate-type/sort'),
                data: {ids: JSON.stringify(sortList)},
                type: 'post'
            }).then(function (res) {
                if (res.result) {
                    tips(res.msg);
                    window.location.reload();
                } else {
                    tips(res.msg, 'tips');
                    $('.opt-hide').css('display', 'none');
                    $('.opt-dosort').show();
                    $('.opt-del').show();
                    $('.opt-modify').show();
                    $('.sort_list').sortable('disable');
                }
            });
        }
    });
});
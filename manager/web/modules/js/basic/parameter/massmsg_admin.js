'use strict';
/*global O,$,define*/
define(function (require, exports, module) {
    require('../../../../frontend/js/plugin/grid.js');
    require('../../../../frontend/js/lib/dialog');
    require('../../../../frontend/js/plugin/search-bar');


    $('#view-table').grid({
        url: O.path('/basic/massmsg-admin/get-admins'),
        delurl: O.path('/basic/massmsg-admin/del-admin'),
        idField: 'id',
        templateid: 'admin_template',
        pagesize: 10,
        searchText: true,
        emptyText: '暂无管理员'
    });

    $('#add_admin_btn').off('click').on('click', function () {
        var box = $.box({
            title: '添加群发管理员',
            width: 750,
            height: 'auto',
            content: $('#lookup_template').html()
        });

        var wrap = $(box.node);

        wrap.find('.search-bar').searchBar(function (change, value) {
            if (change) {
                if (!value) {
                    $.topTips({tip_text: '请输入手机号', mode: 'tips'});
                    return;
                }

                O.ajaxEx({
                    url: O.path('/basic/massmsg-admin/lookup-admin'),
                    data: 'mobile=' + value,
                    type: 'get',
                    success: function (json) {
                        if (!json.result) {
                            $.topTips({tip_text: json.msg, mode: 'tips'});
                        } else {
                            $('#input_nickname').val(json.items && json.items.nick_name || '');
                            $('#input_name').val(json.items && json.items.name || '');
                            $('#input_mobile').val(json.items && json.items.mobile || '');
                            $('#input_openid').val(json.items && json.items.openid || '');
                            $('#member_id').val(json.items && json.items.member_id || '');
                        }
                    }
                });
            }
        });

        wrap.find('#set_btn').off('click').on('click', function () {
            var memberid = $('#member_id').val();
            if (!memberid) {
                $.topTips({tip_text: '没有查询到会员', mode: 'tips'});
                return;
            }

            O.ajaxEx({
                url: O.path('/basic/massmsg-admin/add-admin'),
                data: 'member_id=' + memberid,
                type: 'post',
                success: function (json) {
                    if (!json.result) {
                        $.topTips({tip_text: json.msg, mode: 'tips'});
                    } else {
                        $.topTips({tip_text: '添加管理员成功'});
                        window.location.reload();
                    }
                }
            });
        });
    });

});


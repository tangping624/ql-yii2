/**
 * Created by FUYL on 2015/3/19.
 */
define(function (require, exports, module) {
    require('../lib/template');
    require('../plugin/selectuser_tree');

    var treeData = [];
    var multiSelect = true;

    var templ =
        '<span class="label js-label" id="seluser_{id}" userid="{id}" username="{name}" useraccount="{account}">{name}'
        + '<button i="close" data-id="{id}" class="x-close" title="删除">×</button>'
        + '</span>';

    var getQuery = function () {
        var str = location.search.replace("?", ""), tempArr,
            obj = {}, temp, arr = str.split("&"), len = arr.length;

        if (len > 0) {
            for (var i = 0; i < len; i++) {
                try {
                    if ((tempArr = arr[i].split('=')).length === 2) {
                        temp = decodeURIComponent(tempArr[1]);
                        obj[tempArr[0]] = temp;
                    }
                } catch (e) {
                }
            }
        }

        return obj;
    };

    var tranparams = getQuery(),
        $treeNodes = null;

    function ajaxError(errorInfo) {
        alert('请求服务数据出错！');
    }

    function path(url) {
        var token = tranparams.___token;
        return '/' + token + (url.charAt(0) === '/' ? '' : '/') + url;
    }

    function ajax(url, data, success) {
        $.ajax({
            url: path(url),
            type: 'POST',
            async: false,
            success: success,
            data: data,
            dataType: "json",
            error: ajaxError
        });
    }

    function getAllOrganId() {
        var idArray = [];
        $('#org_tree .tree-text').each(function (i, item) {
            idArray.push($(item).attr('value'));
        });
        return idArray;
    }

    function refreshOrgTree() {
        var tableTitleTemplate;
        if (multiSelect) {
            tableTitleTemplate = $('#table_title_templ');
        }
        else {
            tableTitleTemplate = $('#table_title_templ_single');
        }

        var table_title_templ = tableTitleTemplate.html();
        $('#table_title').html(table_title_templ);
        ajax("/widgets/getorgtree",
            {"range": tranparams.range ? tranparams.range : ''},
            function (data) {
                treeData = data;
                if (data.length > 0) {
                    org_id = data[0].value;
                }

                $('.js-tree').tree({
                    iClick: function (o) {
                        !$treeNodes && ($treeNodes = $('.tree-node'));
                        $treeNodes.removeClass('on');
                        $(o).closest('.tree-node').addClass('on');
                        org_id = $(o).attr("value");
                        refreshUserGrid(1);
                    }
                });
                if (tranparams.range && tranparams.range === 'self') {
                    orgArray = getAllOrganId();
                }
            });

    }

    var $keyword = $("#keyword"),
        org_id = "",
        orgArray = [];

    function getSearchData(mode) {
        if (mode == 1) {
            return {'org_id': org_id};
        }
        if (mode == 2) {
            var val = $.trim($keyword.val());
            val = val === $keyword.attr('placeholder') ? '' : val;
            return {'keyword': val, org_id: orgArray.length > 0 ? orgArray : ''};
        }
    }

    function refreshUserGrid(mode) {
        var tableContentTemplate;
        if (multiSelect) {
            tableContentTemplate = $('#table_templ');
        }
        else {
            tableContentTemplate = $('#table_templ_single');
        }

        var table_templ = tableContentTemplate.html();
        var searchData = getSearchData(mode);

        ajax(
            '/widgets/getusers',
            searchData,
            function (data) {
                $('#table_content').html(Template(table_templ, data));

                if (multiSelect) {
                    $(".form-checkbox").each(function (index) {
                        var id = $(this).attr('data-id');
                        if ($('#seluser_' + $(this).attr('data-id')).length > 0) {
                            $(this).click();
                        }
                    });
                }
                else {
                    $(".table-li").each(function (index) {
                        var id = $(this).attr('data-id');
                        if ($('#seluser_' + $(this).attr('data-id')).length > 0) {
                            $(this).click();
                        }
                    });
                }
            }
        )
    }

    function initParams() {
        if (parent && parent.ProxyUser != undefined && parent.ProxyUser.multiSelect != undefined) {
            multiSelect = parent.ProxyUser.multiSelect;
        }

        initDefaultParams();
    }

    function initDefaultParams() {
        //附加默认选中参数
        if (parent && parent.ProxyUser && parent.ProxyUser.defaultParams) {
            var data = parent.ProxyUser.defaultParams;
            for (var i = 0; i < data.length; i++) {
                addSelectedItem(data[i].id, data[i].name, data[i].account);
            }

            $selecteduser.html() === '' ?
                $selecteduser.addClass('hide') :
                $selecteduser.removeClass('hide');

            refrushHeight();
        }
    }

    function addSelectedItem(id, name, account) {
        if ($('#seluser_' + id).length == 0) {
            $('#selecteduser').append(
                templ.replace(/\{id\}/g, id)
                    .replace(/\{name\}/g, name)
                    .replace(/\{account\}/g, account)
            );
        }
    }

    function searchUser() {
        refreshUserGrid(2);
    }

    var bindEvent = function () {
        $("#search").on("click", searchUser);
        if (multiSelect) {
            $('body').on('click', '.form-checkbox', checkboxHandler);
        }
        else {
            $('body').on('click', '.table-li', rowHandler);
            $('body').on('mouseover', '.table-li', function (e) {
                e.preventDefault();
                var $this = $(this);
                $this.addClass('table-mouse-over');
            });
            $('body').on('mouseout', '.table-li', function (e) {
                e.preventDefault();
                var $this = $(this);
                $this.addClass('table-mouse-over');
            });
        }
        $('body').on('click', '.x-close', closeHandler);
        $('#btn_ok').on('click', confirmSelection);

        $('#btn_cancel').on('click', function () {
            parent && parent.ProxyUser && parent.ProxyUser.cancel();
        });
    };

    function confirmSelection() {
        var ids = "";
        var names = "";
        var accounts = "";
        $("span.js-label").each(function (index) {
            if (ids == "") ids += $(this).attr("userid");
            else ids += ";" + $(this).attr("userid");

            if (names == "") names += $(this).attr("username");
            else names += ";" + $(this).attr("username");

            if (accounts == "") accounts += $(this).attr("useraccount");
            else accounts += ";" + $(this).attr("useraccount");
        });

        if (ids == "") {
            alert('请选择用户！');
            return;
        }

        var data = {
            "ids": ids, // 用户id字符串，分号隔开
            "names": names, // 用户名称字符串，分号隔开
            "accounts": accounts // 用户代码字符串，分号隔开
        };

        data = $.extend(tranparams, data);
        parent && parent.ProxyUser && parent.ProxyUser.ok(data);
    }

    var $selecteduser = $('#selecteduser'),
        _height = $selecteduser.height();

    function rowHandler(e) {
        e.preventDefault();

        var $this = $(this);

        if (!$this.hasClass('table-select')) {
            $('#table_content').find('.table-li').each(function (index) {
                if ($(this).hasClass('table-select')) {
                    $(this).removeClass('table-select');
                }
            });

            $this.addClass('table-select');

            $("#selecteduser").find('.js-label').each(function (index) {
                $(this).remove();
            });

            $('#selecteduser').append(
                templ.replace(/\{id\}/g, $this.attr('data-id'))
                    .replace(/\{name\}/g, $this.attr('data-name'))
                    .replace(/\{account\}/g, $this.attr('data-account'))
            );
        } else {
            $this.removeClass('table-select');
            if ($('#seluser_' + $this.attr('data-id')).length > 0) {
                $('#seluser_' + $this.attr('data-id')).remove();
            }
        }

        $selecteduser.html() === '' ?
            $selecteduser.addClass('hide') :
            $selecteduser.removeClass('hide');

        refrushHeight();
    }

    function checkboxHandler(e) {
        e.preventDefault();

        var $this = $(this),
            checkbox = $(this).find('input[type="checkbox"]');

        if ($this.hasClass('selected')) {
            checkbox.prop('checked', false);
            $this.removeClass('selected');
        } else {
            checkbox.prop('checked', true);
            $this.addClass('selected');
        }

        if ($this.hasClass('selected')) {
            if ($('#seluser_' + $this.attr('data-id')).length == 0) {
                $('#selecteduser').append(
                    templ.replace(/\{id\}/g, $this.attr('data-id'))
                        .replace(/\{name\}/g, $this.attr('data-name'))
                        .replace(/\{account\}/g, $this.attr('data-account'))
                );
            }
        } else {
            if ($('#seluser_' + $this.attr('data-id')).length > 0) {
                $('#seluser_' + $this.attr('data-id')).remove();
            }
        }

        $selecteduser.html() === '' ?
            $selecteduser.addClass('hide') :
            $selecteduser.removeClass('hide');

        refrushHeight();
    }

    function closeHandler(e) {
        var id = $(this).attr('data-id');

        $('#seluser_' + id).remove();

        $selecteduser.html() === '' ?
            $selecteduser.addClass('hide') :
            $selecteduser.removeClass('hide');

        refrushHeight();

        var $label = $('.form-checkbox[data-id="' + id + '"]');
        if ($label.length > 0) {
            var $checkbox = $label.find('input[type="checkbox"]');

            if ($label.hasClass('selected')) {
                $checkbox.prop('checked', false);
                $label.removeClass('selected');
            }
        }
    }

    function refrushHeight() {
        var height = $selecteduser.height();
        if (height !== _height) {
            _height = height;
            parent && parent.ProxyUser && parent.ProxyUser.refrushHeight(height);
        }
    }

    var init = function () {
        initParams();
        bindEvent();
        refreshOrgTree();
    };

    init();
});

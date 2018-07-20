/**
 * 选择多个楼栋
 * Created by weizs on 2015/7/20.
 */
'use strict';
define(function (require, exports, module) {

    require('../../css/plugin/select-builds.css');
    require('./tree');
    require('./search-bar');

    var error = function (msg) {
        $.topTips({mode: 'warning', tip_text: msg || '出现异常'});
    }, tips = function (msg, mode, fn) {
        $.topTips({mode: mode || 'normal', tip_text: msg || '操作成功'}, fn);
    }, request = function (options) {
        return O.ajaxEx(options).error(function () {
            error();
        });
    }, cache = {};

    var tpl =
        '<div class="tree-dialog">' +
        '<div class="form form-base form-horizontal" style="max-height: 800px;">' +
        '<div class="art-box-content inner-wrap">' +
        '<div class="selected_row clearfix"></div>' +
        '<table class="table-wrap">' +
        '<thead>' +
        '<tr class="top">' +
        '<th width="36" class="search-bar">' +
        '<input type="text" class="form-control" placeholder="请输入项目名称">' +
        '<span class="x-icon x-icon-clear" id="x_clear">×</span>' +
        '<div class="search-icon" id="btn_search_project"></div>' +
        '</th>' +
        '<th width="32" class="search-bar area-column">' +
        '<input type="text" class="form-control" placeholder="请输入分区名称">' +
        '<span class="x-icon x-icon-clear" id="x_clear">×</span>' +
        '<div class="search-icon" id="btn_search_area"></div>' +
        '</th>' +
        '<th width="32" class="search-bar">' +
        '<span>楼栋</span>' +
        '<span class="check-all"></span>' +
        '</th>' +
        '</tr>' +
        '</thead>' +
        '<tbody>' +
        '<tr class="content">' +
        '<td>' +
        '<div class="js-tree" id="tree-form"></div>' +
        '</td>' +
        '<td class="area-column">' +
        '<ul id="area_list"></ul>' +
        '</td>' +
        '<td>' +
        '<ul id="room_list"></ul>' +
        '</td>' +
        '</tr>' +
        '</tbody>' +
        '</table>' +
        '<div class="art-box-footer btn-area">' +
        '<button type="button" class="btn btn-primary">确定</button>' +
        '<button type="button" class="btn btn-secondary">取消</button>' +
        '</div>' +
        '</div>' +
        '</div>';

    var getSelectedId = function (selected_row) {
        var selected = {},
            selectDomList = selected_row.find('.select-item');
        selectDomList.each(function (i) {
            selected[selectDomList.eq(i).attr('id')] = true;
        });
        return selected;
    };

    var getRooms = function (html, data, selected, first) {
        if(!data) return;

        if(data.length) {
            $.each(data, function(i, n) {
                getRooms(html, n, selected);
            });
        } else if(data.childNode && data.childNode.length) {
            $.each(data.childNode, function(i, n) {
                getRooms(html, n, selected);
            });
        } else if(data.areas && data.areas.length) {
            $.each(data.areas, function(i, n) {
                getRooms(html, n, selected);
            });
        } else if(data.items && data.items.length) {
            var _attr, _obj;
            $.each(data.items, function(i, item) {
                _obj = cache.room_mapping[item.value];
                _attr = ' data-id="' + item.value + '"';
                _attr += ' data-name="' + item.treeText + '"';
                _attr += ' data-p_id="' + _obj.p_id + '"';
                _attr += ' data-p_name="' + _obj.p_name + '"';
                _attr += _obj.a_id ? ' data-a_id="' + _obj.a_id + '"' : '';
                _attr += _obj.a_name ? ' data-a_name="' + _obj.a_name + '"' : '';

                item.is_room && 
                html.push('<li class="room-item' + (selected[item.value] ? ' on' : '') + '"' + _attr + '>' + item.treeText + '</li>');
            });
        }
    };

    var getAreas = function ($html, data, selected) {
        if(!data) return;
        var areas = data.childNode;
        if(areas && areas.length) {
            var i, childNode = null;
            for (var i = 0; i < areas.length; i++) {
                getAreas($html, areas[i], selected);
            }
        }

        areas = data.areas;
        if(areas && areas.length) {
            for (var i = 0, area = null; i < areas.length; i++) {
                area = areas[i];
                $html.append($('<li class="area-item" data-name="' + area.treeText + '" data-p_name="' + data.treeText + '" data-p_id="' + data.value + '" data-id="' + area.value + '">' + area.treeText + '</li>')
                    .data('node', area));
            }
        }
    };

    var getSelectDom = function () {
        var array = Array.prototype.slice.call(arguments), html = [];
        for (var i = 0; i < array.length; i++) {
            html.push('<span id="' + array[i].id + '" data-name="' + array[i].name + '" data-id="' + array[i].id + '" data-p_id="' + array[i].p_id + '" data-p_name="' + array[i].p_name + '"' + (array[i].a_name ? ' data-a_name="'+array[i].a_name +'"' : '') + (array[i].a_id ? ' data-a_id="'+array[i].a_id +'"' : '') + ' class="select-item">' + array[i].p_name + (array[i].a_name ? '['+array[i].a_name +']' : '') + '[' + array[i].name + ']<span class="close-btn">×</span></span>');
        }
        return html.join('');
    };

    var getRemoveSelector = function () {
        var array = Array.prototype.slice.call(arguments);
        return array.length ? '#' + array.join(',#') : null;
    };

    var checkedAll = function ($dom, $checkAll) {
        var all = $dom.find('.room-item').length;
        if (all !== 0 && all === $dom.find('.room-item.on').length) {
            $checkAll.addClass('on');
        } else {
            $checkAll.removeClass('on');
        }
    };

    var loadDefault = function (selected_row, idArray) {
        if (idArray.length) {
            var html = [];
            $.each(idArray, function (i, v) {
                html.push(cache.room_mapping[v]);
            });
            selected_row.append(getSelectDom.apply(this, html));
        }
    };

    var search = function (name, treeCache, treeForm) {
        var wrapTop = treeForm.offset().top;
        for (var i = 0; i < treeCache.length; i++) {
            if (name === treeCache[i].treeText) {
                var node = treeForm.find('[data-index=' + i + ']'),
                    column = node.closest('.column');
                //打开子节点后定位
                node.closest('.tree-item').parents('.tree-item').addClass('open');
                column.scrollTop(node.offset().top - wrapTop);
                node.trigger('click');
                return;
            }
        }
        tips('没有匹配到项目或分期', 'tips');
    };

    var searchTree = function (name, treeCache, treeForm) {
        if(treeForm.has('.tree-text:contains("' + $.trim(name) + '")').length > 0) {
            treeForm.find('.tree-item[level="1"]').addClass('hide')
                .has('.tree-text:contains("' + $.trim(name) + '")')
                .removeClass('hide').addClass('open')
                .eq(0).trigger('click');
            treeForm.scrollTop(0);
        } else {
            tips('没有匹配到项目或分期', 'tips');
        }
    };

    var searchArea = function (name, areaList) {
        if(areaList.has(':contains("' + $.trim(name) + '")').length > 0) {
            areaList.children().addClass('hide')
                .filter(':contains("' + $.trim(name) + '")')
                .removeClass('hide').eq(0).trigger('click');
            areaList.scrollTop(0);
        } else {
            tips('没有匹配到分区', 'tips');
        }
    };

    var init = function (options) {
        var finish = options.finish || function () {
                },
            data = cache.treeData,
            box = $.box({
                content: tpl,
                title: '选择楼栋',
                height: 'auto',
                width: 950
            }),
            treeDialog = $('.tree-dialog'),
            treeForm = treeDialog.find('#tree-form'),
            roomList = treeDialog.find('#room_list'),
            areaList = treeDialog.find('#area_list'),
            checkAll = treeDialog.find('.check-all'),
            selected_row = treeDialog.find('.selected_row'),
            areaColumn = treeDialog.find('.area-column'),
            tree = treeForm.tree({
                data: data,
                iClick: function (row, domNode, node) {
                    var html = [], $areaHtml = $('<ul></ul>'), selected = getSelectedId(selected_row);
                    getAreas($areaHtml, node, selected, true);
                    getRooms(html, node, selected, true);
                    if($areaHtml.children().length) {
                        areaList.empty().append($areaHtml.children());
                        areaColumn.removeClass('hide');
                    } else {
                        areaList.empty().append('<li class="area-empty">该项目或分期下无分区信息！</li>');
                        areaColumn.addClass('hide');
                    }
                    roomList.html(html.join('') || '<li class="room-empty">该项目或分期下无楼栋信息！</li>');
                    //全选勾选/取消
                    checkedAll(roomList, checkAll);
                }
            }),
            treeCache = tree.params;

        //加载默认选中值
        loadDefault(selected_row, options.idArray || []);

        treeForm.find('[data-index="0"]').trigger('click');
        areaList.off('click').on('click', '.area-item', function () {
            var $dom = $(this),
                selected = getSelectedId(selected_row),
                html = [];
                getRooms(html, $dom.data('node'), selected, true);
                roomList.html(html.join('') || '<li class="room-empty">该分区下无楼栋信息！</li>');

                //全选勾选/取消
                checkedAll(roomList, checkAll);
                $dom.addClass('active').siblings('.active').removeClass('active');
        });
        roomList.off('click').on('click', '.room-item', function () {
            var $dom = $(this),
                data = $dom.data(),
                isChecked = $dom.toggleClass('on').hasClass('on');
            if (isChecked) {
                selected_row.append(getSelectDom(data));
            } else {
                var selector = getRemoveSelector(data.id);
                if (selector) {
                    selected_row.find(selector).remove();
                }
            }
            //全选勾选/取消
            checkedAll(roomList, checkAll);
        });

        checkAll.off('click').on('click', function () {
            var isChecked = $(this).toggleClass('on').hasClass('on'),
                roomItems = roomList.find('.room-item'),
                html = [],
                ids = [];
            roomItems.each(function (i) {
                var data = roomItems.eq(i).data();
                html.push(data);
                ids.push(data.id);
            });
            var selector = getRemoveSelector.apply(this, ids);
            if (selector) {
                selected_row.find(selector).remove();
            }
            if (isChecked) {
                selected_row.append(getSelectDom.apply(this, html));
                roomItems.addClass('on');
            } else {
                roomItems.removeClass('on');
            }
        });

        selected_row.off('click').on('click', '.close-btn', function () {
            var item = $(this).parent();
            roomList.find('[data-id=' + item.attr('id') + ']').removeClass('on');
            checkedAll(roomList, checkAll);
            item.remove();
        });

        $('.search-bar').not('.area-column').searchBar(function (change, val) {
            change && searchTree(val, treeCache, treeForm);
        });

        $('.search-bar.area-column').searchBar(function (change, val) {
            change && searchArea(val, areaList);
        });

        if (!(data && data.length)) {
            treeForm.html($('<div class="empty">').text('没有数据'));
        }

        treeDialog.off('click').on('click', '.btn-area button', function () {
            var $btn = $(this);
            if ($btn.hasClass('btn-primary')) {
                var nodeList = selected_row.find('.select-item'),
                    ids = [],
                    parentIdx = [],
                    parentNodeMap = {},
                    mapText = {},
                    text = [];
                $.each(nodeList, function (i) {
                    var item = nodeList.eq(i).data(),
                        pid = item.p_id;
                    parentNodeMap[pid] = item;
                    if (!mapText[pid]) {
                        mapText[pid] = [];
                        parentIdx.push(pid);
                    }
                    mapText[pid].push(item.name);
                    ids.push(item.id);
                });
                $.each(parentIdx, function (i, v) {
                    var item = parentNodeMap[v];
                    if (item) {
                        text.push(item.p_name + (item.a_name ? '[' + item.a_name + ']' : '') + '[' + mapText[v].join(',') + ']');
                    }
                });

                finish(ids, text);
            }
            box.close();
            box.remove();
        });
    };

    var filterData = function (data, obj) {
        cache.room_mapping = cache.room_mapping || {};
        if(!data) return;
        if(data.length) {
            $.each(data, function(i, dt) {
                filterData(dt, {
                    p_name: dt.treeText,
                    p_id: dt.value
                });
            });
        } else if(data.childNode && data.childNode.length) {
            $.each(data.childNode, function(j, dt) {
                filterData(dt, $.extend(obj, {
                    p_name: dt.treeText,
                    p_id: dt.value
                }));
            });
        } else if(data.areas && data.areas.length) {
            $.each(data.areas, function(j, dt) {
                filterData(dt, $.extend(obj, {
                    a_name: dt.treeText,
                    a_id: dt.value
                }));
            });
        } else if(data.items && data.items.length) {
            $.each(data.items, function(j, dt) {
                if(dt.is_room) {
                    cache.room_mapping[dt.value] = $.extend({}, obj, {
                        name: dt.treeText,
                        id: dt.value
                    });
                }
            });
        }
    };

    /**
     * options.params={}
     * options.idArray=[]
     * options.finish=Function
     * options.data=[]
     * @param options
     */
    exports.open = function (options) {
        options = options || {};
        if (options.data) {
            filterData(options.data);
            cache.treeData = options.data;
        }
        if (cache.treeData) {
            init(options);
        } else {
            request({
                url: '/widgets/get-building-tree',
                type: 'get',
                data: options.params
            }).then(function (res) {
                filterData(res);
                cache.treeData = res;
                init(options);
            });
        }
    };

});
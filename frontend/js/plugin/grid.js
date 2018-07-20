define(function (require, exports, module) {
    var Backbone = require('../lib/backbone/backbone');
    var _ = require('../lib/backbone/underscore');
    var Pagin = require('../lib/pagin');
    window.Template = require('../lib/template');
    require('../lib/tooltips/tooltips');

    jQuery.fn.grid = function (options) {
        var el = $(this),
            singleRequest = options.singleRequest||false,//同一个grid实例是否同时只有一个请求，为true时自动abort上次请求
            url = options.url,
            sync = options.sync || false,
            getReadURL = options.getReadURL || null,
            getSort = options.getSort || null,
            notSortDefault = options.notSortDefault,
            delurl = options.delurl || '',
            templateid = options.templateid || 'grid_template',
            getTemplate = options.getTemplate || null,
            pagesize = options.pagesize || 10,
            searchText = options.searchText ? (typeof options.searchText === 'string' ? options.searchText : '正在加载中...') : false,
            emptyText = options.emptyText || '没有相关数据！',
            setEmptyText = options.setEmptyText || null,
            filter = options.filter || null,
            queryParams = options.queryParams || null,
            idField = options.idField || 'id',
            bindEvent = options.bindEvent || null,
            editCall = options.editCall || null,
            onRowClick = options.onRowClick || null,
            onRowSelect = options.onRowSelect || null,
            method = options.method || 'get',
            _pagin = null,
            loaded = options.loaded || null,
            rowClick = options.rowClick || null,//行点击事件
            sortEvent = options.sortEvent || null,//排序事件
            rowCheckEvent = options.rowCheckEvent || null,//行选中事件
            beforeRowCheckEvent = options.beforeRowCheckEvent || null,//行选中事件
            scrollLoad = options.scrollLoad || false, //是否滚动加载数据
            scrollWrapId = options.scrollWrapId || '', // 滚动区域ID
            scrolling = false,
            isCheckAll = false,      // 是否全选
            _ptFun = options.ptFun || null, // 更改提示框
            noAutoload = options.noAutoload || false; //是否禁止自动加载网格数据

        var sortHtml = '<span class="sort-wrap"><span class="sort sort-up"></span><span class="sort sort-down"></span></span>';

        // gridRow Model
        var GridRow = Backbone.Model.extend({
            idAttribute: '_id',
            defaults: {
                checked: false
            }
        })

        // gridRow View
        var GridRowView = Backbone.View.extend({
            tagName: 'tr',
            //template : _.template($('#'+templateid).html()),
            template: $('#' + templateid).html(),
            events: {
                "click .del": "delRow",
                "click .icon-edit": 'editRow',
                "click": 'clickRow'
            },
            initialize: function () {
                this.listenTo(this.model, 'toggleChecked', this.toggleChecked);
                this.listenTo(this.model, 'delModel', this.delModel);
                this.listenTo(this.model, 'change', this.render);
            },
            render: function () {
                if (this.model.changed.hasOwnProperty('checked')) return;  //checked改变不触发change事件

                //this.$el.html(this.template(this.model.toJSON()));
                this.$el.html(Template(getTemplate ? getTemplate() : this.template, this.model.toJSON()));
                if (onRowClick) {
                    this.$el.css('cursor', 'pointer');
                }
                this.$el.find('td').hover(function () {
                    var td = $(this);
                    if (td.attr('allowedit') && !td.hasClass('edittd')) {
                        td.addClass('edittd');
                        td.append('<span class="icon-merge icon-edit"></span>')
                    }
                }, function () {
                    var td = $(this);
                    if (td.attr('openedit') != 1) {
                        td.removeClass('edittd');
                        td.find('.icon-edit').hide().remove();
                    }
                })
                return this;
            },
            delModel: function () {
                var param = idField + '=' + this.model.get(idField);
                var options = {
                    url: delurl.indexOf('?') > 0 ? (delurl + '&' + param) : (delurl + '?' + param),
                    success: function (model, resp, options) {
                        appview.total--;
                    }
                }
                this.model.destroy(options);
            },
            delRow: function (e) {
                var _this = this;
                appview.tooltips($(e.target), appview.deleteTemplate(), okcall);
                function okcall() {
                    var param = idField + '=' + _this.model.get(idField);
                    if (_this.model.get('del_process')) return;
                    _this.model.set('del_process', true);
                    var options = {
                        url: delurl.indexOf('?') > 0 ? (delurl + '&' + param) : (delurl + '?' + param),
                        success: function (model, resp, options) {
                            $('.pt').hide();
                            appview.total--;
                            appview.refresh();
                        }
                    }
                    _this.model.destroy(options);
                }
            },
            editRow: function (e) {
                var _this = this;
                appview.$el.find('td').removeClass('edittd').attr('openedit', '0');
                var td = $(e.target).closest('td');
                td.attr('openedit', '1');
                td.addClass('edittd');
                var templateid = td.attr('allowedit');
                var data = this.model.toJSON();
                appview.tooltips($(e.target), appview.tipswrap(Template($('#' + templateid).html(), data)), okcall, cancelcall, data);

                function okcall() {
                    var result = editCall && editCall(templateid, _this.model, appview, td);
                    if (result) {
                        closeEdit();
                        $('.pt').hide();
                    }
                }

                function cancelcall() {
                    closeEdit();
                }

                function closeEdit() {
                    td.attr('openedit', '0');
                    td.removeClass('edittd');
                    td.find('.icon-edit').hide().remove();
                }
            },
            clickRow: function (e) {
                if (onRowClick) {
                    onRowClick(this.model, e);
                } else if (rowClick) {
                    if (e.target.tagName.toLowerCase() !== 'td') {
                        return true;
                    }

                    //如果存在选中的行则点击行的时候 不触发
                    if (appview.getSelecteds().length == 0) {
                        switch (e.target.className) {
                            case 'icon-checkbox':
                            case 'form-checkbox-input':
                                return;
                            default :
                                break;
                        }
                        rowClick(this.model);
                        this.$el.addClass('row-bg').attr('data-bg', true).siblings().removeClass('row-bg').removeAttr('data-bg');
                    }
                    else {
                        //勾选当前选中的行
                        var formcheckbox = this.$el.find('.form-checkbox');
                        var ischecked = formcheckbox.hasClass('selected');
                        if (ischecked) {
                            formcheckbox.removeClass('selected');
                            var $currRow = formcheckbox.parents('tr');
                            if ($currRow.hasClass('row-bg')) {
                                $currRow.removeClass('row-bg');
                            }
                            this.model.trigger('toggleChecked', false);
                        }
                        else {
                            formcheckbox.addClass('selected');
                            var $currRow = formcheckbox.parents('tr');
                            if (!$currRow.hasClass('row-bg')) {
                                $currRow.addClass('row-bg');
                            }

                            this.model.trigger('toggleChecked', true);
                        }
                        //如果没有选中的行上面的全选按钮取消选中
                        if ($(grid.models).filter(function (i, m) {
                                return m.get("checked");
                            }).length === 0) {
                            appview.$el.find('table thead .form-checkbox').removeClass('selected');
                        }
                        if (rowCheckEvent) {
                            var obj = $.extend({}, this.model);
                            rowCheckEvent(obj);
                        }
                        return false;
                    }
                }
            },
            toggleChecked: function (checked) {
                this.model.set('checked', checked);
                if (options.onToggleCheck) {
                    options.onToggleCheck(this.model);
                }
            }
        });

        // grid Collection
        var Grid = Backbone.Collection.extend({
            model: GridRow,
            loading: function () {
                if (searchText) {
                    var colSpan = el.find('thead th:visible').length;
                    el.find('tbody').html('<tr><td class="align-c empty-td" colspan="' + colSpan + '">' + searchText + '</td></tr>');
                    el.find('.scrollTips').hide();
                    el.find('tfoot').hide();
                }
            },
            query: function (page, showLoading) {
                var searchParams = queryParams && queryParams() || '';
                searchParams = searchParams ? '&' + searchParams : '';

                var sort = this.getSort();
                var sortParams = getSort && getSort(sort) || sort;
                if (sortParams) {
                    searchParams += '&sort_list=' + sortParams;
                }

                var temp = O.getQuery(null, searchParams)
                if (temp.page) page = temp.page;
                searchParams = searchParams.replace(/page=[0-9]*(&)?/, '').replace(/\&$/, '')

                var collection = this;
                var options = {
                    url: url || (getReadURL && getReadURL()) || '',
                    data: 'page=' + page + '&pageSize=' + pagesize + searchParams + '&_t=' + (+new Date()),
                    reset: scrollLoad ? false : true,
                    page: page,
                    pagesize: pagesize,
                    add: scrollLoad ? true : false,
                    remove: scrollLoad ? false : true,
                    async: !sync,
                    filter: true,
                    ajaxDelay: 0,
                    beforeSend: function () {
                        if (showLoading) {
                            collection.loading();
                        }
                        $('.pt').hide();
                        return true;
                    },
                    preload: function () {
                        if (scrollLoad && page == 1) {
                            collection.reset();
                            var rows = $(el).find('tbody tr');
                            if (rows.length > 0) {
                                $(el).find('tbody').html('');
                            }
                        }
                    },
                    success: function (collection, resp, options) {
                        if (scrollLoad) {
                            if (resp.total == 0) {
                                if (setEmptyText) {
                                    emptyText = setEmptyText();
                                }
                                var colspan = $(el).find('thead th').length;
                                $(el).find('tbody').html('<tr><td colspan="' + colspan + '" class="empty-td align-c">' + emptyText + '</td></tr>');
                            }

                            var pages = Math.ceil(resp.total / pagesize);
                            var scrollTips = $(el).find('.scrollTips');
                            if (resp.total > 0 && page < pages) {
                                if (!scrollTips[0]) {
                                    $(el).append($('<div class="color-gray align-c scrollTips">向下滚动加载数据...</div>'));
                                } else {
                                    scrollTips.show();
                                }
                            } else {
                                scrollTips.hide().remove();
                            }

                            //执行全选操作
                            appview.checkAllOpera();
                            if (rowCheckEvent) {
                                rowCheckEvent(null);
                            }

                            scrolling = false;
                            loaded && loaded(options.response);
                        }
                    }
                };

                this.request = this.fetch(options);
            },
            fetch: function (options) {
                options = options ? _.clone(options) : {};
                if (options.parse === void 0) options.parse = true;
                var success = options.success;
                var preload = options.preload;
                var collection = this;
                options.success = function (resp) {
                    if (!resp.items) {
                        if (resp.data && resp.data.items) {
                            resp = resp.data;
                        } else {
                            resp.items = [];
                        }
                    }
                    var method = options.reset ? 'reset' : 'set';
                    options.total = resp.total;
                    options.response = resp;
                    if (preload) preload();

                    for (var i = 0; i < resp.items.length; i++) {
                        resp.items[i]['_id'] = new Date().getTime() + i
                    }
                    collection[method](resp.items, options);
                    if (success) success(collection, resp, options);
                    collection.trigger('sync', collection, resp, options);
                };
                options.error = function (res) {
                    if (res && res.status == 401 && res.responseText) {
                        var response = JSON.parse(res.responseText);
                        if (response && response['login_url']) {
                            top.window.location.href = response['login_url'];
                        }
                    }
                };
                return this.sync((method == 'post') ? 'create' : 'read', this, options);
            },
            getSort: function () {
                var _this = this;
                _this.sortArr = [];
                var current = $(el).find('.table th.current-sort:visible');
                if (current.length) {
                    var sort = current.attr('sort');
                    if (sort) {
                        var sortInfo = sort.split(',');
                        _this.sortArr.push({
                            field: sortInfo[0],
                            sort: sortInfo[1]
                        });
                    }
                }
                $(el).find('.table th:visible').each(function (i, th) {
                    var th = $(th);
                    if (th.attr('sort') && !th.hasClass('current-sort')) {
                        var sortInfo = th.attr('sort').split(',');
                        _this.sortArr.push({
                            field: sortInfo[0],
                            sort: sortInfo[1]
                        })
                    }
                });

                if (!current.length && notSortDefault) {
                    _this.sortArr = [];
                }

                return JSON.stringify(_this.sortArr);
            }
        }), grid = new Grid;

        // The Application
        var AppView = Backbone.View.extend({
            el: el,
            events: {
                "click .form-search .input-group-addon": "search",
                "keydown .form-search input": "enterSearch"
            },
            initialize: function () {
                this.grid = grid;
                this.GridRow = GridRow;
                this.GridRowView = GridRowView;
                this.colspan = this.$el.find('thead th').length;
                this.listenTo(grid, 'reset', this.render);
                this.listenTo(grid, 'add', this.addRow);
                if (bindEvent) bindEvent(this);
                this.initEvent();

                this.getQueryParams();
                !noAutoload && grid.query(1, true);
            },
            initEvent: function () {
                var _this = this;

                $('body').on('click', '.pt-footer .btn-primary', function () {
                    $.pt && $.pt.okcall && $.pt.okcall();
                }).on('click', '.pt-footer .btn-secondary', function () {
                    $('.pt').hide();
                    $.pt && $.pt.cancelcall && $.pt.cancelcall();
                });

                //自定义checkbox
                this.$el.on('click', 'table tbody .form-checkbox', function () {
                    var ischecked = $(this).hasClass('selected');
                    var model;
                    var index = _this.$el.find('table tbody .form-checkbox').index($(this));
                    if (index >= 0) {
                        model = grid.models[index];
                    }
                    if (ischecked) {
                        $(this).removeClass('selected');
                        //移除选中颜色 yuanl02
                        $(this).parents('tr').removeClass('row-bg');
                        if (model) {
                            model.trigger('toggleChecked', false);
                        }
                        if (isCheckAll) {
                            _this.$el.find('table thead .form-checkbox').removeClass('selected');
                            isCheckAll = false;
                        }
                    } else {
                        if (!beforeRowCheckEvent || beforeRowCheckEvent()) {
                            $(this).addClass('selected');
                            //添加选中颜色 yuanl02
                            var sRows = _this.$el.find('tr[data-bg=true]').length;
                            if (sRows) {
                                _this.$el.find('tr[data-bg=true]').removeClass('row-bg').removeAttr('data-bg');
                            }
                            $(this).parents('tr').addClass('row-bg');
                            if (model) {
                                model.trigger('toggleChecked', true);
                            }
                        }

                        var flag = true;
                        for (var i = 0; i < grid.models.length; i++) {
                            if (!grid.models[i].get('checked')) {
                                flag = false;
                                break;
                            }
                        }
                        if (flag) {
                            _this.$el.find('table thead .form-checkbox').addClass('selected');
                            isCheckAll = true;
                        } else {
                            _this.$el.find('table thead .form-checkbox').removeClass('selected');
                            isCheckAll = false;
                        }
                    }

                    //如果没有选中的行上面的全选按钮取消选中
                    if ($(grid.models).filter(function (i, m) {
                            return m.get("checked");
                        }).length === 0) {
                        _this.$el.find('table thead .form-checkbox').removeClass('selected');
                    }
                    if (rowCheckEvent) {
                        var obj = $.extend({}, model);
                        rowCheckEvent(obj);
                    }
                    return false;//阻断冒泡的响应
                })

                //全选
                this.$el.on('click', 'table thead .form-checkbox', function () {
                    var checkboxs = _this.$el.find('table tbody .form-checkbox');
                    //查找行
                    var $rows = _this.$el.find('table tbody tr');
                    var ischecked = $(this).hasClass('selected');
                    if (!ischecked) {
                        isCheckAll = true;
                        //checkboxs.find('input[type="checkbox"]').prop('checked', true);
                        $(this).addClass('selected');
                        checkboxs.addClass('selected');
                        for (var i = 0; i < grid.models.length; i++) {
                            grid.models[i].trigger('toggleChecked', true);
                            //添加选中颜色 yuanl02
                            $rows.eq(i).addClass('row-bg');
                        }
                    } else {
                        isCheckAll = false;
                        $(this).removeClass('selected');
                        //checkboxs.find('input[type="checkbox"]').prop('checked', false);
                        checkboxs.removeClass('selected');

                        //移除选中颜色 yuanl02
                        $rows.removeClass('row-bg');
                        for (var j = 0; j < grid.models.length; j++) {
                            grid.models[j].trigger('toggleChecked', false);
                        }
                    }

                    if (rowCheckEvent) {
                        rowCheckEvent(null);
                    }
                    return false;
                })


                this.sort();

                //滚动加载数据
                if (scrollLoad && scrollWrapId) {
                    var scrollObj = $('#' + scrollWrapId);
                    scrollObj.scroll(function () {
                        var $this = $(this),
                            viewH = $(this).height(),
                            contentH = $(this).get(0).scrollHeight,
                            scrollTop = $(this).scrollTop();
                        if (scrollTop / (contentH - viewH) >= 0.95) { //到达底部100px时,加载新内容
                            if (!scrolling) {
                                scrolling = true;
                                _this.scrollPage();
                            }
                        }
                    });
                }
            },
            //是否全选
            getIsCheckAll: function () {
                return isCheckAll;
            },
            //全选操作
            checkAllOpera: function () {
                if (isCheckAll) {
                    var checkboxs = this.$el.find('table tbody .form-checkbox');
                    //查找行
                    var $rows = this.$el.find('table tbody tr');
                    checkboxs.addClass('selected');
                    for (var i = 0; i < grid.models.length; i++) {
                        grid.models[i].trigger('toggleChecked', true);
                        //添加选中颜色 yuanl02
                        $rows.eq(i).addClass('row-bg');
                    }
                } else {
                    var checkboxs = this.$el.find('table thead .form-checkbox');
                    if (checkboxs.hasClass('selected')) {
                        checkboxs.removeClass('selected');
                    }
                }
            },
            search: function () {
                isCheckAll = false;
                if (queryParams) {
                    if(singleRequest && this.grid.request){
                        this.grid.request.abort();
                    }
                    grid.query(1, true);
                }
            },

            //获取url后的参数
            getQueryParams: function () {
                var data = {}
                    , str = queryParams && queryParams() || ''
                    , type = $.type(str);
                if (str && type === 'string') {
                    var arr = str.split('&');
                    for (var i = 0; i < arr.length; i++) {
                        var currArr = arr[i].split('=');
                        if (currArr.length > 1) {
                            var key = currArr[0]
                                , value = currArr[1];
                            if (value !== "") {
                                data[key] = value;
                            }
                        }
                    }
                    return data;
                } else if (type == 'object') {
                    return str;
                }
            },
            scrollPage: function (page) {
                var gotopage = this.page + 1;
                if (page) {
                    gotopage = page;
                }

                if (this.total) {
                    var pages = Math.ceil(this.total / pagesize);
                    if (gotopage <= pages) {
                        grid.query(gotopage);
                    }
                } else {
                    grid.query(gotopage);
                }
            },
            enterSearch: function (event) {
                var keycode = event.which;
                if (keycode == 13) {
                    this.search();
                }
            },
            refresh: function () {
                isCheckAll = false;
                if (!scrollLoad) {
                    var pages = Math.ceil(this.total / pagesize);
                    if (this.page <= pages) {
                        grid.query(this.page);
                    } else {
                        grid.query(1);
                    }
                } else {
                    this.scrollPage(1);
                    //this.$el.find('thead')[0].scrollIntoView();
                    if (this.$el.scrollTop()) {
                        this.$el.scrollTop(0);
                    }
                }
            },
            // 排序
            sort: function () {
                this.$el.find('.table th').each(function (i, th) {
                    var th = $(th);
                    if (th.attr('sort')) {
                        th.append($(sortHtml)).addClass('js-th-sort');
                    }
                })

                this.$el.find('.table .js-th-sort').off('click').on('click', function (e) {

                    var isTH = this.tagName == 'TH',
                        th = isTH ? $(this) : $(this).closest('th'),
                        sortText = th.attr('sort') || '',
                        sortInfo = sortText.split(','),
                        sort,
                        sortWrap = th.find('span.sort-wrap');

                    if (!sortText) return !1;

                    //锁定当前排序列
                    th.addClass('current-sort').siblings().removeClass('current-sort');

                    if (isTH) {
                        sortWrap.hasClass('down') ? ( sort = 'asc', sortWrap.removeClass('down').addClass('up') ) : ( sort = 'desc', sortWrap.removeClass('up').addClass('down') )
                    } else {
                        $(this).hasClass('sort-up') ? ( sort = 'asc', sortWrap.removeClass('down').addClass('up') ) : ( sort = 'desc', sortWrap.removeClass('up').addClass('down') )
                    }

                    th.attr('sort', sortInfo[0] + ',' + sort);

                    O.emit('th:sort')
                    //grid在生成的时候，sort-wrap没有给默认的class...
                    //sortWrap.toggleClass('down').toggleClass('up');

                    return sortEvent && sortEvent(), grid.query(1), !1

                })
            },
            getSelecteds: function () {
                var selectedRows = [];
                for (var i = 0; i < grid.models.length; i++) {
                    var model = grid.models[i];
                    if (model.get('checked')) {
                        selectedRows.push(model);
                    }
                }
                return selectedRows;
            },
            //设置选中某个复选框
            setSelected: function (models) {
                var _this = this;
                if (models.length == 0) return;
                for (var i = 0; i < models.length; i++) {
                    var model = _hasModel(models[i]);
                    if (model) {
                        var index = _.indexOf(_this.grid.models, model);
                        model.trigger('toggleChecked', true);
                        var checkbox = _this.$el.find('table tbody .form-checkbox').eq(index);
                        checkbox.addClass('selected');
                        checkbox.find('input[type=checkbox]').prop('checked', true);
                    }
                }

                function _hasModel(model) {
                    for (var i = 0; i < _this.grid.models.length; i++) {
                        var _model = _this.grid.models[i];
                        if (_model.get(idField) == model.get(idField)) {
                            return _model;
                        }
                    }
                    return null;
                }
            },
            delRow: function (model) {
                model.trigger('delModel');
            },
            render: function (collection, options) {
                var _curr = this;
                this.page = options.page;
                this.total = options.total;
                var tbody = this.$el.find('tbody');
                tbody.html('');

                var _currpage = options.page || 1;
                var models = collection.models;
                if (models.length > 0) {
                    for (var i = 0; i < models.length; i++) {
                        var model = models[i];
                        model.set('i', (_currpage - 1) * options.pagesize + i + 1);
                        if (filter) {
                            filter(model);
                        }
                        var view = new GridRowView({model: model});
                        var el = $(view.render().el);
                        if (model.get('className')) {
                            el.addClass(model.get('className'));
                        }
                        tbody.append(el);
                    }
                } else {
                    if (setEmptyText) {
                        emptyText = setEmptyText();
                    }
                    this.colspan = this.$el.find('thead th').length;
                    this.$el.find('tbody').html('<tr><td colspan="' + this.colspan + '" class="empty-td align-c">' + emptyText + '</td></tr>');
                }

                //分页
                var _pages = Math.ceil(options.total / options.pagesize);
                this.renderpage(_pages, options.total);
                _pagin && _pagin.show(_pages, function (n) {
                    grid.query(n)
                }, _currpage);

                loaded && loaded(options.response);
            },
            renderpage: function (pages, total) {
                if (!this.$el.find('tfoot')[0]) {
                    var pager = '<tfoot>'
                        + '<tr>'
                        + '    <td colspan="' + $(el).find('thead th').length + '">'
                        + '        <div class="page-box" id="' + el.attr('id') + '_pagin">'
                        + '            <span class="page-total pull-left">共' + total + '条</span>'
                        + '            <span class="page-nav-area">'
                        + '                <a href="javascript:void(0);" class="btn-pr page-first" style="display: none;"></a>'
                        + '                <a href="javascript:void(0);" class="btn-pr page-prev"><i class="arrow"></i></a>'
                        + '                <span class="page-num">1 / 1</span>'
                        + '                <a href="javascript:void(0);" class="btn-pr page-next"><i class="arrow"></i></a>'
                        + '                <a href="javascript:void(0);" class="btn-pr page-last" style="display: none;"></a>'
                        + '            </span>'
                        + '            <span class="goto-area">'
                        + '                <input type="text" class="inp">'
                        + '                <a href="javascript:void(0);" class="btn-pr bg-white page-go">跳转</a>'
                        + '            </span>'
                        + '        </div>'
                        + '    </td>'
                        + '</tr>'
                        + '</tfoot>';
                    this.$el.find('table').eq(0).append($(pager));
                    _pagin = new Pagin('#' + el.attr('id') + '_pagin');
                } else {
                    this.$el.find('tfoot .page-total').eq(0).html('共' + total + '条');
                }

                var tfoot = this.$el.find('tfoot');
                if (pages > 1) {
                    tfoot.show();
                } else {
                    tfoot.hide();
                }
            },
            addRow: function (model, collection, options) {
                this.page = options.page;
                this.total = options.total;

                var index = getIndex(model, collection);
                if (index >= 0) {
                    model.set('i', index + 1);
                }

                var tbody = this.$el.find('tbody');
                if (filter) {
                    filter(model);
                }
                var view = new GridRowView({model: model});
                tbody.append(view.render().el);

                //序号
                function getIndex(model, collection) {
                    for (var i = 0; i < collection.models.length; i++) {
                        if (model.id == collection.models[i].id) {
                            return i;
                        }
                    }
                    return -1;
                }
            },
            deleteTemplate: function () {
                var templ = '<div class="delete-info">确定删除？</div>';
                return appview.tipswrap(templ);
            },
            tipswrap: function (content) {
                return '<div class="pt-content">' + content + '</div>' +
                    '<div class="pt-footer clearfix">' +
                    '   <button type="button" class="btn btn-primary pull-left">确定</button>' +
                    '   <button type="button" class="btn btn-secondary pull-right">取消</button>' +
                    '</div>';
            },
            tooltips: function (target, template, okcall, cancelcall, data) {
                var cfg = {
                    target: target,
                    width: 286,
                    position: 'b',
                    align: 'c',
                    autoClose: false,
                    leaveClose: false,
                    content: template
                };

                _ptFun && ( cfg = $.extend(cfg, _ptFun(target, data)) );
                $.pt(cfg);

                $.pt.okcall = okcall && okcall || function () {
                    };
                $.pt.cancelcall = cancelcall && cancelcall || function () {
                    };
            }
        });

        var appview = new AppView;
        appview.Pagin = _pagin

        return appview;
    }
})
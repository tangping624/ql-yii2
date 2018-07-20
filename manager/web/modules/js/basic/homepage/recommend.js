/**
 * Created by weizs on 2015/12/16.
 */
'use strict';
/*global O,$,define,plupload*/
define(function (require, exports, module) {
    var utils = require('../../../../frontend/js/lib/utils');
    var template = require('../../../../frontend/js/lib/template');
    require('../../../../frontend/js/lib/dialog');
    require('../../../../frontend/js/plugin/form.js');

    var imgTpl = '<div class="img-wrap" id="<%-id%>"><div class="img-box"><div class="box-inner"><p class="wait">等待中...</p><div class="per"><span class="pct"></span></div></div></div></div>',
        form = $('#form'),

        recommend_left = $('#recommend_left'),
        group_name = recommend_left.find('#group_name'),
        group_name_text = group_name.find('span'),
        areaList = recommend_left.find('.area-list'),
        recommend_main = $('#recommend_main'),
        contentInner = recommend_main.find('.content-inner'),
        arrow = recommend_main.find('#arrow'),
        submit_btn = form.find('#submit_btn'),

        left_tpl = $('#left_tpl').html(),
        main_tpl = $('#main_tpl').html(),
        group_name_tpl = $('#group_name_tpl').html(),
        layout_tpl = $('#layout_tpl').html(),
        count = ((O.getQueryStr('count') || 0) - '');

    var groupId = O.getQueryStr('id');

    var area_tpl = ['',
        $('#area1_tpl').html(),
        $('#area2_tpl').html(),
        $('#area3_tpl').html(),
        $('#area4_tpl').html()
    ];

    var view = {
        _cache: {},
        _change: function (wrap, percent) {
            var wait = wrap.find('.wait'),
                pct = wrap.find('.pct');
            wait.text(percent + '%');
            pct.css('width', percent + '%');
        },
        _uploader: function (wrap) {
            var _self = this,
                showWrap = _self.wrap,
                $wrap = $(wrap),
                _imgWraps = $wrap.find('.img-wraps');
            var _uploader = utils.upload({
                browse_button: 'upload_btn',
                container: wrap,
                multi_selection: false,
                url: O.path('/basic/upload/upload-image'),
                filters: [{title: '图片文件', extensions: 'jpg,jpeg,png,gif'}],
                init: {
                    FilesAdded: function (up, files) {
                        var flag = false;
                        plupload.each(files, function (file) {
                            if (file.size / 1024 > 10240) {
                                flag = 'size';
                                return false;
                            }
                            if (['image/jpeg', 'image/png', 'image/gif'].indexOf(file.type) < 0) {
                                flag = 'type';
                                return false;
                            }
                        });
                        if (flag) {
                            plupload.each(files, function (file) {
                                up.removeFile(file);
                            });
                        }
                        if (flag === 'size') {
                            $.topTips({mode: 'tips', tip_text: '每张最大支持10MB'});
                            return;
                        }
                        if (flag === 'type') {
                            $.topTips({mode: 'tips', tip_text: '只支持jpg/gif/png格式的图片'});
                            return;
                        }
                        plupload.each(files, function (file) {
                            _imgWraps.html(template(imgTpl, {id: file.id}));
                        });
                        _uploader.start();
                    },
                    FileUploaded: function (up, file, info) {
                        var data = JSON.parse(info.response) || {};
                        if (data.status - 0 === 0 || !data.original) {
                            $('#' + file.id).html('<span>上传失败</span>');
                        } else {
                            $('#' + file.id).find('.img-box').html('<img src="' + utils.image(data.original, 172) + '">');
                            $wrap.find('#img_url').val(data.original).keyup();
                            var img = showWrap.find('img');
                            if (img.length) {
                                img.attr('src', data.original);
                            } else {
                                var mask = showWrap.find('.item-mask').clone();
                                showWrap.html('<img class="img" src="' + data.original + '">').append(mask);
                            }
                        }
                        _uploader.refresh();
                    },
                    UploadProgress: function (up, file) {
                        _self._change($wrap, file.percent);
                    },
                    Error: function (up, err) {
                        if (err.code === -601) {
                            $.topTips({mode: 'tips', tip_text: '只支持jpg/gif/png格式的图片'});
                        } else {
                            $.topTips({tip_text: err.message, mode: 'tips'});
                        }
                    }
                }
            });
            _uploader.init();
        },
        _getData: function (id) {
            return O.ajaxEx({
                url: O.path('/basic/homepage/get-recommend'),
                type: 'get',
                data: {
                    id: id
                }
            }).then(function (res) {
                return res[0];
            });
        },
        _renderArea: function (area, render) {
            var html = template(left_tpl, {
                id: area.id || '',
                ad_count: area.ad_count,
                inner: template(area_tpl[area.ad_count], {
                    banners: area.banner_list || []
                })
            });
            if (render) {
                areaList.append(html);
            }
            return html;
        },
        _renderContent: function (data) {
            var html = [],
                area_list = data.area_list;

            //设置文本
            group_name_text.text(data.name);
            group_name.data(data);

            for (var i = 0; i < area_list.length; i++) {
                var area = area_list[i];
                html.push(this._renderArea(area));
            }

            areaList.html(html.join(''));
        },
        _render: function (res) {
            this._cacheData(res);
            if (res) {
                this._renderContent(res);
            } else {
                group_name_text.text('推荐位' + (count + 1));
                group_name.data({
                    name: '推荐位' + (count + 1),
                    sort: count,
                    id: ''
                });
            }
            form.removeClass('hide');
        },
        //缓存数据方便存取
        _cacheData: function (data) {
            data = data || {};
            this._cache.data = data;

            var area_cache = this._cache.area = {},
                area_list = data.area_list || [];

            for (var i = 0; i < area_list.length; i++) {
                var area = area_list[i],
                    banners = area.banner_list,
                    banner_cache = {};
                for (var j = 0; j < banners.length; j++) {
                    var banner = banners[j];
                    banner_cache[banner.id] = banner;
                }

                area_cache[area.id] = banner_cache;
            }

        },
        //显示右侧主题部分
        _showContent: function (tpl, data, marginTop) {
            contentInner.html(template(tpl, data));
            arrow.css('top', 7);
            recommend_main.css('marginTop', marginTop);
            recommend_main.removeClass('hide');
        },
        _mainValid: function () {
            var _self = this;
            form.form({
                submitbtn: 'submit_btn',
                formName: 'form',
                rules: [
                    {
                        id: 'href',
                        msg: {url_error: '请输入正确的链接地址'},
                        fun: function () {
                            var url = this.value;
                            if (url !== '') {
                                url = url.indexOf('http://') === 0 || url.indexOf('https://') === 0 ? url : 'http://' + url;
                                if (!utils.isURL(url)) {
                                    return 'url_error';
                                }
                                _self.wrap.find('.edit').attr('data-href', url).data('href', url);
                            }
                        }
                    },
                    {
                        id: 'img_url',
                        required: true,
                        msg: {required: '请上传图片'}
                    }
                ],
                validate: function () {
                    var uploadimg = recommend_main.find('.uploadimg-wrap');
                    return !!(uploadimg.length && uploadimg.is(':visible'));
                }
            });

            this._uploader(recommend_main.find('.uploadimg-wrap').get(0));
        },
        _layoutValid: function () {
            var _self = this,
                layout_selector = recommend_main.find('#layout_selector');

            form.form({
                submitbtn: 'layout_submit',
                formName: 'form',
                rules: [{
                    id: 'layout_selector',
                    msg: {required: '请选择模块类型'},
                    fun: function () {
                        if (!layout_selector.parent().find('.selected input').length) {
                            return 'required';
                        }
                    }
                }],
                submit: function (param, data, node) {
                    if (node.hasClass('btn-primary')) {
                        _self._renderArea({
                            ad_count: data.layout
                        }, true);
                    }
                    recommend_main.addClass('hide');
                }
            });

            form.off('click', '.form-radio,#layout_cancel').on('click', '.form-radio', function () {
                layout_selector.keyup();
            }).on('click', '#layout_cancel', function () {
                recommend_main.addClass('hide');
            });

        },
        _nameValid: function () {
            form.form({
                submitbtn: 'submit_btn',
                formName: 'form',
                rules: [
                    {
                        id: 'group_name',
                        msg: {required: '请输入分组名称'},
                        fun: function () {
                            group_name.data('name', this.value);
                            group_name_text.html(this.value ? this.value : '&nbsp;');
                            if (this.value === '') {
                                return 'required';
                            }
                        }
                    }
                ]
            });
        },
        _bindEvent: function () {
            var _self = this;
            //添加动作
            recommend_left.on('click', '.add', function () {
                var $this = $(this),
                    pos = $this.position().top;
                _self._showContent(layout_tpl, {}, pos);
                _self._layoutValid();
            });

            //删除动作
            recommend_left.on('click', '.del', function () {
                $(this).closest('.area-item').remove();
                recommend_main.addClass('hide');
            });

            //编辑动作
            recommend_left.on('click', '.edit', function () {
                var $this = $(this),
                    id = $this.data('id'),
                    name = $this.data('name'),
                    wrap = $this.closest('.mask-wrap'),
                    pos = wrap.position().top;

                if (id === 'name') {
                    _self._showContent(group_name_tpl, group_name.data(), pos);
                    _self._nameValid();
                } else {
                    var normal = {name: name, href: '', img_url: ''},
                        areaId = $this.closest('.area-item').data('id'),
                        banner_cache = _self._cache.area[areaId],
                        data = banner_cache && banner_cache[id] || normal;

                    data.tip = wrap.data('tip');
                    data.img_url = wrap.find('.img').attr('src') || '';
                    data.href = $this.data('href');
                    
                    _self.wrap = wrap;
                    _self._showContent(main_tpl, data, pos);
                    _self._mainValid();
                }

            });

            form.on('click', '#submit_btn', function () {
                _self._saveData($(this));
            });

        },
        //构造数据
        _consData: function () {
            var valid = true,
                group_data = group_name.data(),
                area_items = areaList.find('.area-item'),
                data = {
                    id: group_data.id,
                    name: group_data.name,
                    sort: group_data.sort,
                    area_list: []
                };

            if (!data.name || data.name === '') {
                valid = false;
                $.topTips({tip_text: '分组名称不能为空', mode: 'tips'});
            }

            area_items.each(function (i) {
                var area_item = area_items.eq(i),
                    img_items = area_item.find('.mask-wrap'),
                    item_data = area_item.data();
                item_data.sort = i;
                item_data.banner_list = [];

                img_items.each(function (idx) {
                    var img_item = img_items.eq(idx),
                        img_data = img_item.find('.edit').data();

                    img_data.img_url = img_item.find('.img').attr('src');

                    if (img_data.img_url && img_data.img_url !== '') {
                        item_data.banner_list.push(img_data);
                    }

                });

                if (item_data.banner_list.length) {
                    data.area_list.push(item_data);
                } else {
                    valid = false;
                    $(window).scrollTop(area_item.offset().top);
                    $.topTips({tip_text: '每个模块至少添加一个推荐位内容', mode: 'tips'});
                }

            });

            if (valid && data.area_list.length) {
                return JSON.stringify([data]);
            } else {
                if (valid) {
                    valid = false;
                    $.topTips({tip_text: '每个分组至少添加一个模块', mode: 'tips'});
                }
            }

            return valid;
        },
        //保存
        _saveData: function (btn) {
            var data = this._consData();

            if (data) {
                O.ajaxEx({
                    node: btn,
                    url: O.path('/basic/homepage/save-recommend'),
                    type: 'post',
                    data: {
                        recommend_list: data
                    }
                }).then(function (res) {
                    if (res.result) {
                        $.topTips({tip_text: res.msg});
                        setTimeout(function () {
                            window.location.href = O.path('/basic/homepage/index#home_recommend');
                        }, 1000);
                    } else {
                        $.topTips({tip_text: res.msg, mode: 'tips'});
                    }
                });
            }

        },
        init: function () {
            var _self = this;
            _self._getData(groupId).then(function (res) {
                _self._render(res);
            });
            _self._bindEvent();
        }
    };

    view.init();
});
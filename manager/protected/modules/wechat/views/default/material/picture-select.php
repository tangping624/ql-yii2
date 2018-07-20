<?php
/**
 * Created by PhpStorm.
 * User: weizs
 * Date: 2015/6/15
 * Time: 17:31
 */
$request = Yii::$app->request;
$accountId = $request->get('account');
$multi = $request->get('multi');
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="prefix" content="<?= Yii::$app->response->getHeaders()->get('prefix') ?>">
    <title>选择图片</title>
    <link href="/frontend/css/bootstrap/dist/css/bootstrap.min.css?v=b547efe9b3" rel="stylesheet">
    <link rel="stylesheet" href="/modules/css/wechat/material/mpnews.min.css?v=1acbd3db28"/>
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/frontend/js/lib/compatible.js"></script>
    <![endif]-->
</head>
<body>
<input type="hidden" name="account_id" id="account_id" value="<?= $accountId ?>"/>
<input type="hidden" name="multi" id="multi" value="<?= $multi ?>"/>

<div class="art-box pic-selector">
    <div class="art-box-content selector-content" style="padding: 0;">
        <div class="table-cell selector-left">
            <div class="left-inner">
                <ul id="group_list">
                    <li class="on">未分组<span class="gray">(0)</span></li>
                </ul>
            </div>
        </div>
        <div class="table-cell selector-right">
            <div class="right-inner">
                <div class="upload-img clearfix">
                    <div class="pull-right">
                        <div class="tips">建议尺寸：900像素 * 500像素</div>
                        <a href="javascript:;" class="btn btn-primary" id="dialog_upload_btn">本地上传</a>
                        <div class="process" style="right:20px;"></div>
                    </div>
                </div>
                <div class="scroll-wrap clearfix">
                    <div class="img-content clearfix"></div>
                </div>
                <div class="page-row clearfix" id="page_row" style="visibility: hidden;">
                    <div class="page-box">
                        <span class="page-nav-area">
                            <a href="javascript:;" class="btn-pr page-prev hidden">
                                <i class="arrow"></i>
                            </a>
                        <span class="page-num"></span>
                            <a href="javascript:;" class="btn-pr page-next hidden">
                                <i class="arrow"></i>
                            </a>
                        </span>
                        <span class="goto-area">
                            <input type="text" class="inp">
                            <a href="javascript:;" class="btn-pr bg-white page-go">跳转</a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="art-box-footer">
        <span style="position: absolute;left: 20px; line-height: 34px;">已选<span id="limit"
                                                                                data-limit="<?= $multi ? 20 : 1 ?>">0</span>个，可选<?= $multi ? 20 : 1 ?>
            个</span>
        <button type="button" class="btn btn-primary btn-disable js-okbtn" id="ok_btn">确定</button>
        <button type="button" class="btn btn-secondary" id="cancel_btn">取消</button>
    </div>
</div>


<script type="text/template" id="img_item_template">
    <div class="m-item<%-selected?' selected':''%>" data-id="<%- id %>">
        <div class="m-cover">
            <img src="<%- img_url %>"/>
        </div>
        <div class="selected_mask">
            <div class="selected_mask_inner"></div>
            <div class="selected_mask_icon"></div>
        </div>
        <div class="m-name">
            <%- name %>
        </div>
    </div>
</script>
<script type="text/template" id="upload_process_template">
    <div class="process-state">
        <div class="process-tips">正在保存</div>
        <div class="wrap">
            <div class="file-info">
                <div class="file-name"><%- file_name%></div>
                <div class="file-size">(<%- file_size%>)</div>
            </div>
            <div class="bar-wrap">
                <div class="bar"></div>
            </div>
            <a href="javascript:;" class="cancel">取消</a>
        </div>
    </div>
</script>
<script type="text/javascript" src="/frontend/js/lib/global.js"></script>
<script type="text/javascript" src="/frontend/3rd/plupload/plupload.full.min.js"></script>
<script type="text/javascript">
    seajs.use(['utils', 'dialog', 'template'], function (utils) {
        // parent accessor
        var var_name = O.getQueryStr('var');
        var proxy = top[var_name];

        var error = function (msg) {
            top.$.topTips({mode: 'warning', tip_text: msg || '出现异常'});
        }, tips = function (msg, mode, fn) {
            top.$.topTips({mode: mode || 'normal', tip_text: msg || '操作成功'}, fn);
        }, request = function (options) {
            return O.ajaxEx(options).error(function () {
                error();
            });
        };
        var accountId = $('#account_id'),
            multi = $('#multi').val() - 0,
            limitEl = $('#limit'),
            limit = limitEl.data('limit');
        //初始化上传
        var initUpload = function (container, options) {
            if (container.length) {
                var imgUploader = utils.upload({
                    runtimes: 'html5,flash,silverlight,html4',
                    browse_button: options.button,
                    container: $('body').get(0),
                    url: O.path('/wechat/upload/upload-image'),
                    flash_swf_url: '/frontend/plupload/Moxie.swf',
                    silverlight_xap_url: '/frontend/plupload/Moxie.xap',
                    filters: [{title: '图片文件', extensions: 'jpg,jpeg,png,gif,bmp'}],
                    init: {
                        FilesAdded: function (up, files) {
                            var flag = false;
                            if (files[0].size / 1024 > 2048) flag = 'size';
                            if (flag) up.removeFile(files[0]);
                            if (flag == 'size') return tips('每张最大支持2M', 'tips');
                            imgUploader.start();
                            container.find('.process').html($(Template(uploadTpl, {
                                file_size: (files[0].size / 1024).toFixed(2) + 'KB',
                                file_name: files[0].name
                            })));
                            options.start && options.start.call(container, files[0]);
                            container.on('click', '.cancel', function () {
                                imgUploader.stop();
                                container.find('.process-state').remove();
                            });
                        },
                        FileUploaded: function (up, file, info) {
                            var data = JSON.parse(info.response) || {};
                            if (data.status == 0 || !data.original) {
                                tips('上传失败', 'tips');
                            } else {
                                var processBar = container.find('.process-state');
                                if (!options.not_auto_close) processBar.remove();
                                options.done && options.done.call(container, {
                                    img_url: data['original'],
                                    img_name: data['imgname']
                                }, processBar);
                            }
                        },
                        UploadProgress: function (up, file) {
                            container.find('.bar').css('width', file.percent + '%');
                            if (file.percent == 100) {
                                container.find('.cancel').remove();
                            }
                        },
                        Error: function (up, err) {
                            err.code == -601 ? tips('只支持jpg/gif/png/bmp格式的图片', 'tips') : error(err.message);
                            container.find('.process-state').remove();
                        }
                    }
                });
                imgUploader.init();
            }
        };

        var uploadTpl = $('#upload_process_template').html(),
            itemTpl = $('#img_item_template').html(),
            picSelector = $('.pic-selector'),
            okBtn = picSelector.find('#ok_btn'),
            uploaderWrap = picSelector.find('.upload-img');

        var viewList = {
            params: {
                limit: 10,
                offset: 0,
                account_id: accountId.val(),
                group_id: ''
            },
            page: 1,
            maxPage: 0,
            selected: [],
            selectedMap: {},
            dataMap: {},
            renderGroupData: function (data) {
                if (data && data.length) {
                    this.groupList.empty();
                    for (var i = 0; i < data.length; i++) {
                        var item = data[i], id = item['id'];
                        var group = $('<li>').html(item['name'] + '<span class="gray">(' + item['total'] + ')</span>').data(item).attr('data-id', item['id']).appendTo(this.groupList);
                        if (!i) group.addClass('on');
                    }
                }
            },
            renderGroup: function () {
                var $self = this;
                request({
                    url: O.path('/wechat/material/picture-group-list'),
                    type: 'post',
                    data: {
                        account_id: accountId.val()
                    }
                }).then(function (res) {
                    if (res.result) $self.renderGroupData(res.data || []);
                });
            },
            renderPictureData: function (data, insertData) {
                if (data.length) {
                    var html = [];
                    if(insertData && insertData.id){
                        this.dataMap[insertData.id] = insertData;
                        this.selected.push(insertData.id);
                        okBtn.toggleClass('btn-disable', !this.hasSelected());
                    }
                    for (var i = 0; i < data.length; i++) {
                        data[i]['selected'] = this.selected.length ? this.selected.indexOf(data[i]['id']) + 1 : 0;
                        html.push(Template(itemTpl, data[i]));
                        this.dataMap[data[i]['id']] = data[i];
                    }
                    this.groupCtInner.html(html.join(''));
                } else {
                    this.groupCtInner.html('<div class="m-empty">该分组暂时没有图片素材</div>');
                }
            },
            renderPicturePage: function (total) {
                var prevBtn = this.pageRow.find('.page-prev'),
                    nextBtn = this.pageRow.find('.page-next');
                if (total > this.params.limit) {
                    this.maxPage = Math.ceil(total / this.params.limit);
                    this.page == 1 ? prevBtn.addClass('hidden') : prevBtn.removeClass('hidden');
                    this.page == this.maxPage ? nextBtn.addClass('hidden') : nextBtn.removeClass('hidden');
                    this.pageRow.find('.page-num').text(this.page + ' / ' + this.maxPage).end().css('visibility', 'visible');
                } else {
                    this.pageRow.css('visibility', 'hidden');
                }
            },
            renderPicture: function (insertData) {
                var $self = this;
                $self.params.offset = ($self.page - 1) * $self.params.limit;
                request({
                    url: O.path('/wechat/material/picture-list'),
                    type: 'post',
                    data: $self.params
                }).then(function (res) {
                    if (res.result) {
                        var data = res.data;
                        $self.renderPictureData(data.data || [], insertData);
                        $self.renderPicturePage((data.total - '') || 0);
                    }
                });
            },
            select: function (item, multi) {
                if (item) {
                    var id = item.data('id');
                    if (id) {
                        if (multi) {
                            this.selected.push(id);
                        } else {
                            this.selected = [id];
                        }
                        limitEl.text(viewList.selected.length);
                    }
                }
            },
            un_select: function (item) {
                if (item) {
                    var id = item.data('id');
                    if (id) {
                        this.selected.splice(this.selected.indexOf(id), 1);
                        limitEl.text(viewList.selected.length);
                    }
                }
            },
            hasSelected: function () {
                return !!this.selected.length;
            },
            clearSelected: function () {
                //clear cache
                this.selected = [];
                this.selectedMap = {};
                this.dataMap = {};
            },
            getSelected: function () {
                var data = [], selected = this.selected, map = this.dataMap;
                if (selected && map) {
                    $.each(selected, function (i, v) {
                        if (map[v]) {
                            data.push(map[v]);
                        }
                    });
                }
                this.clearSelected();
                return data;
            },
            bindEvent: function () {
                var $this = this;
                $this.pageRow.off('click').on('click', '.btn-pr', function () {
                    var $btn = $(this);
                    if ($btn.hasClass('page-prev')) {
                        $this.page -= 1;
                    } else if ($btn.hasClass('page-next')) {
                        $this.page += 1;
                    } else if ($btn.hasClass('page-go')) {
                        var goInput = $btn.prev(),
                            page = goInput.val() - '';
                        if (page > 0 && page <= $this.maxPage) {
                            $this.page = page;
                        }
                        goInput.val('');
                    }
                    $this.renderPicture();
                });
                $this.pageRow.off('keyup').on('keyup', '.inp', function (e) {
                    var $inp = $(this), page = $inp.val();
                    if (e.keyCode == 13) {
                        if (page > 0 && page <= viewList.maxPage) {
                            $this.page = page;
                            $this.renderPicture();
                        }
                        $inp.val('');
                    }
                });
                $this.groupList.off('click').on('click', 'li', function () {
                    var group = $(this);
                    group.addClass('on').siblings().removeClass('on');
                    $this.params.group_id = group.data('id');
                    $this.renderPicture();
                });
            },
            render: function (dom, insertData) {
                this.groupList = dom.find('#group_list');
                this.pageRow = dom.find('#page_row');
                this.groupCtInner = dom.find('.img-content');
                this.renderPicture(insertData);
                this.renderGroup();
                this.bindEvent();
            }
        };

        viewList.render(picSelector);
        initUpload(uploaderWrap, {
            not_auto_close: true,
            button: 'dialog_upload_btn',
            done: function (data, processBar) {
                request({
                    url: O.path('/wechat/material/save'),
                    type: 'post',
                    data: {
                        account_id: accountId.val(),
                        type: 'picture',
                        data: JSON.stringify(data)
                    }
                }).then(function (res) {
                    if (res.result) {
                        tips('创建成功');
                        processBar.remove();
                        viewList.render(picSelector, res.data);
                    } else {
                        if (res.msg.indexOf('错误码:45001') > -1) {
                            tips('大小: 不超过2M, 格式: bmp, png, jpeg, jpg, gif, 高度（像素）与宽度（像素）的乘积不超过400万', 'tips');
                        } else {
                            tips(res.msg, 'tips');
                        }
                    }
                });
            }
        });

        picSelector.off('click');
        picSelector.on('click', '.m-cover', function () {
            var $target = $(this), item = $target.closest('.m-item');
            if (!multi || limitEl.text() - 0 < limit) {
                item.addClass('selected');
                viewList.select(item, multi);
                viewList.hasSelected() ? okBtn.removeClass('btn-disable') : okBtn.addClass('btn-disable');
                if (!multi) {
                    item.siblings().removeClass('selected');
                }
            }
        });
        picSelector.on('click', '.selected_mask_icon', function () {
            var $target = $(this), item = $target.closest('.m-item');
            item.removeClass('selected');
            viewList.un_select(item);
            viewList.hasSelected() ? okBtn.removeClass('btn-disable') : okBtn.addClass('btn-disable');
        });
        picSelector.on('click', '#cancel_btn,#ok_btn', function () {
            var $btn = $(this);
            if (!$btn.hasClass('btn-disable')) {
                if ($btn.hasClass('js-okbtn')) {
                    proxy.onSelect && proxy.onSelect.call(this, viewList.getSelected());
                }
                proxy.close();
                proxy.remove();
            }
        });
    });
</script>
</body>
</html>
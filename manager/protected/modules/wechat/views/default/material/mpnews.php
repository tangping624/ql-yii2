<?php
/**
 * Created by PhpStorm.
 * User: weizs
 * Date: 2015/6/2
 * Time: 12:28
 */
$request=Yii::$app->request;
$this->title = Yii::$app->params['system_name'];

$type=$request->get('type', 'single');
$view=$request->get('view', 'card');

$id=$request->get('id');
$accountId=$data;

$is_multi=$type=='multi';
?>
<?php $this->beginBlock('css') ?>
    <link rel="stylesheet" href="/modules/css/wechat/material/mpnews.min.css?v=1acbd3db28"/>
<?php $this->endBlock() ?>
    <div class="manage-content">
        <div class="padding">
            <h4 class="manage-title">素材管理</h4>
            <input type="hidden" id="account_id" value="<?=$accountId?>"/>
        </div>
        <ul class="tab-nav mb30" id="type_box">
            <li class="on" data-type="index"><span>图文消息</span></li>
            <li data-type="picture"><span>图片库</span></li>
            <li data-type="voice"><span>语音</span></li>
<!--            <li data-type="video"><span>视频</span></li>-->
        </ul>
        <div class="padding">
            <div class="breadcrumbs">
                <a href="<?=$this->context->createUrl("/wechat/material/index?view=$view") ?>" class="icon-merge icon-goback" title="返回上一层">返回上一层</a>
                <a href="<?=$this->context->createUrl("/wechat/material/index?view=$view") ?>" class="parent">图文消息</a> /
                <span><?=$id?'编辑':'新建'?>图文消息</span>
            </div>
        </div>
        <div class="padding">
            <form class="form form-preview" id="user_form">
                <div class="clearfix">
                    <div class="upload-preview multi pull-left">
                        <div class="list-wrap">
                            <div class="news-list"></div>
                            <div class="plus">
                                <a href="javascript:;" class="add">
                                    <span class="add-icon"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="form-content pull-right" id="edit_area">
                        <span class="icon-merge trigon" id="arrow"></span>
                        <input type="hidden" value="" id="id" name="id">
                        <div class="form-group">
                            <label for="title">标题</label>
                            <input type="text" class="form-control" id="title" name="title" value="">
                        </div>
                        <div class="form-group">
                            <label for="author">作者<span class="gray">（选填）</span></label>
                            <input type="text" class="form-control" id="author" name="author" value="">
                        </div>
                        <div class="form-group">
                            <label for="cover_url">封面<span class="gray">（大图片建议尺寸：900像素 * 500像素）</span></label>
                            <input type="hidden" id="cover_url" name="cover_url" value="">
                            <input type="hidden" id="cover_name" name="cover_name" value="">
                            <div class="upload-wrap" id="upload_wrap">
                                <div class="button-area">
                                    <button type="button" class="btn btn-secondary" id="upload_btn">上传</button>
                                    <button type="button" class="btn btn-secondary" id="img_lib_btn">从图片库选择</button>
                                    <div class="process"></div>
                                </div>
                                <div class="preview-area"></div>
                            </div>
                            <label class="form-checkbox" id="show_cover">
                                <i class="icon-checkbox"></i>
                                <span class="align-m gray">封面图片显示在正文中</span>
                                <input type="checkbox" class="form-checkbox-input">
                                <input type="hidden" id="is_cover_showin_body" name="is_cover_showin_body" value="0">
                            </label>
                        </div>
                        <div class="form-group summary">
                            <label for="summary">摘要<span class="gray">（选填，该摘要只在发送图文消息为单条时显示）</span></label>
                            <textarea name="summary" id="summary" class="form-control" maxlength="120"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="body">正文</label>
                            <textarea name="body" id="body"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="original_url">原文链接<span class="gray">（选填）</span></label>
                            <input type="text" class="form-control" id="original_url" name="original_url" value="">
                        </div>
                        <div class="form-group">
                            <label for="share_point">分享朋友圈送积分</label>
                            <input type="text" class="form-control" id="share_point" name="share_point" value="">
                        </div>
                        <div class="form-group hide" id="share_url_wrap">
                            <label for="share_url">分享朋友圈送积分链接</label>
                            <div class="form-inline">
                                <a href="javascript:;" class="copy-button">复制链接</a>
                                <input type="hidden" id="share_url" name="share_url" value="">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-bottom align-c">
                    <button type="button" class="btn btn-primary" id="save_btn"><span class="icon-loading"></span>保存</button>
                    <button type="button" class="btn btn-secondary" id="preview_btn"><span class="icon-loading"></span>预览</button>
                    <button type="button" class="btn btn-secondary" id="save_send_btn"><span class="icon-loading"></span>保存并群发</button>
                    <button type="button" class="btn btn-secondary" id="back_list"><span class="icon-loading"></span>关闭</button>
                </div>
            </form>
        </div>
    </div>

    <script type="text/template" id="empty_template">
        <div class="imgtext<%-index?'':' on'%>">
            <div class="img-row clearfix">
                <div class="img-cover"><%-index?'缩略图':'封面图片'%></div>
                <h4 class="news-title<%-index?' m-text':''%>">标题</h4>
            </div>
            <div class="img-mask">
                <a href="javascript:;" class="sort sort-up"></a>
                <a href="javascript:;" class="sort sort-down"></a>
                <a href="javascript:;" class="del"></a>
            </div>
        </div>
    </script>
    <script type="text/template" id="item_template">
        <div class="imgtext<%-index?'':' on'%>">
            <div class="img-row clearfix">
                <div class="img-cover"><img src="<%- cover_url %>" /></div>
                <h4 class="news-title<%-index?' m-text':''%>"><%- title %></h4>
            </div>
            <div class="img-mask">
                <a href="javascript:;" class="sort sort-up"></a>
                <a href="javascript:;" class="sort sort-down"></a>
                <a href="javascript:;" class="del"></a>
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
    <script type="text/template" id="preview_template">
        <div id="quesClass_form">
            <div class="art-box-content preview-wrap">
                <p>关注公众号后，才能接收图文消息预览</p>
                <div class="preview-input">
                    <input class="form-control" type="tel" placeholder="请输入会员手机号码" id="mobile" maxlength="11"/>
                    <p class="tips-info f12" style="margin-top:5px;"></p>
                    <div id="store_mobile" class="store-mobile clearfix">
                        <?php
                        if(isset($preview_history)){
                            foreach ($preview_history as $item) {
                                ?>
                                <div class="mobile-item" data-mobile="<?=$item['mobile']?>"><?=$item['name'].'&nbsp;'.$item['mobile']?><!--<span class="close">×</span>--></div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="art-box-footer">
                <button type="button" class="btn btn-primary" id="submit_preview"><span class="icon-loading"></span>发送</button>
                <button type="button" class="btn btn-secondary js-cancelbtn">关闭</button>
            </div>
        </div>
    </script>
<?php $this->beginBlock('js') ?>
<script type="text/javascript" src="/modules/build/script/script-2250a0d6aa.js?v=5ba1580f52" data-build></script>
<script type="text/javascript">
        var __SCRIPT = [
            '/frontend/3rd/ueditor/ueditor.config.js',
            '/frontend/3rd/ueditor/ueditor.all.min.js',
            '/frontend/3rd/ueditor/lang/zh-cn/zh-cn.js',
            '/frontend/3rd/plupload/plupload.full.min.js'
        ];
        __REQUIRE('/modules/js/wechat/material/mpnews.js?v=a8650ec39e');
    </script>
<?php $this->endBlock() ?>
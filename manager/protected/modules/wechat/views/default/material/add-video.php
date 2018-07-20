<?php
/**
 * Created by PhpStorm.
 * User: weizs
 * Date: 2015/6/15
 * Time: 17:21
 */
$this->title = '编辑视频';
$request=Yii::$app->request;
$accountId=$request->get('account');
?>
<?php $this->beginBlock('css') ?>
    <link rel="stylesheet" href="/modules/css/wechat/material/add-video.min.css?v=cf2741c324"/>
<?php $this->endBlock() ?>
    <div class="manage-content">
        <div class="padding">
            <h4 class="manage-title">素材管理</h4>
            <input type="hidden" id="account_id" value="<?=$accountId?>"/>
        </div>
        <ul class="tab-nav mb30" id="type_box">
            <li data-type="index"><span>图文消息</span></li>
            <li data-type="picture"><span>图片库</span></li>
            <li data-type="voice"><span>语音</span></li>
            <li class="on" data-type="video"><span>视频</span></li>
        </ul>
        <div class="padding">
            <div class="breadcrumbs">
                <a href="<?=$this->context->createUrl("/wechat/material/video?account=$accountId") ?>" class="icon-merge icon-goback" title="返回上一层">返回上一层</a>
                <a href="<?=$this->context->createUrl("/wechat/material/video?account=$accountId") ?>" class="parent">视频消息</a> /
                <span>新建视频消息</span>
            </div>
        </div>
        <div class="padding">
            <form class="form" style="width: 660px;" id="video_form">
                <div class="form-group">
                    <label for="video">视频</label>
                    <input type="hidden" id="type" name="type" value="本地">
                    <input type="hidden" id="video_url" name="video_url" value="<?=$data['video_url']?>">
                    <input type="hidden" id="video_name" name="video_name" value="<?=$data['video_name']?>">
                    <div id="upload_wrap">
                        <div class="button-area">
                            <button type="button" class="btn-pr sub-btn" id="upload_btn">本地上传</button>
                            <span class="gray" style="margin-left: 20px;">大小: 不超过20M,    格式: rm, rmvb, wmv, avi, mpg, mpeg, mp4</span>
                            <div class="process"></div>
                        </div>
                        <div class="preview-area"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="title">标题</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?=$data['title']?>">
                </div>
                <div class="form-group">
                    <label for="summary">简介<span class="gray">（选填）</span></label>
                    <textarea name="summary" id="summary" class="form-control" style="resize: none; height: 110px;"><?=$data['summary']?></textarea>
                </div>
                <div>
                    <button type="button" class="btn-pr ok-btn" id="save_btn">保存</button>
                </div>
            </form>
        </div>
    </div>
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
<?php $this->beginBlock('js') ?>
<script type="text/javascript" src="/modules/build/script/script-08f5327c15.js?v=d3a6e81e66" data-build></script>
<script type="text/javascript">
        var __SCRIPT = [
            '/frontend/3rd/plupload/plupload.full.min.js'
        ];
        __REQUIRE('/modules/js/wechat/material/add-video.js?v=c1d409612b');
    </script>
<?php $this->endBlock() ?>
<?php
/**
 * Created by PhpStorm.
 * User: weizs
 * Date: 2015/6/9
 * Time: 17:59
 */
$this->title = '素材管理'; 
$accountId=$data;
?>
<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/wechat/material/voice.min.css?v=f7cd193c66"/>
<?php $this->endBlock() ?>
    <div class="manage-content">
        <div class="padding">
            <h4 class="manage-title">素材管理</h4>
              <input type="hidden" id="account_id" value="<?=$data?>"/> 
        </div>
        <ul class="tab-nav mb30" id="type_box">
            <li data-type="index"><span>图文消息</span></li>
            <li data-type="picture"><span>图片库</span></li>
            <li class="on" data-type="voice"><span>语音</span></li>
<!--            <li data-type="video"><span>视频</span></li>-->
        </ul>
        <div class="padding">
            <div class="upload-area clearfix">
                <a href="javascript:;" class="btn btn-primary btn-add" id="upload_btn">上传</a>
                <div class="tips">大小: 不超过5M,    长度: 不超过60s,    格式: mp3, wma, wav, amr</div>
                <div class="process"></div>
            </div>
            <div class="data-content clearfix" id="data_content"></div>
            <div class="page-row" id="page_row" style="visibility: hidden;">
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
    <script type="text/template" id="item_template">
        <div class="m-item" data-id="<%- id %>">
            <div class="m-content">
                <div class="m-play">
                    <a href="javascript:;" class="play-btn">
                        <span class="audio-icon"></span>
                    </a>
                </div>
                <span class="m-title"><%- name %></span>
            </div>
            <div class="m-option">
                <a href="javascript:;" class="col-md-4 download-btn" data-id="<%- id %>" data-url="<%- voice_url %>" data-title="下载">
                    <span class="m-icon download"></span>
                </a>
                <a href="javascript:;" class="col-md-4 edit-btn" data-id="<%- id %>" data-title="编辑">
                    <span class="m-icon edit"></span>
                </a>
                <a href="javascript:;" class="col-md-4 delete-btn" data-id="<%- id %>" data-title="删除">
                    <span class="m-icon delete"></span>
                </a>
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
    <script type="text/template" id="delTpl">
        <div class="pt-wrap">
            <div class="pt-content">确定删除此素材？</div>
            <div class="pt-footer clearfix">
                <button type="button" class="btn btn-primary pull-left js-okbtn">确定</button>
                <button type="button" class="btn btn-secondary pull-right">取消</button>
            </div>
        </div>
    </script>
    <script type="text/template" id="inputTpl">
        <div class="pt-wrap">
            <div class="pt-content">
                <span>编辑名称</span>
                <input type="text" class="form-control" value="<%- value %>"/>
            </div>
            <div class="pt-footer clearfix">
                <button type="button" class="btn btn-primary pull-left js-okbtn">确定</button>
                <button type="button" class="btn btn-secondary pull-right">取消</button>
            </div>
        </div>
    </script>
<?php $this->beginBlock('js') ?>
<script type="text/javascript" src="/modules/build/script/script-ccee514cb9.js?v=d3a6e81e66" data-build></script>
<script type="text/javascript">
        var __SCRIPT = [
            '/frontend/3rd/plupload/plupload.full.min.js'
        ];
        __REQUIRE('/modules/js/wechat/material/voice.js?v=ba64923d4b');
    </script>
<?php $this->endBlock() ?>
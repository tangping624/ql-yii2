<?php
/**
 * Created by PhpStorm.
 * User: weizs
 * Date: 2015/6/9
 * Time: 17:58
 */
$this->title = '素材管理'; 
$accountId=$data;
?>
<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/wechat/material/video.min.css?v=a7c2826d81"/>
<?php $this->endBlock() ?>
    <div class="manage-content">
        <div class="padding">
            <h4 class="manage-title">素材管理</h4>
             <input type="hidden" id="account_id" value="<?=$data?>"/> 
        </div>
        <ul class="tab-nav mb30" id="type_box">
            <li data-type="index"><span>图文消息</span></li>
            <li data-type="picture"><span>图片库</span></li>
            <li data-type="voice"><span>语音</span></li>
            <li class="on" data-type="video"><span>视频</span></li>
        </ul>
        <div class="padding">
            <div class="title-row">
                <h3>视频列表(共<span id="data_num">0</span>个)</h3>
            </div>
            <div class="msg-list">
                <div class="msg-col">
                    <a href="javascript:;" class="add" id="add_btn">
                        <span class="add-icon"></span>
                    </a>
                    <div class="col-inner" id="msg_list_1"></div>
                </div>
                <div class="msg-col">
                    <div class="col-inner" id="msg_list_2"></div>
                </div>
                <div class="msg-col">
                    <div class="col-inner" id="msg_list_3"></div>
                </div>
            </div>
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
        <div class="m-item">
            <div class="m-content-wrap">
                <div class="m-content">
                    <h4><a href="javascript:;"><%- title %></a></h4>
                    <div class="m-date"><%- modified_on %></div>
                    <div class="m-cover">
                        <span class="empty-video"></span>
                    </div>
                    <p class="m-desc"><%- summary %></p>
                </div>
            </div>
            <div class="m-option">
                <a href="javascript:;" class="col-md-6 edit-btn" data-id="<%- id %>" data-title="编辑">
                    <span class="m-icon edit"></span>
                </a>
                <a href="javascript:;" class="col-md-6 delete-btn" data-id="<%- id %>" data-title="删除">
                    <span class="m-icon delete"></span>
                </a>
            </div>
        </div>
    </script>
    <script type="text/template" id="delTpl">
        <div class="tips-wrap">
            <div class="content">确定删除此素材？</div>
            <div class="clearfix">
                <button type="button" class="btn-pr ok-btn pull-left">确定</button>
                <button type="button" class="btn-pr cancel-btn pull-right">取消</button>
            </div>
        </div>
    </script>
<?php $this->beginBlock('js') ?>
    <script type="text/javascript">
        __REQUIRE('/modules/js/wechat/material/video.js?v=96a36d30da');
    </script>
<?php $this->endBlock() ?>
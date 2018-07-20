<?php
/**
 * Created by PhpStorm.
 * User: weizs
 * Date: 2015/6/9
 * Time: 17:45
 */
$this->title = '素材管理'; 
$accountId=$data;
?>
<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/wechat/material/picture.min.css?v=dfcac9422a"/>
<?php $this->endBlock() ?>
    <div class="manage-content">
        <div class="padding">
            <h4 class="manage-title">素材管理</h4>
             <input type="hidden" id="account_id" value="<?=$data?>"/> 
        </div>
        <ul class="tab-nav mb30" id="type_box">
            <li data-type="index"><span>图文消息</span></li>
            <li class="on" data-type="picture"><span>图片库</span></li>
            <li data-type="voice"><span>语音</span></li>
<!--            <li data-type="video"><span>视频</span></li>-->
        </ul>
        <div class="padding">
            <div class="msg-list">
                <div class="table-cell msg-left">
                    <div class="left-inner">
                        <div class="head-row btn-reset clearfix">
                            <div class="group-info">
                                <span>未分组</span>
                                <a href="javascript:;" class="rename-group hidden">重命名</a>
                                <a href="javascript:;" class="del-group hidden">删除分组</a>
                            </div>
                            <div class="upload-wrap">
                                <div class="tips">大小: 不超过2M,    格式: bmp, png, jpeg, jpg, gif</div>
                                <a href="javascript:;" class="btn btn-primary btn-add" id="upload-img">上传</a>
                                <div class="process"></div>
                            </div>
                        </div>
                        <div class="opt-group btn-reset clearfix">
                            <label class="form-checkbox check-all">
                                <i class="icon-checkbox"></i>
                                <span class="align-m">全选</span>
                            </label>
                            <a href="javascript:;" class="btn-pr normal-btn move-select-group disabled-btn">移动分组</a>
                            <a href="javascript:;" class="btn-pr normal-btn del-select-pic disabled-btn">删除</a>
                        </div>
                        <div class="group-content">
                            <div class="group-inner clearfix"></div>
                        </div>
                        <div class="page-wrap clearfix">
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
                <div class="table-cell msg-right">
                    <div class="right-inner">
                        <ul id="group_list">
                            <li class="on">未分组<span class="gray">(0)</span></li>
                        </ul>
                        <a href="javascript:;" class="add-group"><span class="m-icon add"></span>新建分组</a>
                    </div>
                </div>
            </div>
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
    <script type="text/template" id="item_template">
        <div class="m-item">
            <div class="m-content">
                <div class="content-img">
                    <img src="<%- img_url %>" />
                </div>
                <div class="content-opt">
                    <label class="form-checkbox" data-id="<%- id %>">
                        <i class="icon-checkbox"></i>
                        <span class="align-m"><%- name %></span>
                    </label>
                </div>
            </div>
            <div class="m-option">
                <a href="javascript:;" class="col-md-4 edit-pic" data-id="<%- id %>" data-title="编辑名称">
                    <span class="m-icon edit"></span>
                </a>
                <a href="javascript:;" class="col-md-4 move-group" data-id="<%- id %>" data-title="移动分组">
                    <span class="m-icon move"></span>
                </a>
                <a href="javascript:;" class="col-md-4 del-pic" data-id="<%- id %>" data-title="删除">
                    <span class="m-icon delete"></span>
                </a>
            </div>
        </div>
    </script>
    <script type="text/template" id="delTpl">
        <div class="pt-wrap">
            <div class="pt-content"><%- tip %></div>
            <div class="pt-footer clearfix">
                <button type="button" class="btn btn-primary pull-left js-okbtn">确定</button>
                <button type="button" class="btn btn-secondary pull-right">取消</button>
            </div>
        </div>
    </script>
    <script type="text/template" id="inputTpl">
        <div class="pt-wrap">
            <div class="pt-content">
                <span><%- title %></span>
                <input type="text" class="form-control" value="<%- value %>"/>
            </div>
            <div class="pt-footer clearfix">
                <button type="button" class="btn btn-primary pull-left js-okbtn">确定</button>
                <button type="button" class="btn btn-secondary pull-right">取消</button>
            </div>
        </div>
    </script>
    <script type="text/template" id="moveGroupTpl">
        <div class="pt-wrap">
            <div class="pt-content">
                <div class="move">
                    <div class="content">
                        <% if(group&&group.length){
                        for(var i=0;i < group.length;i++){ %>
                        <label class="form-radio" data-id="<%- group[i]['id'] %>">
                            <i class="icon-radio"></i>
                            <span class="align-m"><%- group[i]['name'] %></span>
                        </label>
                        <% } }else{ %>
                        暂无可用分组
                        <% } %>
                    </div>
                </div>
            </div>
            <div class="pt-footer clearfix">
                <button type="button" class="btn btn-primary pull-left js-okbtn">确定</button>
                <button type="button" class="btn btn-secondary pull-right">取消</button>
            </div>
        </div>
    </script>
<?php $this->beginBlock('js') ?>
<script type="text/javascript" src="/modules/build/script/script-33f444e54d.js?v=d3a6e81e66" data-build></script>
<script type="text/javascript">
        var __SCRIPT = [
            '/frontend/3rd/plupload/plupload.full.min.js'
        ];
        __REQUIRE('/modules/js/wechat/material/picture.js?v=9a49541ed7');
    </script>
<?php $this->endBlock() ?>
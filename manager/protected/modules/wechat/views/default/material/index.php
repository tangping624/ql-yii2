<?php
/**
 * Created by PhpStorm.
 * User: weizs
 * Date: 2015/6/1
 * Time: 13:23
 */
$request=Yii::$app->request;
$accountId=$request->get('public_id');
$view=$request->get('view', 'card');
$view=$view==''?'card':$view;
?>
<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/wechat/material/index.min.css?v=cccd164cbd"/>
<?php $this->endBlock() ?>
    <div class="manage-content">
        <div class="padding">
            <h4 class="manage-title">素材管理</h4>
             <input type="hidden" id="account_id" value="<?=$data?>"/> 
        </div>
        <ul class="tab-nav mb30" id="type_box">
            <li class="on" data-type="index"><span>图文消息</span></li>
            <li data-type="picture"><span>图片库</span></li>
            <li data-type="voice"><span>语音</span></li>
<!--            <li data-type="video"><span>视频</span></li>-->
        </ul>
        <div class="padding">
            <div class="form clearfix">
                <div class="search-bar js-searchcon width-long">
                    <input type="text" class="search-input" placeholder="标题/作者/摘要" id="search_input">
                    <span class="x-icon x-icon-clear" id="x_clear">×</span>
                    <span class="search-btn search-icon"></span>
                </div>

            </div>
            <div class="title-row">
                <h3>图文消息(共<span id="data_num">0</span>个)</h3>
                <div class="view-type">
                    <a href="javascript:;" class="card-view<?=$view=='card'?' on':''?>" data-view="card" title="卡片式">卡片式</a>
                    <a href="javascript:;" class="list-view<?=$view=='list'?' on':''?>" data-view="list" title="列表式">列表式</a>
                </div>
                <a href="javascript:;" class="btn btn-primary pull-right" id="new_msg">新建图文消息</a>
<!--                <div class="tips">图文消息创建、删除、修改限制每天10次</div>-->
            </div>
            <div class="msg-list <?=$view?>">
                <?php
                if ($view=='card') {?>
                <div class="msg-col">
                    <div class="col-inner" id="msg_list_1"></div>
                </div>
                <div class="msg-col">
                    <div class="col-inner" id="msg_list_2"></div>
                </div>
                <div class="msg-col">
                    <div class="col-inner" id="msg_list_3"></div>
                </div>
                <?php
                } else {?>
                <div class="msg-col"></div>
                <?php
                }?>
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
    <script type="text/template" id="msgTpl">
        <?php
        if ($view=='card') {?>
            <div class="m-item<%- (articles=articles||[]).length>1?' multi':'' %>" data-index="<%- i %>" data-id="<%- id %>">
                <div class="m-content-wrap">
                    <% for(var idx=0;idx < articles.length;idx++){ %>
                    <div class="m-content">
                        <h4><a href="javascript:;"><%- articles[idx]['title'] %></a></h4>
                        <% if(!idx){ %><div class="m-date"><%- modified_on||'' %></div><% } %>
                        <div class="m-cover">
                            <img src="<%- articles[idx]['cover_url'] %>" />
                        </div>
                        <% if(!idx){ %><p class="m-desc"><%- articles[idx]['summary']||'' %></p><% } %>
                    </div>
                    <% }%>
                </div>
                <div class="m-option">
                    <a href="javascript:;" class="col-md-6 edit-btn" data-title="编辑">
                        <span class="m-icon edit"></span>
                    </a>
                    <a href="javascript:;" class="col-md-6 delete-btn" data-title="删除">
                        <span class="m-icon delete"></span>
                    </a>
                </div>
            </div>
        <?php
        } else {?>
            <div class="m-item<%- (articles=articles||[]).length>1?' multi':'' %>" data-index="<%- i %>" data-id="<%- id %>">
                <div class="m-content-wrap">
                    <div class="m-cover">
                        <img src="<%- articles[0]['cover_url'] %>" />
                    </div>
                    <div class="m-content">
                        <% for(var idx=0;idx < articles.length;idx++){ %>
                        <p><a href="javascript:;"><%-articles.length>1?idx+'. ':''%><%- articles[idx]['title'] %></a></p>
                        <% }%>
                    </div>
                    <div class="m-date"><%- modified_on||'' %></div>
                </div>
                <div class="m-option">
                    <a href="javascript:;" class="edit-btn">编辑</a>
                    <a href="javascript:;" class="delete-btn">删除</a>
                </div>
            </div>
        <?php
        }?>
    </script>
    <script type="text/template" id="delTpl">
        <div class="js-delwrap">
            <div class="pt-content">确定删除？</div>
            <div class="pt-footer clearfix">
                <button type="button" class="btn btn-primary pull-left js-okbtn">确定</button>
                <button type="button" class="btn btn-secondary pull-right">取消</button>
            </div>
        </div>
    </script>
<?php $this->beginBlock('js') ?>
<script type="text/javascript">var appmsgDataCache=null;</script>
<script type="text/javascript">
    __REQUIRE('/modules/js/wechat/material/index.js?v=1f463816f9');
</script>
<?php $this->endBlock() ?>
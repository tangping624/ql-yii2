<?php
$this->title = Yii::$app->params['system_name'];
?>
<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/wechat/fan/index.min.css?v=1f126a7f81"/>
<?php $this->endBlock() ?>

<div class="manage-content">
    <div class="padding"> 
        <h4 class="manage-title">粉丝管理</h4>
        <input type="hidden" value="<?=$account_id?>" id="account_id"> 
    </div>
    <ul class="tab-nav mb30" id="type_box">
        <li data-followed="1" class="on"><span>已关注</span></li>
        <li data-followed="0"><span>取消关注</span></li>
    </ul>
    
    <div class="padding grid">
        
        <ul class="form search-con clearfix" id="search_con">
            <li>
                <span>昵称/姓名</span>
                <input type="text" class="form-control" name="name" id="name">
            </li>
            <li>
                <span>证件号码</span>
                <input type="text" class="form-control" name="id_code" id="id_code">
            </li>
            <li>
                <span>手机</span>
                <input type="text" class="form-control" name="mobile" id="mobile">
            </li>  
            <li class="search">
                <a href="javascript:;" class="btn btn-primary" id="btn_search">搜索</a>
            </li>
        </ul>
        <div class="grid-toolbar">
            <div class="export-box clearfix">
                <div class="pull-right">
                    <a href="javascript:;" class="btn btn-primary btn-disable" id="export_btn">
                        <span>导出</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="grid-content clearfix">
            <div class="content-left">
                <div class="title">分组</div>
                <ul class="left-menu" id="type_menu">
                    <li class="on" data-type="all">全部用户<span>(0)</span></li>
                    <li data-type="fan">粉丝<span>(0)</span></li>
                    <li data-type="member">会员<span>(0)</span></li> 
                </ul>
            </div>
            <div class="content-right" id="fan_grid">
                <table class="table">
                    <thead>
                    <tr class="all on">
                        <th style="width:300px" class="fan-info">粉丝信息</th>
                        <th class="member-info">会员信息</th>
                        <th class="user-type">用户类型</th>
                        <th class="created-on" sort="created_on,desc">首次关注时间</th>
                        <th class="first_login_time" sort="first_login_time,desc">注册时间</th> 
                    </tr>
                    <tr class="fan">
                        <th>粉丝信息</th>
                        <th class="created_on" sort="created_on,desc">首次关注时间</th>
                    </tr>
                    <tr class="member">
                        <th style="width:300px">粉丝信息</th>
                        <th>会员信息</th>
                        <th sort="created_on,desc">首次关注时间</th>
                        <th class="first_login_time" sort="first_login_time,desc">注册时间</th>
                    </tr> 
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/template" id="grid_template">
    <td>
        <img class="avatar" src="<%-headimg_url?headimg_url:'/modules/images/global/default-headimg.png'%>" data-id="<%-_id%>"/>
        <span class="block em12"><%-nick_name||''%></span>
        <% if(type=='fan' && (sex=='男' || sex=='女')){ %>
        <span class="block em4"><%-sex%></span>
        <% } %>
    </td>
    <% if(type!='fan'){ %>
    <td>
        <% if(name||id_code){ %>
        <div><span class="inline-block em4 member-name"><%-name||''%></span><span class="inline-block"><%-id_code?(name?'，':'')+id_code:''%></span></div>
        <% } %>
        <% if(mobile||level){ %>
        <div><span data-id="<%- _id %>" class="mobile-tips"><%-mobile||''%></span><%-level?(mobile?'，':'')+level:''%></div>
        <% } %>
    </td>
    <% } %>
    <% if(type=='all'){ %>
    <td><%-user_type%></td>
    <% } %>
    <td><%-created_on||''%></td>
    <% if(type!='fan'){ %>
    <td><%-first_login_time||''%></td>
    <% } %> 
</script>
<script type="text/template" id="detail_template">
    <div class="detail">
        <div class="detail-title">详细资料</div>
        <div><span class="pt-label"><%-nick_name||''%></span><span
                class="sex-icon<%-sex=='男'?'':(sex=='女'?' woman':' normal')%>"></span></div>
        <% if(birthday){ %>
        <div><span class="pt-label">年龄</span><%-birthday%></div>
        <% } %> 
    </div>
</script>  
<script type="text/template" id="member_name_template">
    <div class="detail">
        <%-name%>
    </div>
</script>
<?php $this->beginBlock('js') ?>
<script type="text/javascript">
    __REQUIRE('/modules/js/wechat/fan/index.js?v=cf3179d7ff');
</script>
<?php $this->endBlock() ?>

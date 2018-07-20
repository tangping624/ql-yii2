<?php
$this->title = Yii::$app->params['system_name'];
?>

<?php $this->beginBlock('css') ?>
<link rel="stylesheet" type="text/css" href="/modules/css/wechat/groupsend/groupsend.min.css?v=c0e3fbda42" />
<?php $this->endBlock() ?>

<div class="manage-content">
    <div class="padding">
        <h4 class="manage-title">群发功能</h4>
          <input type="hidden" id="account_id" value="<?=$accountId?>"/> 
    </div>
    
    <ul class="tab-nav">
        <li>
            <a href="<?= $this->context->createUrl('/wechat/mass-message/index')?>">新建群发消息</a>
        </li>
        <li class="on">
            <span>已发送</span>
        </li>
    </ul>
    
    <div class="msg_list" id="msg_list">
        <table class="table">
            <thead style="display: none;"><tr><th></th><th></th><th></th><th></th></tr></thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script type="text/template" id="gridrow_template">
    <td>
        <div class="clearfix msg-item">
            <img src ="<%-mpnews_cover_url%>" class="pull-left"/>
            <div class="pull-overflow">
                <p class="f14 title"><%-mpnews_title%></p>
                <p class="color-gray f12"><%-mpnews_summary%></p> 
            </div>
        </div>
    </td>
    <td class="color-gray" width="200">
        <% if(status=='发送成功' || status=='发送失败' || status=='已撤销'){ %><span class="sended-msg" data-id="<%- _id %>"><%-status%><span class="icon-arrow"></span></span><% }else{ %><%-status%><% } %>
    </td>
    <td class="color-gray" width="170"><%-send_time||''%></td>
    <td width="100"><% if(status=='发送成功'){ %><a href="javascript:;" class="cancel-send" data-id="<%- id %>">撤销</a><% } %></td>
</script>

<?php $this->beginBlock('js') ?>
<script type="text/javascript">
    __REQUIRE('/modules/js/wechat/mass-message/list.js?v=5a61f059ea');
</script>
<?php $this->endBlock() ?>
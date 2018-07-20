<?php
    $this->title = Yii::$app->params['system_name'];
?>

<?php $this->beginBlock('css') ?>
<link rel="stylesheet" type="text/css" href="/modules/css/wechat/autoreply/autoreply.min.css?v=61e3abfce7" />
<?php $this->endBlock() ?>

<div class="manage-content">
    <div class="padding mb30 border-bottom">
        <h4 class="manage-title">自动回复</h4>
          <input type="hidden" id="account_id" value="<?=$data?>"/> 
    </div>

    <div class="padding">
        <ul class="reply-tabs clearfix" id="reply_type">
            <li><a href="<?= $this->context->createUrl('/wechat/auto-reply/index?type=welcome&public_id='.$data)?>">被添加自动回复</a></li>
            <li><a href="<?= $this->context->createUrl('/wechat/auto-reply/index?type=autoreply&public_id='.$data)?>">消息自动回复</a></li>
            <li class="on"><a href="<?= $this->context->createUrl('/wechat/auto-reply/kf-keyword&public_id='.$data)?>">自动转客服关键字</a></li>
            <li class="no-extra"><a href="<?= $this->context->createUrl('/wechat/auto-reply/keyword&public_id='.$data)?>">关键词自动回复</a></li>
        </ul>
    </div>
    
    <div class="keyword-wrap padding" id="keyword_wrap">
        <div id="js_msgSender"></div>
<!--        <div class="copy-link">
            <span>链接：</span>
            <a title="点击复制" class="link copy-button"><?=$this->context->createUrl('/wechat/dkf/member-info')?></a>
        </div>-->
<div style="padding-top: 10px;">
            <button type="button" class="btn btn-primary" id="save_btn">保存</button>
        </div>
    </div>
</div>

<?php $this->beginBlock('js') ?>
<script type="text/javascript">
    __REQUIRE('/modules/js/wechat/auto-reply/kf-keyword.js?v=aa53283edd');
</script>
<?php $this->endBlock() ?>
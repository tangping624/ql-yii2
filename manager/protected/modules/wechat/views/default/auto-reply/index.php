<?php
    $this->title = Yii::$app->params['system_name'];
?>

<?php $this->beginBlock('css') ?>
<link rel="stylesheet" type="text/css" href="/frontend/js/widgets/weixinEdition/css/msg_sender.css?v=a0ad0a19f5" />
<link rel="stylesheet" type="text/css" href="/modules/css/wechat/autoreply/autoreply.min.css?v=61e3abfce7" />
<?php $this->endBlock() ?>

<div class="manage-content">
    <div class="padding mb30 border-bottom">
        <h4 class="manage-title">自动回复</h4>
         <input type="hidden" id="account_id" value="<?=$data?>"/> 
    </div>

    <div class="padding">
        <ul class="reply-tabs clearfix" id="reply_type" data-type="welcome">
            <li><a href="javascript:;">被添加自动回复</a></li>
            <li><a href="javascript:;">消息自动回复</a></li>
            <li><a href="<?= $this->context->createUrl('/wechat/auto-reply/kf-keyword?public_id='.\Yii::$app->request->get("public_id"))?>">自动转客服关键字</a></li>
            <li class="no-extra"><a href="<?= $this->context->createUrl('/wechat/auto-reply/keyword?public_id='.\Yii::$app->request->get("public_id"))?>">关键词自动回复</a></li>
        </ul>

        <div id="js_msgSender"></div>

        <div style="margin-top:25px;">
            <button type="button" class="btn btn-primary" id="save_btn">保存</button>&nbsp;&nbsp;
            <button type="button" class="btn btn-secondary btn-disable" id="clear_btn" disabled="disabled">清除内容</button>
        </div>

    </div>
</div>

<?php $this->beginBlock('js') ?>
<script type="text/javascript" src="/modules/build/script/script-bca49585b2.js?v=d3a6e81e66" data-build></script>
<script type="text/javascript">
    var __SCRIPT = [
        '/frontend/3rd/plupload/plupload.full.min.js'
    ];
    __REQUIRE('/modules/js/wechat/auto-reply/index.js?v=5e52f511d7');
</script>
<?php $this->endBlock() ?>
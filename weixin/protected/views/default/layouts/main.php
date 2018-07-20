<?php
use yii\helpers\Html;

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <meta name="msapplication-tap-highlight" content="no" />
    <meta name="prefix" content="<?= Yii::$app->response->getHeaders()->get('prefix')  ?>">
    <title><?= Html::encode(\Yii::$app->context->getCustomTitle()?:$this->title) ?></title>
    <?php require './mobiend/inc/global.html';?>
    <?php
    if (isset($this->blocks['css'])) {?>
        <?= $this->blocks['css'] ?>
    <?php
    }?>
    <?= $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
    <div id="page_content">
        <?= $content ?>
    </div>
    <?php require './mobiend/inc/mix.html';?>
    <?php require './modules/inc/copyright.php';?>
    <?php
    if (isset($this->blocks['js'])) {?>
        <?= $this->blocks['js'] ?>
    <?php
    }?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
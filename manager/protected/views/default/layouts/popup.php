<?php
use yii\helpers\Html;

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="prefix" content="<?= Yii::$app->response->getHeaders()->get('prefix') ?>">
    <title><?= Html::encode($this->title) ?></title>
    <link href="/frontend/css/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php if (isset($this->blocks['css'])) { ?>
        <?= $this->blocks['css'] ?>
    <?php } ?>
    <?= $this->head() ?>
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/js/lib/compatible.js"></script>
    <![endif]-->
</head>
<body>
<?php $this->beginBody() ?>



<!-- 容器 [[-->
<!-- <div class="container-fixed">
    <div class="manage-container"> -->

<!-- 内容 [[-->
<?= $content ?>
<!-- 内容 ]]-->

<!-- </div>
</div> -->
<!-- 容器 ]]-->

<script type="text/javascript" src="/frontend/js/lib/global.js"></script>
<?php if (isset($this->blocks['js'])) { ?>
    <?= $this->blocks['js'] ?>
<?php } ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

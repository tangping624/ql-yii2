<?php
use yii\helpers\Html;
$this ->title='紧急详情';
?>

<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/base.css" />
<link rel="stylesheet" href="/modules/css/index/index.css" />
<style type="text/css">
  body{background:#fff;}
  h1{font-size:18px;}
</style>
<?php $this->endBlock() ?>

<div id="page_content">
  <!--header S-->
<!--  <header>-->
<!--     <div class="Head">-->
<!--        <a class="top-back" href="javascript:history.back(-1);"></a>-->
<!--        <div class="Hcon">-->
<!--          <h1>紧急详细</h1>-->
<!--       </div>-->
<!--     </div>-->
<!--  </header>-->
  <!--header E-->
  <div class="padt1" style="background:#fff;padding-top: 0">
      <div class="Urgent-detail">
          <h1><?=$details['title']?></h1>
          <h5>电话：<span class="lightgray"><?=$details['tel']?></span></h5>
          <h5>地址：<span class="lightgray"><?=$details['address']?></span></h5>
          <div class="hidden" style="display:none;"><?= $details['content']?></div>
          <p class="replace"></p>
          
      </div>   
      <div class="morebtn"><a href="tel:<?=$details['tel']?>"><em></em>拨打电话</a></div>
  </div>
  </div>
  <?php $this->beginBlock('js') ?>
   <script type="text/javascript">
     var str=$(".hidden").html().replace(/&nbsp;/ig, "");
     var strs=str.replace(/<p>/g,"");
     strs=strs.replace(/<\/p>/g,"");
     $(".replace").html(strs);
   </script>
  <?php $this->endBlock() ?>

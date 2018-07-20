<?php
use yii\helpers\Html;
$this ->title='我的';
?>
<?php $this->beginBlock('css') ?>
    <link rel="stylesheet" href="/modules/css/index/index.css" />
    <link rel="stylesheet" href="/modules/css/base.css" />
<?php $this->endBlock() ?>
<div class="page page-index page-fiexed clearfix" id="page">
  <div class="Membg">
<!--       <a class="top-back" href="javascript:history.back(-1);"></a>-->
       <a href="#" class="SignIn" style="display: none;"><em></em> 签到</a>
       <div class="MemInfo clearfix">
          <a href="/me/me/my-info">
             <div class="img"><img src="<?= $user['headimgUrl'] ?>" onerror="javascript:this.src='/images/myPhoto.png'"/></div>
             <div class="info"><h4><?= $user['name'] ?></h4><p>注册会员</p></div>
          </a>
       </div>
   </div>
   <div class="padb1">
       <div class="favorable" style="margin-top: 0;">
          <ul class="clearfix" style="display: none;">
             <li class="fore1"><a href="javascript:;">商家优惠<b>5</b></a><div class="dotL"></div><div class="dotR"></div></li>
             <li class="fore2"><a href="javascript:;">积分<b>1152</b></a><div class="dotL"></div><div class="dotR"></div></li>
          </ul>
       </div>
       <div class="Box" style="margin-top: 0;">
         <ul class="clearfix">
            <li><a href="/me/member/my-track"><em class="icon"><img src="/images/memicon1.png" /></em><b>我的足迹</b></a></li>
            <li><a href="/me/member/my-collection"><em class="icon"><img src="/images/memicon2.png" /></em><b>我的收藏</b></a></li>
            <li><a href="/me/member/praise"><em class="icon"><img src="/images/memicon3.png" /></em><b>我赞过的</b></a></li>
			<li><a href="/me/member/my-blog"><em class="icon"><img src="/images/memicon4.png" /></em><b>我的游说</b></a></li>
         </ul>
       </div>
   </div>
   <?php require './modules/inc/menu.php';?>
  </div>
<?php $this->beginBlock('js') ?>
<?php $this->endBlock() ?>
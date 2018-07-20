<?php
use yii\helpers\Html;
$this ->
title='新鲜事详细';
//var_dump($data);exit;
?>

<?php $this->
beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/base.css" />
<style type="text/css">
body{background:#fff;}
header{position: fixed;top: 0;left: 0;right: 0;z-index: 101;background: #f7f7f7; border-bottom: 1px solid #ccc;height: 2.45rem;}
.head{margin: 0.4rem 0.55rem;line-height: 1.65rem;position: relative;}
.head .top-back{position: absolute;left: 0;top: 0;background: url(/images/top-back.png) no-repeat left center;background-size: 0.56rem auto;width: 1.65rem;height: 1.65rem;display: block;}
.head .hcon{margin: 0 1.8rem;position: relative;}
.head .hcon h1{width: 100%;text-align: center;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;color: #f47920;}
.padt1{background: #fff;}
.padt1 .g_detail{padding:0;}
.padt1 .g_detail .img img{width: 100%;height: auto;}
.padt1 .g_detail .box{padding: 0 0.56rem;background: #fff;}
.padt1 .g_detail .box .myid{line-height: 2.22rem;margin-top: -0.4rem;position: relative;z-index: 2;}
.padt1 .g_detail .box .myid .my{float: left;width: 2.22rem;height: 2.22rem;border-radius: 50%;overflow: hidden;}
.padt1 .g_detail .box .myid .my img{width: 100%;height: auto;}
.padt1 .g_detail .box .myid p{font-size: 0.67rem;color: #666;padding-left: 2.6rem;}
.padt1 .g_detail .box .text{padding: 1rem 0;}
.padt1 .g_detail .box .text .tit{text-align: center;}
.padt1 .g_detail .box .text .tit h1{font-size: 0.89rem;}
.padt1 .g_detail .box .text .tit .tip{color: #999;font-size: 0.67rem;margin-top: 0.56rem;}
.padt1 .g_detail .box .text .tit .tip span{margin-right: 0.5rem;}
.padt1 .g_detail .box .text .con{margin-top: 1rem;text-indent: 1.5rem;font-size: 0.78rem;line-height: 1.1rem;color: #666;}
.padt1 .g_detail .box .text .con img{display: block}
#page_content{background-color: #fff;}
</style>
<?php $this->
endBlock() ?>
<header>
    <div class="head">
        <a class="top-back" href="javascript:history.back(-1);"></a>
        <div class="hcon">
            <h1 style="font-size:18px;">新鲜事详情</h1>
        </div>
        <a href="/home/home/search" style="width: 1rem;height: 1rem;background: url(/images/top-search.png) no-repeat center;position: absolute;right: 0;top: .4rem;background-size: 100% auto;"></a>
    </div>
</header>
<!--header E-->
<div class="padt1" style="padding-top:2.5rem;">
    <div class="g_detail">
        <div class="img">
            <img src="<?= $data['photo'] ?>"/>
        </div>
        <div class="box">
            
            <div class="text">
                <div class="tit">
                    <h1><?= $data['title'] ?></h1>
                    <div class="tip">
                        <span><?= explode(' ',$data['created_on'])[0]?></span>
                    </div>
                </div>
                <div class="con">
                    <?= $data['content'] ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->
beginBlock('js') ?>

<?php $this->
endBlock() ?>
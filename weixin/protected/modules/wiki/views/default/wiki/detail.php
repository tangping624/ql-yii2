<?php
/**
 * Created by PhpStorm.
 * User: tx-04
 * Date: 2017/3/29
 * Time: 12:39
 */
use yii\helpers\Html;
$this ->title='百科详情';
?>
<?php //var_dump($details);exit;?>
<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/index/index.css" />
<link rel="stylesheet" href="/modules/css/base.css" />

<?php //var_dump($details);exit;?>
<style>
    body{background:#fff;}
    .G_detail .text .con p{text-indent: 0}
    .con img{margin: 0.5rem 0}
</style>
<?php $this->endBlock() ?>
<!--header S-->
<header>
    <div class="Head">
        <a class="top-back" href="javascript:history.back(-1);"></a>
        <div class="Hcon">
            <h1 style="font-size: 1rem;">百科详情</h1>
        </div>
        <a href="/home/home/search" style="width: 1rem;height: 1rem;background: url(/images/top-search.png) no-repeat center;position: absolute;right: 0;top: .4rem;background-size: 100% auto;"></a>
    </div>
</header>
<!--header E-->
<div class="padt1" style="padding-top: 2.45rem">
    <div class="G_detail">
        <div class="img"><img src="<?= Html::encode($details['logo'])?>" /></div>
        <div class="box">
            <div class="text">
                <div class="tit">
                    <h1 style="line-height: 1.5rem;"><?= Html::encode($details['title'])?></h1>
                    <div class="tip"><span></span><span><?= explode(' ',$details['created_on'])[0]?></span></div>
                </div>
                <div class="con"><?= $details['content']?></div>
            </div>
        </div>
    </div>

</div>
<?php $this->beginBlock('js') ?>
<script type="text/javascript" src="/mobiend/js/lib/zepto/zepto.js"></script>
<script type="text/javascript" src="/modules/js/TouchSlide.1.1.js"></script>
<script type="text/javascript" src="/modules/js/idangerous.swiper.min.js"></script>

<script type="text/javascript">

    $('.con>p').each(function(i,v){
        var imgLen=$(v).find('img').length;
        if(imgLen){
            $(v).css('text-align','center')
        }else{
            $(v).css('text-indent','1.5rem')
        }
    })

</script>
<?php $this->endBlock() ?>

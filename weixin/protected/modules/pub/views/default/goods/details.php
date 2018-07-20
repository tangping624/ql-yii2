<?php
/**
 * Created by PhpStorm.
 * User: tx-04
 * Date: 2017/4/13
 * Time: 17:14
 */
use yii\helpers\Html;
$this ->title=$details['name'];
?>
<?php //var_dump($details);exit;?>
<?php $this->beginBlock('css') ?>
    <link rel="stylesheet" type="text/css" href="/modules/css/index/index.css"/>
    <link rel="stylesheet" href="/modules/css/base.css" />
    <link rel="stylesheet" href="/modules/css/details.css" />
<?php $this->endBlock() ?>
    <input type="hidden" id="cookie" value="<?= Html::encode($cookie)?>">
    <input type="hidden" id="id" value="<?= Html::encode($details['id'])?>">
    <input type="hidden" id="seller_id" value="<?= Html::encode($details['seller_id'])?>">
    <header>
        <div class="Head">
            <a class="top-back" href="javascript:history.back(-1);"></a>
            <div class="Hcon" style="margin: 0 5rem;">
               <h1 style="overflow:hidden;width:78%;"><?= $details['name']?></h1>
            </div>
<!--            <a class="icon dz --><?//= $details['prise']?'yi-dz':'wei-dz'?><!--" href="javascript:;" style="right: 2.6rem;"></a>-->
            <a class="icon sc <?= $details['collection']?'yi-sc':'wei-sc'?>" href="javascript:;" style="right:0"></a>
<!--            <a href="/home/home/search" style="width: 1rem;height: 1rem;background: url(/images/top-search.png) no-repeat center;position: absolute;right: 0;top: .4rem;background-size: 100% auto;"></a>-->
        </div>
    </header>
    <div class="padt1">
        <div class="Detbox">
            <div class="tit borb1"><s></s><b>基本信息</b></div>
            <div class="prolist1 smallImg con">
                <div><?= $details['summary']?></div>
            </div>
        </div>
        <div class="Detbox">
            <div class="tit borb1"><s></s><b>商品简介</b></div>
            <div class="prolist1 smallImg con">
                <p><?= $details['content']?></p>
            </div>
        </div>
    </div>
    <?php $this->beginBlock('js') ?>
    <script>
        seajs.use('/modules/js/goods/details',function(details){
            details.init();
        })
    </script>
<?php $this->endBlock() ?>
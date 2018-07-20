<?php
use yii\helpers\Html;
$this ->
title='商家详情';
?>
<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/base.css" />
<!-- <link rel="stylesheet" href="/modules/css/details.css" /> -->
<link rel="stylesheet" href="/modules/css/index/index.css" />
<style type="text/css">
   .top-share{
        background: url(/images/dianzan.png) no-repeat center;
        background-size:1.1111rem auto;
        
        }
        .top-share1{
        background: url(/images/dianzan-h.png) no-repeat center;
        background-size:1.1111rem auto;
        }
        .star1{
        height: .67rem;
        float: left;
        margin-right: .3rem;
        width:1rem;
        height:.67rem;
        background:url(/images/dianzan-h.png) no-repeat center;
        background-size: .6667rem auto;
        }
     .top-star{
     	background: url(/images/shoucang.png) no-repeat center;
        background-size:1.1111rem auto;
        
     }
     .top-star1{
     	background: url(/images/shoucang-h.png) no-repeat center;
        background-size:1.1111rem auto;
        
     }
   .prolist1 li .info{width:66%;}
   .loca{padding-left:18px;}
   
</style>
<?php $this->endBlock() ?>
<input type="hidden" id="cookie" value="<?= Html::encode($cookie)?>">
<input type="hidden" id="shopid" value="<?= Html::encode($details['id'])?>">
<header>
    <div class="Head">
        <a class="top-back" href="javascript:history.back(-1);"></a>
        <div class="Hcon">
            <h1>商家详细</h1>
        </div>
        <a class="icon1 <?= $details['prise']?'top-share1':'top-share'?>" href="javascript:;"></a>
        <a class="icon2 <?= $details['prise']?'top-star1':'top-star'?>" href="javascript:;"></a>
    </div>
</header>

<div class="padt1">
    <ul class="prolist1 nobg ">
        <li class="nobd clearfix">
            <div class="img fd-locate" >
                <a href="#">
                    <?php foreach (array_reverse($images) as $v){?>
                    <span style="position:absolute;">
                    
                            <img  src="<?= $v['original_url']?>"/>
                        
                    </span>
                    <?php }?>
                	<div class="imgnum">65张</div>
                </a>
            </div>
            <div class="info clearfix">
                <h3 ><?=Html::encode($details['NAME'])?></h3>
                <div class="com">
                    <span class="star1"></label></span>
                    <span>(1234)</span> 
                 </div> 
                <div class="about clearfix">
                    <a class="fd-right btn-intro" href="/house/house/product-index?id=<?=$details['id']?>">查看详细<s></s></a>
                </div>
            </div>
        </li>
    </ul>
    <div class="DetItem bg-white">
        <a href="#">
            <div class="site"><em><img src="/images/location.png" /></em><?php $latitudes=Html::encode($details['latitudes']);$longitudes=Html::encode($details['longitudes']);$address=Html::encode($details['address'])?>
                <a class="loca clearfix" id="map_link" href="<?=$address?'/pub/map/map?address='.$address.'&latitudes='.$latitudes.'&longitudes='.$longitudes:'javascript:;'?>">
                    <?= Html::encode($details['address']?$details['address']:'地址为空哦')?>
                </a>
            </div>
        </a>
    </div>
    <div class="DetItem bg-white">
        <a href="tel:<?=$details['linktel']?>">
            <div class="site"><em><img src="/images/phone.png" /></em><span class="black"><?=$details['linktel']?></a></span></div>
        </a>
    </div>
    <!-- <div class="Detbox">
       <div class="tit"><s></s><b>商家优惠</b></div>
       <div class="DetItem">
           <a href="Shopcoupon.html" class="clearfix">
              <div class="icon"><img src="images/gift.png" /></div>
              <div class="state">使用手机APP领取商店优惠券，享立减十元优惠</div>
           </a>
        </div>
   </div> -->

    <!-- <div class="mart1 bg-white">
        <div class="DetItem">
           <a href="#" class="clearfix">
              <div class="dt fd-left">
                 <b class="black">评论</b><span class="fs24 gray">（1136条评论）</span>
              </div>
              <div class="dd fd-right"><span class="star"><label style="width:100%"></label></span></div>
           </a>
        </div>
   </div> -->

    <div class="Detbox">
        <div class="tit borb1"><s></s><b>商家介绍</b><span class="fs24 gray"></span></div>
        <div class="prolist1 smallImg" style="padding-bottom:10px ;border-bottom:10px solid #e6e6e6;">
            
            	<?= $details['content']?>
            
        </div>
        <div class="tit borb1" style="padding-top:10px;margin-bottom:10px;"><s></s><b>商家提醒</b><span class="fs24 gray"></span></div>
        <div class="prolist1 smallImg" style="padding-bottom: 20px;">
            
            	<?=$details['remind']?>
           
            </div>
        </div>
</div>
<?php $this->beginBlock('js') ?>

<script>
    seajs.use('/modules/js/house/details',function(details){
        details.init();
    })
</script>
<?php $this->endBlock() ?>
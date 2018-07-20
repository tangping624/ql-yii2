<?php
use yii\helpers\Html;
$this ->title='百科';
?>
<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/index/index.css" />
<link rel="stylesheet" href="/modules/css/base.css" />

<?php //var_dump($advert);exit;?>
<style>
    /*.idxAd21{margin-top: 0}*/
    .nav1 ul li:not(:first-child){margin-left: 0.3rem}
    .flex{display: -webkit-box;padding:0.3rem 0}
    .bknav{position: relative;background: #fff;}
    .bknav .nav1{padding:0 0 0 0.36rem;overflow-x: scroll;z-index: 10;border-bottom: none;}
    .scrollBar{  height:10px;  background: #fff;  position: absolute;  bottom: 0; /*border-bottom: 1px solid #ededed;z-index: 1000; */}
    /*#loadList{position: relative}*/
    body{background: #fff}
    /*.scroll-wrap{top: 13rem ;}*/
    #LoadMoreWrap{margin-top: 20px}
    #scrollWrap{z-index: 9999}
    .idxAd21{margin-top: 2.45rem;}
    .idxAd21 li img{max-height: 7rem;height: 7rem;}
    .prolist1{padding: 0;}
    .prolist1 li{background: url(/images/arrowR.png) no-repeat right center;background-size: .5rem auto;margin: 0.2rem 0;padding: 0}
    .prolist1 li .img span img{height: 6rem;max-width:100%;}
    .prolist1 li .info{min-height: 6rem;position: absolute;width: 100%;float: none;text-align: center;margin: 0;top: 55%;color: #fff;}
    .prolist1 li .info p{color: #fff;}
    .prolist1 li .img{width:100%;height: 4.5rem;float: none;}
    .searchicon{background: #fff;top: 0.5rem;z-index: 100}
    .imgico:before{
        content: '';position: absolute;width: 100%;height: 100%;background-color: rgba(0, 0, 0, 0.3);
        /*background:#000; !* 一些不支持背景渐变的浏览器 *!*/
        /*background: linear-gradient(to top, rgba(0, 0, 0, 0.9),rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.3));*/
        background: -webkit-linear-gradient(bottom,#000 0%,transparent 100%);
        opacity: 0.7;
        top: 60%;
        height: 40%;
    }
    .bklist .info .time{margin-top: 0}
    .prolist1 li .info p{margin-top: 0}
    /*.flex{padding: 0.25rem 0;}*/
    #menu .align-c{padding:50px 0;margin:0 -0.56rem;background:#f0f0f0;}
    .ho_loading{width: 100%;height: 100px;text-align: center;padding-top: 65px;background: #f0f0f0;}
    .searchicon a{background-size: .85rem auto;}
</style>
<?php $this->endBlock() ?>
    <header>
        <div class="Head">
            <a class="top-back" href="javascript:history.back(-1);"></a>
            <div class="Hcon">
                  <h1 style="font-size:1rem;">百科</h1>
            </div>
            <!-- <a href="/home/home/search" style="width: 1rem;height: 1rem;background: url(/images/top-search.png) no-repeat center;position: absolute;right: 0;top: .4rem;background-size: 100% auto;"></a> -->
        </div>
    </header>
    <div style="margin-top:2.45rem;">
<?php if(!empty($advert)){?>
    <div class="idxAd21 clearfix"  id="picScroll2" >
        <div class="hd" style="display:none;"><ul></ul></div>
        <?php $count=count($advert)?>
        <div class="bd">
            <?php foreach ($advert as $i=>$v){?>
                    <ul class="ad" style="display: none">
                        <li class="listAd" style="width: 100%;"><a href="<?= Html::encode($v['link_url'])?>"><img src="<?= Html::encode($v['original_url'])?>" /></a></li>
                    </ul>
            <?php }?>
        </div>
    </div>
<?php }?>
<!--header E-->
<div class="padt21" >
    <?php if(count($type)){?>
        <div class="bknav" id="slider-nav" style="display: none;">
            <div class="searchicon"><a href="search"></a></div>
            <div class="nav1" style="padding-right: 0;margin-right: 2rem;">
<!--                <s></s>-->
                <ul class="flex">
                         <li data-id="" class="cur"><a href="javascript:;" style="padding: 0 0.6rem">全部</a></li>
                            <?php foreach ($type as $i=>$v){?>
                                    <li data-id="<?= Html::encode($v['id'])?>"><a href="javascript:;" style="padding: 0 0.5rem"><?= Html::encode($v['name'])?></a></li>
                            <?php }?>

                </ul>

            </div>
            <div class="scrollBar"></div>
        </div>
    <?php }?>
    <div id="loadList" class="activity-wrap bklist">
        <div class="scroll-wrap" id="scrollWrap" style="<?php if(empty($advert)){echo 'top: 4.4rem;';?><?php }else{ echo 'top:11.4rem';}?>">
            <div>
<!--                <div id="loading" class="align-c" style="padding-top: 0"></div>-->
                <div class="ho_loading"><img src="/images/loading.gif" alt=""></div>
                <ul class="menu clearfix prolist1" id="menu" style="padding: 0 0.36rem;">
                    
                </ul>
                <div id="LoadMoreWrap"><div class="align-c color-gray2 f-12" id="LoadMore" style="display:none;">上滑加载更多</div></div>
            </div>
        </div>
    </div>
</div>
</div>
<script type="text/template" id="baikeList">
    <% for(var i=0;i<data.length;i++){%>
        <li class="imgico">
            <a href="javascript:;" data-id="<%=data[i].id%>" class="clearfix">
                <div class="img"><span><img src="<%=data[i].logo%>" width="100%"/></span></div>
                <div class="info">
                    <h5 class="ellipsis" style="background: none;margin-top: 0.3rem"><%=data[i].title%></h5>
                    <p class="time" style=""><%=data[i].created_on.split(' ')[0]%></p>
                </div>
            </a>
        </li>
    <% }%>
</script>
<script type="text/template" id="empty_tmpl">
    <div class="align-c" style="padding:50px 0;">
        <img src="/images/no.png" width="80"/>
<!--        <p class="f-12">还没有相关信息</p>-->
    </div>
</script>
<?php $this->beginBlock('js') ?>
<script type="text/javascript" src="/mobiend/js/lib/zepto/zepto.js"></script>
<script type="text/javascript" src="/modules/js/TouchSlide.1.1.js"></script>
<!--<script type="text/javascript" src="/modules/js/idangerous.swiper.min.js"></script>-->

<script type="text/javascript">
    seajs.use('/modules/js/baike/index',function(index){
        index.init();
    })
</script>
<?php $this->endBlock() ?>

<?php
use yii\helpers\Html;
$this ->title='产品列表';
?>
<?php //var_dump($appcode);exit;?>
<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/base.css" />

<link rel="stylesheet" href="/modules/css/index/index.css" />
<link rel="stylesheet" href="/modules/css/main.css" />

<link rel='stylesheet' type="text/css" href='/modules/css/swiper.min.css'>
<style>
    .idxAd1 {margin-top: 0;}
    .mart1{margin-top:0}
    .prolist1 li{padding:.56rem;}
    .prolist1{padding: 0}
    .prolist1 li .info{min-height:3rem;}
    /*.swiper-wrapper{padding: 0 0.56rem}*/
    .prolist1 li .img{width: 100%;float: none}
    .prolist1 li .img span img{width: 100%;  height: 6.5rem;max-height: 6.5rem;max-width: 100%}
    .prolist1 li {width:49%;}

    .favor-header-bar li{width: 5rem}
    .scrollBar{  height:10px; position: absolute;  bottom: 3px; z-index: 1000;}
    .prolist1 li{padding: .56rem 0;}
    .prolist1 li:nth-child(2n-1){padding: 0.56rem 0.5%;}
    .prolist1 li:nth-child(2n){padding: 0.56rem 0.5%;}
    .prolist1 li:nth-child(1),.prolist1 li:nth-child(2){padding: 0 0.5%;}
    .prolist1 li .info{text-align: center;padding: 0.5rem 0;min-height: 0;width: 100%;margin: 0}
    .Hcon h1{font-size: 1rem}
    .favor-header-bar li.default a{color: #F47920;border-bottom: 0.15rem solid #F47920;}
    .scroll-wrap{top:2.45rem;}
    .product_wrapper{margin-top:2.45rem;}
    #ho_loading img{width: 20px;margin-top: 20px;}
</style>
<?php $this->endBlock() ?>

<!--header S-->
 <header>
    <div class="Head">
        <a class="top-back" href="javascript:history.back(-1);"></a>
        <div class="Hcon">
            <h1>产品列表</h1>
        </div>
        <a href="/home/home/search" style="width: 1rem;height: 1rem;background: url(/images/top-search.png) no-repeat center;position: absolute;right: 0;top: .4rem;background-size: 100% auto;"></a>
    </div>
</header>
    <div class="product_wrapper">
        <?php if(!empty($advert)){?>
            <div class="idxAd1"  id="picScroll2" style="display: none">
                <div class="hd" style="display:none;">
                    <ul></ul>
                </div>
                <div class="bd">
                    <?php foreach ($advert as $v){?>
                        <ul style="display: table-cell">
                            <li>
                                <a href="<?= Html::encode($v['link_url']?$v['link_url']:'javascript:;')?>">
                                    <img src="<?= Html::encode($v['original_url'])?>" />
                                </a>
                            </li>
                        </ul>
                    <?php }?>
                </div>
            </div>
        <?php }?>
        <?php if($type){?>
            <div class="idxRecomme" style="position: relative;margin-top: 0;">
                <div class="favor-header-bar" id="sidebar"  style="overflow-x: auto;z-index: 10;">
                    <ul class="tabs clearfix" style="display: none">
                        <?php foreach ($type as $i=>$v){?>
                            <?php if($i==0){?>
                                <li class="default" data-id="<?= Html::encode($v['id'])?>"><a href="javascript:;" hidefocus="true"><?= Html::encode($v['name'])?></a></li>
                            <?php }else{?>
                                <li data-id="<?= Html::encode($v['id'])?>">
                                    <a href="javascript:"hidefocus="true"><?= Html::encode($v['name'])?>
                                    </a>
                                </li>
                            <?php }?>
                        <?php }?>
                    </ul>
<!--                    <div class="scrollBar" style="bottom: 4px;"></div>-->
                </div>
            </div>
        <?php }?>
        <div class="activity-wrap">
            <div class="scroll-wrap" id="scrollWrap" style="<?php if(!empty($advert)){echo 'top: 12.05rem;';?><?php }else{ echo 'top:5.05rem';}?>">
                <div id="ho_loading" class="align-c"><img src="/images/loading.gif" alt=""></div>
                <ul class="prolist1 swiper-slide " id="menuList">

                </ul>
                <div id="LoadMoreWrap">
                    <div class="align-c color-gray2 f-12" id="LoadMore" style="display:none;">上滑加载更多</div>
                </div>
            </div>
        </div>
    </div>
<!--    <div class="loader"><em class="clip-rotate"></em>加载中...</div>-->
<script type="text/template" id="menu_tmpl">
    <% for (var i=0; i<data.length; i++) { %>
        <li class="fl" data-id="<%= data[i].id%>">
            <a href="javascript:;" class="clearfix">
                <div class="img fn" class=""><span><img src="<%= data[i].logo%>" /></span></div>
                <div class="info" style="float: none">
                    <h3><%= data[i].name%></h3>
                </div>
            </a>
        </li>
    <% } %>
    <% if(data.length%2!=0){%>
    <li class="fl" data-id="">
        <a href="javascript:;" class="clearfix">
            <div class="img fn" class=""><img src="" alt="" style="height: 6.5rem;visibility: hidden"><span></span></div>
            <div class="info" style="float: none">
                <h3 style="visibility: hidden">''</h3>
            </div>
        </a>
    </li>
    <% }%>
</script>
<script type="text/template" id="empty_tmpl">
    <div class="align-c emptyTips" style="padding:50px 0;">
        <img src="/images/no.png" width="80"/>
<!--        <p class="f-12">还没有相关信息</p>-->
    </div>
</script>
<?php $this->beginBlock('js') ?>
<script type="text/javascript" src="/modules/js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="/modules/js/idangerous.swiper.min.js"></script>
<script type="text/javascript" src="/modules/js/TouchSlide.1.1.js"></script>
<script src="/modules/js/portamento.js"></script>
<!--<script>$('#sidebar').portamento({disableWorkaround: true});</script>-->
<script type="text/javascript">
    seajs.use('/modules/js/goods/index',function(index){
        index.init();
    })
</script>
<?php $this->endBlock() ?>

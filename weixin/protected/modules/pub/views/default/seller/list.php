<?php
/**
 * Created by PhpStorm.
 * User: tx-04
 * Date: 2017/4/6
 * Time: 16:52
 */
use yii\helpers\Html;
$this ->title='';
?>
<?php //var_dump($type);exit;?>
<?php $this->beginBlock('css') ?>
    <link rel="stylesheet" href="/modules/css/base.css" />
    <link rel="stylesheet" href="/modules/css/main.css" />
    <!--<link rel="stylesheet" href="/modules/css/index/index.css" />-->
    <style type="text/css">
       #loading img{width: 20px;margin-top: 20px;}
       #LoadMore img{width:20px;}
    </style>
<?php $this->
endBlock() ?>
    <div class="page-content activity-wrap">
        <header class="header_flxed">
        <div class="Head">
            <a class="top-back" href="javascript:history.back(-1);"></a>
            <div class="Hcon">
                  <h1 style="font-size:18px;"></h1>
            </div>
            <a href="/home/home/search" style="width: 1rem;height: 1rem;background: url(/images/top-search.png) no-repeat center;position: absolute;right: 0;top: .4rem;background-size: 100% auto;"></a>
        </div>
    </header>
        <div class="scroll-wrap" id="scrollWrap" style="<?php if(empty($advert)){?>top:2.45rem <?php }else{?>top:0 <?php }?>">
            <div>
                <?php if(!empty($advert)){?>
                    <div class="idxAd1 clearfix"  id="picScroll2" style="display:none;">
                        <div class="hd" style="display:none;">
                            <ul></ul>
                        </div>
                        <div class="bd">
                            <?php foreach ($advert as $v){?>
                                <ul>
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
                <!--排序 S-->
                <div class="padt21 clearfix">
                    <div class="rankbox" style="display: none;z-index: 102;">
                        <ul class="flex">
                            <li class="flex_div">
                                <a href="#">
                                    <span class="ellipsis1">区域</span> <i></i>
                                </a>
                            </li>
                            <li class="flex_div type">
                                <a href="#">
                                    <span class="ellipsis1">全部分类</span> <i></i>
                                </a>
                            </li>
                            <li class="flex_div">
                                <a href="#">
                                    <span>智能排序</span>
                                    <i></i>
                                </a>
                            </li>
                        </ul>
                        <div class="Prank" style="display:none;">
                            <div class="mask"></div>
                            <div class="layer">
                                <div class="conbox Pnearby" style="display:none;">
                                    <ul class="popL poprank" id="scroller" style="width: 100%">
<!--                                        <li data-id="" class="" id="allCity">-->
<!--                                            <a class="ellipsis" style="width: 100%">全部城市</a>-->
<!--                                        </li>-->
                                        <?php for ($i=0;$i<count($city);$i++){?>
                                            <li data-id="<?= Html::encode($city[$i]['id'])?>" class="">
                                                <a href="#list<?=$i?>" class="ellipsis" style="width: 100%"><?= Html::encode($city[$i]['treeText'])?></a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                                <div class="conbox">
                                    <ul class="poprank typeList">
                                        <li data-id=""><a href="#">全部分类</a></li>
                                        <?php foreach ($type as $i=>$v){?>
                                            <li data-id="<?= Html::encode($v['id'])?>"><a href="#"><?= Html::encode($v['name'])?></a></li>
                                        <?php }?>
                                    </ul>
                                </div>

                                <div class="conbox" style="display:none">
                                    <ul class="poprank">
                                        <li><a href="#">智能排序</a></li>
                                        <li><a href="#">离我最近</a></li>
                                        <li class="assess"><a href="#">好评最多</a></li>
                                        <li class="collection"><a href="#">收藏最高</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--排序 E-->
                <!--    <div class="scroll-wrap" id="scrollWrap" style="margin-top:500px; --><?//=isset($params['from'])&&$params['from']=='index'?'top:45px;bottom:55px;padding-bottom: 10px;':'top:0;bottom:30px;padding-bottom: 10px;'?>
                <!--        ">-->
                <div  style="margin-top: 2rem;">

                    <div>
                        <div id="loading" class="align-c"><img src="/images/loading.gif" alt=""></div>
                        <ul class="prolist1 menu clearfix" id="menu">

                        </ul>
                        <div id="LoadMoreWrap">
                            <div class="align-c color-gray2 f-12" id="LoadMore" style="display:none;">点击加载更多</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/template" id="menu_tmpl">
        <% for (var i=0; i<data.length; i++) { %>
            <li class="clearfix">
                <a href="javascript:;" data-id="<%=data[i].id%>">
                    <div class="img"><span><img src="<%=data[i].logo%>" width="100%"/></span></div>
                    <div class="info">
                        <h3 class="ellipsis"><%=data[i].NAME%></h3>
                        <p class="agent ellipsis"><%=data[i].summary%></p>
                    </div>
                </a>
            </li>
        <% } %>
    </script>
    <script type="text/template" id="empty_tmpl">
        <div class="align-c" style="padding:50px 0;">
            <img src="/images/no.png" width="80"/>
<!--            <p class="f-12">还没有相关信息</p>-->
        </div>
    </script>
<?php $this->
beginBlock('js') ?>
    <script type="text/javascript" src="/mobiend/js/lib/TouchSlide.1.1.js"></script>
    <script type="text/javascript">
        seajs.use('/modules/js/public/type-index',function(index){
            index.init();
        })
    </script>
    <!--    <script src="js/portamento.js"></script>-->
<?php $this->
endBlock() ?>
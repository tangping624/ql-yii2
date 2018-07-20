<?php
/**
 * Created by PhpStorm.
 * User: tx-04
 * Date: 2017/4/6
 * Time: 16:52
 */
use yii\helpers\Html;
$this ->title='外汇';
?>
<?php //var_dump($type);exit;?>
<?php $this->beginBlock('css') ?>
    <link rel="stylesheet" href="/modules/css/base.css" />
    <link rel="stylesheet" href="/modules/css/main.css" />
    <!--    <link rel="stylesheet" href="/modules/css/index/index.css" />-->
    <style>
        .prolist1 li .info p{
            margin-top: 0.5rem;
            line-height: 1.2;
        }

        .prolist1 li .info i {
            font-style: normal;
            padding: 0 .1rem;
            border-radius: .1rem;
            color: #fff;
            font-size: .56rem;
            margin-right: .2rem;
            display: inline-block;
            margin-top: 0.2rem;
        }
        .prolist1 li .info .about {
            margin-top: .65rem;
            color: #999;
            font-size: .56rem;
            line-height: 1rem;
        }
        .scroll-wrap{top:2.45rem;}
        .idxAd1{margin-top:0px;}
    </style>
<?php $this->
endBlock() ?>
<?php foreach ($type as $i=>$v){?>
    <input type='hidden' data-id="<?= Html::encode($v['id'])?>" value="<?= Html::encode($v['name'])?>" class="type_id"/>
<?php }?>
    <div class="page-content activity-wrap">
        <header>
            <div class="Head">
                <a class="top-back" href="javascript:history.back(-1);"></a>
                <div class="Hcon">
                      <h1 style="font-size:18px;">外汇</h1>
                </div>
            </div>
        </header>
        <div class="scroll-wrap" id="scrollWrap">
            <div>
                <?php if(!empty($advert)){?>
                    <div class="idxAd1 clearfix"  id="picScroll2" style="display: none">
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
                    <div class="rankbox" style="display: none">
                        <ul class="flex">
                            <li class="flex_div">
                                <a href="#">
                                    <span>区域</span> <i></i>
                                </a>
                            </li>
                            <li class="flex_div type">
                                <a href="#">
                                    <span>全部分类</span> <i></i>
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
                                    <ul class="popL" id="scroller" >
                                        <?php for ($i=0;$i<count($city);$i++){?>
                                            <li data-id="<?= Html::encode($city[$i]['id'])?>" class="">
                                                <a href="#list<?=$i?>" class="ellipsis" style="width: 100%"><?= Html::encode($city[$i]['treeText'])?></a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                    <?php for ($val=0;$val<count($city);$val++){?>
                                        <ul class="popR poprank pop1" style="display:none;" id="list<?=$val?>">
                                            <?php if(count($city[$val]['childNode'])>0) { ?>
                                                <?php foreach ($city[$val]['childNode'] as $j){?>
                                                    <li data-id="<?= Html::encode($j['id'])?>" class="child">
                                                        <a href="#" class="ellipsis" style="width: 100%"><?= Html::encode($j['treeText'])?></a>
                                                    </li>
                                                <?php }?>
                                            <?php }?>
                                        </ul>
                                    <?php }?>
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
                        <div id="loading" class="align-c" style="display: none"></div>
                        <ul class="prolist1 menu clearfix" id="menu">

                        </ul>
                        <div id="LoadMoreWrap">
                            <div class="align-c color-gray2 f-12" id="LoadMore" style="display:none;">上滑加载更多</div>
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
                    <h3 class="ellipsis"><%=data[i].name%></h3>
                    <div class="clearfix about" style="width: 90%;margin-top: 1.5rem">
                        <p class=" ellipsis fl" style="width: 64%;margin-top: 0"><%=data[i].summary%></p>
                        <span class="fd-right" style=""><%=Number(data[i].dis).toFixed(1)%>km</span>
                    </div>
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
        seajs.use('/modules/js/repast/index',function(index){
            index.init();
        })
    </script>
    <!--    <script src="js/portamento.js"></script>-->
<?php $this->
endBlock() ?>
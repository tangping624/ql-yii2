<?php
use yii\helpers\Html;
$this ->title='';
?>
<?php $this->beginBlock('css') ?>
    <link rel="stylesheet" href="/modules/css/index/index.css" />
    <link rel="stylesheet" href="/modules/css/base.css" />
    <!-- <link rel="stylesheet" href="/modules/css/based.css" /> -->
    <link rel="stylesheet" href="/modules/css/demo.css" />
    <!-- <link rel="stylesheet" href="/modules/css/default.css" /> -->
    <link rel="stylesheet" href="/modules/css/osSlider.css" />
    <style type="text/css">
    body{background:#f0f0f0!important;}
    .info p{text-overflow: -o-ellipsis-lastline;overflow: hidden;text-overflow: ellipsis;display: -webkit-box;-webkit-line-clamp: 2;-webkit-box-orient: vertical;}
    .prolist11 li .info{border-bottom: 1px solid #e6e6e6;}
    /*.actybox{background:-webkit-linear-gradient(rgb(246,226,196), rgb(230,187,124));padding:3% 0;margin-top:0px;}*/
    .actybox{background: none;padding: 0;}
    .actybox li{background: #fff;}
    /*.myAdvert{margin: 3% 1.75% 0 1.75%;height: 9.2rem}*/
    .myAdvert{margin: 3% 0 0 0;height: 9.2rem;}
    .iconBox{display: block;background: #fff;text-align: center;height: 100%;}
    .leftAd {margin-right: 3px;height: 100%;}
    .leftAd .img{height: 100%;}
    .leftAd .img img{width: 100%;height: 100%;}
    .rightAd .img img{ height: 100%;width: 100%;}
    .rightAd .img{height: 100%;}
    .rightAd .iconBox{height:4.5rem}
    .actybox li .img{height: 100%;padding: 0;}
    .actybox li img{width: 100%;height: 100%;}
    .add_top{width:30px;height:30px;position: fixed;bottom:65px;right:15px;background-color:#fff;border:1px solid #e6e6e6;z-index: 100;border-radius:50%;display: none;box-shadow: 0 0 5px #111;}
    .add_top img{width:15px;height:15px;position: relative;top:8px;left:8px;}
    #more{width:100%;height:40px;line-height:40px;text-align: center;display: none;}
    .kong{padding:50px 0;}
    .actybox li.fore2{margin: 0 2%;}
    #loading img{width: 20px;margin-top: 20px;padding-bottom:1000px;}
    #LoadMore{margin: 20px 0;}
    #LoadMore img{width:20px;}
    .ho_loading{width: 100%;height: 100px;text-align: center;padding-top: 45px;background: #f0f0f0;}
    /*.actybox li{margin-left: 0;width: 32%;}*/
    /*.slider-main img{width:100%;height:6.3rem!important;}*/
    </style>
<?php $this->endBlock() ?>
<div class="add_top"><img src="/images/go_top.png"></div>
<div class="page page-index page-fiexed clearfix" id="page">
    <header class="bg-org">
        <div class="Head">
            <div class="idxHead clearfix">
                <!-- <a href="javascript:;" class="HLocation fd-left"><span class="addr_city"></span><em></em></a> -->
                <div class="Hsearbox" style="margin-left:0px;"><em></em><a href="/home/home/search">搜索</a></div>
            </div>
            <a href="/home/home/notice" class="top-message"></a>
        </div>
    </header>
    <div class="padt1">
        <nav>
            <div id="picScroll" class="navbox" style="display:none;">
                <div class="hd"><ul></ul></div>
                <div class="bd">
                    <ul class="bd_ul" style="display: none;">
                        <?php for($i=0;$i<=9;$i++){?>
                        <li style="<?php if($type[$i]['app_code']=='specialty') { ?>display:none<?php }?>"><a href="<?= Html::encode($type[$i]['url'])?>?id=<?= Html::encode($type[$i]['id'])?>&appcode=<?=Html::encode($type[$i]['app_code'])?>"><div class="idxicon"><img src="<?= Html::encode($type[$i]['icon'])?>" onerror="javascript:this.src='/images/kong.png'"/></div><p><?= Html::encode($type[$i]['name'])?></p></a></li>
                        <?php }?>
                    </ul>

                    <ul class="bd_ul" style="display: none;">
                        <?php for($i=10;$i<count($type);$i++){?>
                        <li style="<?php if($type[$i]['app_code']=='specialty') { ?>display:none<?php }?>"><a href="<?= Html::encode($type[$i]['url'])?>?id=<?= Html::encode($type[$i]['id'])?>&appcode=<?=Html::encode($type[$i]['app_code'])?>"><div class="idxicon"><img src="<?= Html::encode($type[$i]['icon'])?>" onerror="javascript:this.src='/images/kong.png'"/></div><p><?= Html::encode($type[$i]['name'])?></p></a></li>
                        <?php }?>
                    </ul>
                </div>
            </div>
        </nav>
        <?php if(!empty($advert)){?>
        <div class="htmleaf-container" style="margin-top:0.55rem;">
            <div class="slider">
                <ul class="slider-main">
                  <?php for($i=0;$i<count($advert);$i++){?>
                    <li>
                    <a   href="<?=$advert[$i]['link_url']?$advert[$i]['link_url']:'javascript:;'?>">
                        <img style="" src="<?=$advert[$i]['original_url']?>" alt=""></a>
                    </li>
                  <?php } ?>
                </ul>
            </div>
        </div>
        <?php }?>
        
        <div class="myAdvert flex">
            <div class="leftAd flex_div ">
                <a class="iconBox" href="<?=(isset($otherAdvert[2])?$otherAdvert[2]['link_url']:'javascript:;')?>" ><div class="img"><img src="<?=(isset($otherAdvert[2])?$otherAdvert[2]['original_url']:'/images/icon_ad.png') ?>" /></div></a>
            </div>
            <div class="rightAd flex_div ">
                <a class="iconBox" style="margin-bottom: 1.8%" href="<?=(isset($otherAdvert[3])?$otherAdvert[3]['link_url']:'javascript:;')?>"><!-- <h5>购物大优惠</h5><p>多款商品优惠放送</p> --><div class="img"><img src="<?=(isset($otherAdvert[3])?$otherAdvert[3]['original_url']:'/images/icon_ad.png')?>" /></div></a>
                <a class="iconBox" href="<?=(isset($otherAdvert[4])?$otherAdvert[4]['link_url']:'javascript:;')?>"><!-- <h5>线路精选</h5><p>热门景点一网打尽</p> --><div class="img"><img src="<?=(isset($otherAdvert[4])?$otherAdvert[4]['original_url']:'/images/icon_ad.png')?>" /></div></a>
            </div>
        </div>

        <div class="actybox">
         <ul class="clearfix">
            <li class="fore1" stlye="background:#fff;"><a href="<?=(isset($otherAdvert[5])?$otherAdvert[5]['link_url']:'javascript:;')?>"><!-- <h5>超值折扣菜</h5><p>劲爆折扣乐享不停</p> --><div class="img"><img src="<?=(isset($otherAdvert[5])?$otherAdvert[5]['original_url']:'/images/icon_ad.png')  ?>" /></div></a></li>
            <li class="fore2" stlye="background:#fff;"><a href="<?=(isset($otherAdvert[6])?$otherAdvert[6]['link_url']:'javascript:;')?>"><!-- <h5>购物大优惠</h5><p>多款商品优惠放送</p> --><div class="img"><img src="<?=(isset($otherAdvert[6])?$otherAdvert[6]['original_url']:'/images/icon_ad.png')  ?>" /></div></a></li>
            <li class="fore3" stlye="background:#fff;"><a href="<?=(isset($otherAdvert[7])?$otherAdvert[7]['link_url']:'javascript:;')?>"><!-- <h5>线路精选</h5><p>热门景点一网打尽</p> --><div class="img"><img src="<?=(isset($otherAdvert[7])?$otherAdvert[7]['original_url']:'/images/icon_ad.png') ?>" /></div></a></li>
         </ul>
        </div>
        <div class="made scrollbox" id="horizontal">
            <div class="title"><s></s><b><a href="Hotel.html">商家推荐</a></b></div>
            <div class="madegame">
                <div class="ho_loading"><img src="/images/loading.gif" alt=""></div>
                <ul class="clearfix" id="ho" style="left:0;">
                    
                </ul>
            </div>
        </div>
    </div>
    <div class="idxRecomme" id="listScroll" style="">
            <div class="favor-header-bar hd" id="sidebar" style="position: absolute;">
                <ul class="tabs clearfix">
                    <!--<li class="on" data-id="house" data-type="fc"><a href="javascript:void(0);" id="btn1" hidefocus="true">房产</a></li>
                    <li data-id="shop" data-type="gw"><a href="javascript:void(0);" id="btn2" hidefocus="true">购物</a></li>
                    <li data-id="cooperation" data-type="tc"><a href="javascript:void(0);" id="btn3" hidefocus="true">合作交流</a></li>
                    <li data-id="tour" data-type="jd"><a href="javascript:void(0);" id="btn4" hidefocus="true">景点</a></li>-->

                    <li class="on" data-id="house" data-type="fc"><a href="javascript:void(0);" id="btn1" hidefocus="true">房产</a></li>
                    <li data-id="house" data-type="gw"><a href="javascript:void(0);" id="btn2" hidefocus="true">房产</a></li>
                    <li data-id="house" data-type="tc"><a href="javascript:void(0);" id="btn3" hidefocus="true">房产</a></li>
                    <li data-id="house" data-type="jd"><a href="javascript:void(0);" id="btn4" hidefocus="true">房产</a></li>
                </ul>
            </div>
            <div class="swiper-container favor-list" style="padding-top: 2.45rem;">
                <div class="swiper-wrapper bd" id="swiper_list">
                    <div id="loading" class="align-c"><img src="/images/loading.gif" alt=""></div>
                    <ul class="prolist11 swiper-slide" id="menu">
                        
                    </ul>
                    <div class="align-c color-gray2 f-12" id="LoadMore" style="display:none;">点击加载更多</div>
                </div>
            </div>
        </div>
    <?php require './modules/inc/menu.php';?>
    <script type="text/html" id="recommend_tpml">
            {{each data as da}}
                <li>
                    <a href="javascript:;" data-id="{{da.id}}" data-code="{{da.app_code}}" data-pid="{{da.type_pid}}">         
                        <img src="{{da.logo}}" class="gameImg" />
                        <p>{{da.name}}</p>
                        <span>{{da.summary}}</span>
                    </a>
                </li>
            {{/each}}
    </script>
    <script type="text/html" id="Touch_tpml">
       {{each data as da}} 
        <li>
            <a href="javascript:;" data-id="{{da.id}}" data-code="{{da.app_code}}" data-pid="{{da.type_pid}}">
                <div class="img">
                    <span><img src="{{da.logo}}" /></span>
                </div>
                <div class="info">
                    <h3>{{da.name}}</h3>
                    <p>{{da.summary}}</p>
                </div>
            </a>
        </li>
        {{/each}}
    </script>
    <script type="text/html" id="kong_tpml">
        <div class="kong">
            <img src="/images/no.png" width="80">
        </div>
    </script>
</div>
<?php $this->beginBlock('js') ?>
    <script type="text/javascript" src="/modules/js/TouchSlide.1.1.js"></script>
    <script type="text/javascript" src="/modules/js/jquery-1.9.1.js"></script>
    <script type="text/javascript" src="/modules/js/osSlider.js"></script>
    <script type="text/javascript" src="/mobiend/js/lib/jquery.cxscroll.js"></script>
    <script type="text/javascript">
    if($(".slider").length){
        $(".htmleaf-container").show();
        var sliders = new osSlider({ //开始创建效果
        pNode:'.slider', //容器的选择器 必填
        cNode:'.slider-main li', //轮播体的选择器 必填
        speed:1000, //速度 默认3000 可不填写
        autoPlay:true //是否自动播放 默认true 可不填写
    });

    }

    </script>
    <script type="text/javascript">
        $(function(){
            $("#picScroll").show();
            $('.bd_ul').show();
            TouchSlide({ 
                slideCell:"#picScroll",
                titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
                autoPage:true //自动分页
            });
        });
    </script>
    <script type="text/javascript">
        window.WxJSSDKSign='<?=json_encode($wxjsdk)?>';
        seajs.use('/modules/js/home/home/index',function(index){
            index.init();
        });
    </script>
<?php $this->endBlock() ?>
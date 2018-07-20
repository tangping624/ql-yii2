<?php
use yii\helpers\Html;
$this ->
title='房产';
?>

<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/base.css" />
<!-- <link rel="stylesheet" href="/modules/css/main.css" /> -->
<link rel="stylesheet" href="/modules/css/index/index.css" />
<style type="text/css">
        .rankbox{position: absolute;top:0px;border-top: 1px solid #ccc;}
        .rankbox li {line-height:2.2rem;}
        .rankbox li a{font-size:0.78rem;}
        .rankbox li i{top:-0.25rem;}
        .rankbox li.cur i{top:-0.4rem;}
        .poprank li{border: 1px solid #e6e6e6;border-width: 0 1px 1px 0;}
        .layer{position: absolute;max-height:15rem;top:2.3rem;}
        .layer li{text-align: left;}
        .conbox{background: #fff}
        .idxAd {
            margin-top: 0;
        }
        body{background:#fff;}
        .idxAd li{width:100%;height:6.5rem;}
        .padb1{padding-bottom: 0em;padding-top: 0}
        .padt21{padding-top: 0px;}
        .fl{float: left}
        .fr{float: right}
        .Pnearby .popR {width:60%;}
        .hot_wrapper .hot_pic{width: 100%;z-index: 1;height:100%;}
        .hot_wrapper li.fl>div>div{position: absolute;z-index: 100;color: #fff;top: 0;left: 0;}
        .tips{border:1px solid #ddd;border-radius: 30px;padding:2px 5px;background: #fff;color: #333;font-size: 0.67em;opacity: 0.9;overflow: hidden;text-overflow:ellipsis;white-space: nowrap;}
        .title{font-size: .78rem;
         background: url(/images/arrowR.png) no-repeat right 10px;
         background-size: 0.5rem auto;
         margin-right: 0.45rem;
         padding-left: 0.45rem;
         padding-bottom: 0.2rem;
         height: 1.67rem;
         line-height: 1.67rem}
        .hot_wrapper ul li{margin-right:1.4%;}
        .hot_wrapper ul li:last-child{margin-right:0px;}
        .Pnearby .popL li{border-bottom: 1px solid #e6e6e6;}
        .Pnearby .popL li>a{padding:0 0.5rem;}
        .prolist1 li {background: url(/images/arrowR.png) no-repeat right center;background-size: 0.5rem auto;
          }
        .prolist1 li .info i {font-style: normal;padding: 0 .1rem;border-radius: .1rem;color: #fff;font-size: .56rem;margin-right: .2rem;display:inline-block;margin-top:0.2rem;}
        .prolist1 li .info p{font-size: .67rem;color: #999;margin-top: .5rem;line-height: 1.2;}
        .prolist1 li .img{width:6.5rem;height:5rem;}
        .scroll-wrap{margin-top:2.45rem;}
</style>
<?php $this->endBlock() ?>
<div class="page-content activity-wrap"> 
    <header>
        <div class="Head">
            <a class="top-back" href="javascript:history.back(-1);"></a>
            <div class="Hcon">
                <h1 style="font-size:18px;">产品列表</h1>
            </div>
        </div>
    </header>
    <div class="scroll-wrap" id="scrollWrap" >
        <div class="scroll-box">
             <div class="idxAd"  id="picScroll2" style="display:none;">
                 <div class="hd" style="display:none;"><ul></ul></div>
                      <div class="bd">
                      <?php foreach ($advert as $v){?>
                      <ul>
                          <li><a href="<?=$v['link_url']?>"><img src="<?=$v['original_url']?>" /></a></li>
                  <!-- <li><a href="/Catedetail.html"><img src="ImgUpload/idxad2.jpg" /></a></li> -->
                      </ul>
                      <?php }?>
                 </div>
             </div>
      
             
            <div class="padt21 clearfix" style="position:relative;display:none;">
                 <div class="rankbox" style="">
                  <?php for($i=0;$i<100;$i++){?>
                  <?php } ?>
                      <ul class="flex" style="">
                          <li class="flex_div">
                              <a href="#">
                                  <span>全部城市</span> <i></i>
                              </a>
                          </li>
                          <li class="flex_div">
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
                 </div>
                 <div class="Prank" style="display:none;">
                     <div class="mask"></div>
                     <div class="layer">
                         <div class="conbox Pnearby" style="display:none;">
                             <ul class="popL" id="scroller" >
                                  <li data-id="" class="" id="allCity">
                                    <a class="">全部</a>
                                  </li>
                                 <?php for ($i=0;$i<count($city);$i++){?>

                                     <li data-id="<?= Html::encode($city[$i]['id'])?>" class=""><a href="#list<?=$i?>" class=""><?= Html::encode($city[$i]['treeText'])?></a>

                                     </li>

                               <?php } ?>
                             </ul>
                            <?php for ($val=0;$val<count($city);$val++){?>

                             <ul class="popR poprank pop1" style="display:none;" id="list<?=$val?>">
                                 <li data-id="" class="allType" id="allArea">
                                     <a class="ellipsis" style="width: 100%">全部</a>
                                 </li>
                                  <?php if(count($city[$val]['childNode'])>0) { ?>
                                  <?php foreach ($city[$val]['childNode'] as $j){?>

                                  <li data-id="<?= Html::encode($j['id'])?>" class="child"><a href="#" class=""><?= Html::encode($j['treeText'])?>      </a></li>

                                   <?php }?>
                                   <?php }?>
                               </ul>
                             <?php }?>
                         </div>
                         <div class="conbox" style="display:none">
                              <ul class="poprank">
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
      <div  style="margin-top: 2.3rem;">

            <div class="min-h" style="">
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
    <% for (var i=0; i<list.length; i++) { %>
        <li class="clearfix">
            <a href="javascript:;" data-id="<%= list[i].id%>">
                <div class="img"><span><img src="<%= list[i].logo%>" width="100%"/></span></div>
                <div class="info">
                    <h3 class="ellipsis"><%= list[i].NAME%></h3>
                    <% if (list[i].tags.length>0) { %>
                      <p>
                        <% for(var j=0;j<list[i].tags.length;j++){ %>
                        <i class="bg-blue"><%=list[i].tags[j].NAME%></i>
                        <% } %>

                      </p>
                    <% } %>
                    <p class="agent ellipsis"><%= list[i].summary%></p>
                </div>
            </a>
        </li>
    <% } %>
</script>
    <script type="text/template" id="empty_tmpl">
      <div class="align-c emptyTips" style="padding:50px 0;">
        <img src="/images/no.png" width="80"/>
        <!-- <p class="f-12">还没有相关信息</p> -->
      </div>
    </script>
<?php $this->beginBlock('js') ?>
<script type="text/javascript" src="/mobiend/js/lib/TouchSlide.1.1.js"></script>
<script type="text/javascript">
     var api = 'house/house/';
        seajs.use('/modules/js/house/type-index',function(index){
            index.init();
        });
</script>
<?php $this->endBlock()?>
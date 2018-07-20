<?php
use yii\helpers\Html;
function convertUrlQuery($query)
{
    $queryParts = explode('&', $query);
    $params = array();
    foreach ($queryParts as $param) {
        $item = explode('=', $param);
        $params[$item[0]] = $item[1];
    }
    return $params;
}
$url=parse_url($_SERVER['REQUEST_URI']);
//var_dump(convertUrlQuery($url['query'])['appcode']);
$appcode=convertUrlQuery($url['query'])['appcode'];
$titl='外汇';
//$this ->title=$appcode=='migrate'?'移民':($appcode=='teach'?'教育培训':($appcode=='serve'?'服务':($appcode=='vip'?'vip服务':($appcode=='shop'?'购物惠':''))));

if($appcode=='migrate'){$this ->title='移民';$titl='移民';}
if($appcode=='teach'){$this ->title='教育培训';$titl='教育培训';}
if($appcode=='serve'){$this ->title='服务';$titl='服务';}
if($appcode=='invest'){$this ->title='投资';$titl='投资';}
if($appcode=='vip'){$this ->title='VIP服务';$titl='VIP服务';}
if($appcode=='shop'){$this ->title='购物惠';$titl='购物惠';}
if($appcode=='cooperation'){$this ->title='合作交流';$titl='合作交流';}
if($appcode=='sports'){$this ->title='休闲娱乐';$titl='休闲娱乐';}
if($appcode=='urgent'){$this ->title='紧急';$titl='紧急';}
if($appcode=='tour'){$this ->title='旅游';$titl='旅游';}
if($appcode=='repast'){$this ->title='外汇';$titl='外汇';}
if($appcode=='house'){$this ->title='房产';$titl='房产';}
?>
<?php //var_dump(isset($params['from']));exit;?>
<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/base.css" />
<link rel="stylesheet" href="/modules/css/main.css" />
<style type="text/css">
    .scroll-wrap{top:2.45rem;}
    .idxAd1{margin-top:0px;}
    .rankbox{z-index: 1000;}
    .hot_wrapper{margin: 0.36rem 0;}
    .hot_wrapper s{    background: #f47920;
    width: .1rem;
    height: .75rem;
    border-radius: .05rem;
    display: inline-block;
    float: left;
    margin: .5rem .3rem 0 0;}
    #loading img{width: 20px;margin-top: 20px;}
    #LoadMore img{width:20px;}
    #menu li:last-child{margin-bottom: 0.56rem;}
</style>
<!--<link rel="stylesheet" href="/modules/css/index/index.css" />-->
<?php $this->endBlock() ?>
<div class="page-content activity-wrap">
    <header class="header_flxed">
        <div class="Head">
            <a class="top-back" href="javascript:history.back(-1);"></a>
            <div class="Hcon">
                  <h1 style="font-size:1rem;"><?= Html::encode($titl)?></h1>
            </div>
            <a href="/home/home/search" style="width: 1rem;height: 1rem;background: url(/images/top-search.png) no-repeat center;position: absolute;right: 0;top: .4rem;background-size: 100% auto;"></a>
        </div>
    </header>
    <div class="scroll-wrap" id="scrollWrap">
        <div class="scroll-box">

        <?php if(!empty($advert)){?>
        <div class="idxAd1"  id="picScroll2" style="display:none;">
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
        <div class="padt1 padb1" style="display: none">
            <nav>
                <div id="picScroll" class="navbox" style="<?= count($type)?'padding-bottom:1.6rem':'padding-bottom:0'?>">
                    <div class="hd"><ul></ul></div>
                    <div class="bd">
                        <?php $count=count($type);?>
                        <!--外层生成多个ul，里面生成多个li，每次遍历都从下一页（外层遍历的索引*pageSize）开始-->
                        <?php for ($i=1;$i<=ceil($count/10);$i++){?>
                            <ul>
                                <?php for ($j=($i-1)*10;$j<$i*10;$j++){?>
                                    <?php if($j<$count){?>
                                    <li data-id="<?= Html::encode($type[$j]['id'])?>">
                                        <a href="javascript:;">
                                            <div class="idxicon">
                                                <img src="<?= Html::encode($type[$j]['icon'])?>" />
                                            </div>
                                            <p class="ellipsis" style="width: 100%"><?= Html::encode($type[$j]['name'])?></p>
                                        </a>
                                    </li>
                                    <?php }?>
                                <?php }?>
                            </ul>
                        <?php }?>
                    </div>
                </div>
            </nav>
        </div>
        <?php if(!empty($news)){?>
            <?php $count=count($news);$imgList=0?>
            <div class="hot_wrapper" style="display: none">
                <p class="title " style=""><s></s>新鲜事</p>
                <ul class="clearfix">
                    <?php foreach ($news as $v){?>
                        <li class="flex_div imgico" data-id="<?= Html::encode($v['id'])?>" style="">
                            <img src="<?= Html::encode($v['photo'])?>" class="hot_pic">
                            <div class="text_wrapper">
                                <p class="titleinfo ellipsis"><?= Html::encode($v['title'])?></p>
                                <div class="summary ellipsis"></div>
                                <div class="con" style="display: none"><?= $v['content']?></div>
                            </div>
                        </li>
                    <?php }?>
                    <?php for($i=0;$i<3-$count;$i++){?>
                        <li class="flex_div" style="">
                            <img src="/images/background.png" class="hot_pic">
                        </li>
                    <?php }?>
                </ul>
            </div>
        <?php }?>
        <!--排序 S-->
      <div class="padt21">
          <div class="rankbox" style="display: none">
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
<!--                              <li data-id="" class="" id="allCity">-->
<!--                                   <a class="ellipsis" style="width: 100%">全部</a>-->
<!--                              </li>-->
                             <?php for ($i=0;$i<count($city);$i++){?>
                                 <li data-id="<?= Html::encode($city[$i]['id'])?>" class="">
                                     <a href="#list<?=$i?>" class="ellipsis" style="width: 100%"><?= Html::encode($city[$i]['treeText'])?></a>
                                 </li>
                             <?php } ?>
                         </ul>
<!--                         --><?php //for ($val=0;$val<count($city);$val++){?>
<!--                             <ul class="popR poprank pop1" style="display:none;" id="list--><?//=$val?><!--">-->
<!--                                 --><?php //if(count($city[$val]['childNode'])>0) { ?>
<!--                                     --><?php //foreach ($city[$val]['childNode'] as $j){?>
<!--                                         <li data-text="--><?//= Html::encode($j['treeText'])?><!--"-->
<!--                                             data-pid="--><?//= Html::encode($j['pid'])?><!--"-->
<!--                                             data-pname="--><?//= Html::encode($j['pName'])?><!--"-->
<!--                                             data-id="--><?//= Html::encode($j['id'])?><!--" class="child">-->
<!--                                             <a href="#" class="ellipsis" style="width: 100%" style="width: 90%">-->
<!--                                                 --><?//= Html::encode($j['treeText'])?><!--</a>-->
<!--                                         </li>-->
<!--                                     --><?php //}?>
<!--                                 --><?php //}?>
<!--                             </ul>-->
<!--                         --><?php //}?>
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
<!--        <div class="scroll-wrap" id="scrollWrap" style="margin-top:500px; --><?//=isset($params['from'])&&$params['from']=='index'?'top:45px;bottom:55px;padding-bottom: 10px;':'top:0;bottom:30px;padding-bottom: 10px;'?>
<!--            ">-->
        <div  style="margin-top: 2.5rem;">
            <div>
                <div id="loading" class="align-c"><img src="/images/loading.gif" alt=""></div>
                <ul class="prolist1 menu clearfix" id="menu"> <!--style="border-top: 0.56rem solid #f0f0f0;"-->
                    
                </ul>
                <div id="LoadMoreWrap">
                    <div class="align-c color-gray2 f-14" id="LoadMore" style="display:none;">点击加载更多</div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
<script type="text/template" id="menu_tmpl">
    <% for (var i=0; i<data.length; i++) { %>
        <li class="clearfix">
            <a href="javascript:;" data-id="<%=data[i].id%>" class="clearfix" data-type_pid="<%=data[i].type_pid%>">
                <div class="img"><span><img src="<%=data[i].logo%>" width="100%"/></span></div>
                <div class="info">
                    <h3 class="ellipsis"><%=data[i].NAME%></h3>
                    <p class="agent ellipsis2"><%=data[i].summary%></p>
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
    seajs.use('/modules/js/public/index',function(index){
        index.init();
    })
</script>
<!--    <script src="js/portamento.js"></script>-->
<?php $this->
endBlock() ?>
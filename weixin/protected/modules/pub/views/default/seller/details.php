<?php
use yii\helpers\Html;
$this ->title=$details['NAME'];
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
?>
<!-- --><?php //var_dump($details);exit;?>
<?php $this->
beginBlock('css') ?>
<link rel="stylesheet" type="text/css" href="/modules/css/index.css"/>
<link rel="stylesheet" href="/modules/css/base.css" />
<link rel="stylesheet" href="/modules/css/details.css" />
<link rel="stylesheet" href="/modules/css/photoswipe/photoswipe.css">
<link rel="stylesheet" href="/modules/css/photoswipe/default-skin/default-skin.css">
<style type="text/css">
#loading{position: fixed;top:45%;left: 45%;z-index: 1000}
</style>
<?php $this->endBlock() ?>
    <input type="hidden" id="cookie" value="<?= Html::encode($cookie)?>">
    <input type="hidden" id="shopid" value="<?= Html::encode($details['id'])?>">
<header>
    <div class="Head">
        <a class="top-back" href="javascript:history.back(-1);"></a>
        <div class="Hcon" style="margin: 0 3rem;">
            <h1 style="overflow:hidden;width:100%;"><?= Html::encode($details['NAME'])?></h1>
       </div>
<!--        <a class="icon dz --><?//= $details['prise']?'yi-dz':'wei-dz'?><!--" href="javascript:;" style="right: 2.6rem;"></a>-->
        <a class="icon sc <?= $details['collection']?'yi-sc':'wei-sc'?>" href="javascript:;" style="right:0"></a>
<!--        <a href="/home/home/search" style="width: 1rem;height: 1rem;background: url(/images/top-search.png) no-repeat center;position: absolute;right: 0;top: .4rem;background-size: 100% auto;"></a>-->
    </div>
</header>
<div class="dialog-cls-box dialog-msg-box hide" id="dialog_msg_box" style="position: fixed; left: 119.5px; top: 346px;">
    <div class="dialog-cls-wrap dialog-msg-wrap" id="dialog_msg_wrap">
        <div class="dialog-msg-content" id="dialog_msg_content"></div></div>
</div>
    <div id="loading" class="align-c"><img src="/images/loading.gif" alt=""></div>
 <div class="padt1">
     <ul class="prolist1 clearfix infoWrapper">
        <li class="clearfix">
             <div class="img fd-locate">
                 <a href="javascript:;">
                     <span class="imgList">
                         <?php foreach (array_reverse($images) as $v){?>
                            <img src="<?= $v['original_url']?>"/>
                         <?php }?>
                     </span>
                     <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
                         <div class="pswp__bg"></div>
                         <div class="pswp__scroll-wrap">
                             <div class="pswp__container">
                                 <div class="pswp__item"></div>
                                 <div class="pswp__item"></div>
                                 <div class="pswp__item"></div>
                             </div>
                             <div class="pswp__ui pswp__ui--hidden">
                                 <div class="pswp__top-bar">
                                     <div class="pswp__counter"></div>
                                     <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
<!--                                     <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>-->
<!--                                     <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>-->
                                     <div class="pswp__preloader">
                                         <div class="pswp__preloader__icn">
                                             <div class="pswp__preloader__cut">
                                                 <div class="pswp__preloader__donut"></div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                                     <div class="pswp__share-tooltip"></div>
                                 </div>
                                 <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
                                 </button>
                                 <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
                                 </button>
                                 <div class="pswp__caption">
                                     <div class="pswp__caption__center"></div>
                                 </div>
                             </div>
                         </div>

                     </div>
                     <div class="imgnum"><?= Html::encode(count($images))?>张</div>
                 </a>
             </div>
             <div class="info intro">
                 <h3><?= Html::encode($details['NAME'])?></h3>
                 <div class="com praise" style="width: 50%">
                    <span class="dz <?= $details['prise']?'yi-dz':'wei-dz'?>" style="position: relative;top: 0.4rem;right: 0"></span></label></span>
                    (<span class="dzNum"><?= Html::encode($details['dz_num'])?></span>)
                 </div>
                 <?php if(!empty($appcode)){?>
                     <?php if($appcode!=='wiki'&&$appcode!=='ctrip'&&$appcode!=='lobby') {?>
                     <div class="about clearfix" style="margin-top: 0;">
                        <a class="fd-right btn-intro" href="javascript:;">查看详细<s></s></a>
                     </div>
                     <?php }?>
                 <?php }?>
             </div>
          </li>
       </ul>
       <div class="DetItem bg-white address">
           <?php $latitudes=Html::encode($details['latitudes']);$longitudes=Html::encode($details['longitudes']);$address=Html::encode($details['address'])?>
         <a href="<?=$address?'/pub/map/map?address='.$address.'&lat='.$latitudes.'&lng='.$longitudes:'javascript:;'?>">
            <div class="site clearfix">
                <em class="fl"><img src="/images/location.png" /></em>
                <span class="black addr ellipsis"><?= Html::encode($details['address']?$details['address']:'地址为空哦')?></span>
            </div>
         </a>
      </div>
      <div class="DetItem bg-white clearfix">
         <a href="tel:<?= Html::encode($details['linktel'])?>">
            <div class="site clearfix">
                <em><img src="/images/phone.png" /></em>
                <span class="black"><?= Html::encode($details['linktel'])?></span>
            </div>
         </a>
      </div>
    <div class="DetItem bg-white">
        <div>
            <div class="site clearfix">
                <em><img src="/images/fax.png"></em>
                <span class="black"><?= Html::encode($details['fax'])?></span>
            </div>
        </div>
    </div>
    <div class="DetItem bg-white">
        <div>
            <div class="site clearfix">
                <em><img src="/images/mail.png"></em>
                <span class="black"><?= Html::encode($details['mail'])?></span>
            </div>
        </div>
    </div>
     <div class="Detbox">
         <div class="tit borb1"><s></s><b>商家介绍</b></div>
         <div class="prolist1 smallImg con" style="position:relative;padding-bottom:5px;">
            <img src="<?= Html::encode($details['logo'])?>">
            <div class="over" style="margin-bottom:20px;"><?= $details['content']?>
                
            </div>
            <a href="javascript:;" class="more" style="background: url(/images/top-back.png) no-repeat center;background-size:20px;width:20px;height:20px;position:absolute;left:50%;bottom:6px;display:none;transform:translate(-50%,0px)rotate(-90deg);margin-right:10px;"></a>
          </div>
     </div>
     <div class="Detbox">
          <div class="tit borb1"><s></s><b>商家提示</b><span class="fs24 gray"></span></div>
         <div class="prolist1 smallImg con" style="text-indent: 1.5rem">
             <?= Html::encode($details['remind'])?>
         </div>
     </div>
     <div class="Detbox">
         <div class="tit borb1"><s></s><b>相关推荐</b><span class="fs24 gray">(<span class="reNum"></span>)</span></div>
         <ul class="prolist1 smallImg" id="nearby">

         </ul>
     </div>
</div>
    <script type="text/template" id="nearby_templ">
        <% for (var i=0; i<data.length; i++) { %>
         <li>
             <a href="javascript:;">
                 <div class="img" style="text-align: inherit"><span><img src="<%=data[i].logo%>" /></span></div>
                 </a>
                 <div class="info clearfix">
                     <div class="fl" style="width: 63%">
                         <h3><%=data[i].name%></h3>
                         <div class="com">
                             <span class="ellipsis2"><%=data[i].summary%></span>
                         </div>
                     </div>
                     <div class="about" style="float:right;" data-id="<%=data[i].id%>" data-type_pid="<%=data[i].type_pid%>">
                         <!-- <span class="fd-left">2235条评论</span> -->
                         <a class="fd-right btn-intro" href="javascript:;">查看详细<s></s></a>
                     </div>
                 </div>
         </li>
        <% } %>
    </script>
<?php $this->beginBlock('js') ?>
<script type="text/javascript">
//  $(function(){
//     $(".Jshare").click(function(){
//        $(".Pshare").show()
//        $("body,html").css({"overflow":"hidden"});
//     })
//     $(".mask").click(function(){
//        $(".Pshare").hide()
//        $("body,html").css({"overflow":"auto"});
//     })
//  })
</script>
    <script type="text/javascript" src='/modules/js/photoswipe/photoswipe.js'></script>
    <script src="/modules/js/photoswipe/photoswipe.min.js"></script>
    <script src="/modules/js/photoswipe/photoswipe-ui-default.min.js"></script>
<!--    window.WxJSSDKSign='--><?//=json_encode($wxjsdk)?><!--';-->
    <script>
    seajs.use('/modules/js/public/details',function(details){
        details.init();
    })
</script>
<?php $this->endBlock() ?>
<?php
use yii\helpers\Html;
$this ->title='我的足迹';
?>
<?php $this->beginBlock('css') ?>
    <link rel="stylesheet" href="/modules/css/index.css" />
    <link rel="stylesheet" href="/modules/css/base.css" />
<?php $this->endBlock() ?>
    <header>
     <div class="Head">
        <a class="top-back" href="javascript:history.back(-1);"></a>
        <div class="Hcon">
          <h1 style="font-size: 0.88rem;">我的足迹</h1>
       </div>
       <!-- <a href="javascript:;" class="top-text2 reload" style="font-size: 0.7rem;">刷新</a> -->
       <a href="javascript:;" class="top-text J-delete" style="font-size: 0.7rem;">删除</a>
     </div>
  </header>
  <!--header E-->
  <div class="padt2" style="padding-top: 4.4rem;">
     <!-- <div class="rankbox">
        <ul class="flex">
           <li class="flex_div cur" type="1"><a href="javascript:;"><span style="display: inline;">商家</span></a></li>
           <li class="flex_div" type="2"><a href="javascript:;"><span style="display: inline;">产品</span></a></li>
        </ul>
     </div> -->
    
    <div class="scroll-wrap idxRecomme" id="scrollWrap" style="margin-top: 2.45rem;">
         <div>
           <div id="loading" class="align-c" style="display: none"></div>
                  <ul class="myview" id="menu" style="min-height: 30rem">
                    
                  </ul>
                  <div id="LoadMoreWrap">
                      <div class="align-c color-gray2 f-12" id="LoadMore" style="display:none;padding: 0.36rem 0;">上滑加载更多</div>
                  </div> 
         </div>
       </div>
  </div>
  <script type="text/html" id="trackList">
                    {{each list as value1}}
                    <li>
                      <div class="Viewtime" style="font-size: 0.6rem;">{{value1.created_on}}</div>
                        <ul class="prolist1" style="border-top:1px solid #e6e6e6;">
                            {{each value1.data as value2}}
                                <li id="{{value2.id}}" seller_id="{{value2.seller_id}}" product_id="{{value2.product_id}}" appcode="{{value2.app_code}}">
                                  <div class="DeleteIcn"></div>
                                  <a href="javascript:;">
                                     <div class="img" style="width: 6.5rem;height:4rem;"><span><img src="{{value2.logo}}" style="width: 100%;height: 4rem;" /></span></div>
                                     <div class="info" style="min-height: 4rem;">
                                         <h3>{{value2.NAME}}</h3>
                                         <p class="agent ellipsis">{{value2.summary}}</p>
                                         <div class="com" style="display:none;">
                                            <span class="star"><label style="width:80%"></label></span>
                                            <span>4分</span> <s></s> <span>1335条评论</span>
                                         </div>
                                         <div class="about" style="display:none;">
                                            <span class="price"><b>￥355</b>起</span>
                                         </div>
                                     </div>
                                  </a>
                                </li>
                            {{/each}}
                         </ul> 
                    </li> 
                    {{/each}}
                </script>
<?php $this->beginBlock('js') ?>
    <script type="text/javascript">
        seajs.use(['/modules/js/member/track'],function(index){
           index.init();
        });
    </script>
<?php $this->endBlock() ?>
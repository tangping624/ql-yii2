<?php
use yii\helpers\Html;
$this ->title='我的收藏';
?>
<?php $this->beginBlock('css') ?>
    <link rel="stylesheet" href="/modules/css/index.css" />
    <link rel="stylesheet" href="/modules/css/base.css" />
<?php $this->endBlock() ?>
   <!--header S-->
  <header>
     <div class="Head">
        <a class="top-back" href="javascript:history.back(-1);"></a>
        <div class="Hcon">
          <h1 style="font-size: 0.88rem;">我的收藏</h1>
       </div>
       <a href="javascript:;" class="top-text J-delete" style="font-size: 0.7rem;">删除</a>
     </div>
  </header>
  <!--header E-->
  <div class="padt2">
     <div class="rankbox">
        <ul class="flex">
           <li class="flex_div cur" type="1"><a href="javascript:;"><span style="display: inline;">商家</span></a></li>
           <li class="flex_div" type="2"><a href="javascript:;"><span style="display: inline;">产品</span></a></li>
           <li class="flex_div" type="3"><a href="javascript:;"><span style="display: inline;">游说</span></a></li>
        </ul>
     </div>
       <div class="scroll-wrap idxRecomme" id="scrollWrap" style="margin-top: 4.55rem;">
         <div>
           <div id="loading" class="align-c" style="display: none"></div>
                  <ul class="prolist1 menu clearfix" id="menu" style="min-height: 30rem">
                    
                  </ul>
                  <div id="LoadMoreWrap">
                      <div class="align-c color-gray2 f-12" id="LoadMore" style="display:none;">上滑加载更多</div>
                  </div> 
         </div>
       </div>
  </div>  
     
  <script type="text/html" id="collectionList">
    {{each list as value}}
            <li id="{{value.id}}" seller_id="{{value.seller_id}}" product_id="{{value.product_id}}" appcode="{{value.app_code}}" mid="{{value.mid}}">
              <div class="DeleteIcn"></div>
              <a href="#" class="clearfix">
                 <div class="img" style="width: 6.5rem;height: 4rem;"><span><img src="{{value.logo}}" style="width: 100%;height: 4rem;" /></span></div>
                 <div class="info" style="min-height: 4rem;">
                     <h3>{{value.NAME}}</h3>
                     <p class="agent ellipsis">{{value.summary}}</p>
                     <p style="display: none;"><i class="bg-blue">景点</i></p>
                     <div class="about clearfix" style="display: none;">
                        <span class="star"><label style="width:80%"></label></span>
                        <span>4分</span>
                     </div>
                 </div>
              </a>
            </li> 
    {{/each}}               
  </script>
<?php $this->beginBlock('js') ?>
    
    <script type="text/javascript">
        seajs.use(['/modules/js/member/collection'],function(login){
           login.init();
        });
    </script>
<?php $this->endBlock() ?>
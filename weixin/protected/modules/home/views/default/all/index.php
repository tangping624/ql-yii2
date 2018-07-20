<?php
use yii\helpers\Html;
$this ->title='全部分类';
?>
<?php $this->beginBlock('css') ?>
    <link rel="stylesheet" href="/modules/css/index/index.css" />
    <link rel="stylesheet" href="/modules/css/base.css" />
    <style type="text/css">
    </style>
<?php $this->endBlock() ?>
<!--header S-->
<!--  <header>-->
<!--     <div class="Head">-->
<!--       <a class="top-back" href="javascript:history.back(-1);"></a>-->
<!--        <div class="Hcon">-->
<!--          <h1 style="font-size: 0.98rem;">全部分类</h1>-->
<!--       </div>-->
<!--       -->
<!--     </div>-->
<!--  </header>-->
  <!--header E-->
  <div class="padt1" id="classBox" style="padding-top: 0">
     
  </div>
  <script type="text/html" id="classList">
      {{each list as value1}}
            {{if value1.app_code!=='all'}}
            <div class="categorybox" code="{{value1.code}}">
                 <div class="tit" id="{{value1.id}}" appcode="{{value1.app_code}}"><em><img src="{{value1.icon}}"/></em><b>{{value1.treeText}}</b></div>
                 <div class="category">
                   <ul class="clearfix">
                   {{if value1.childNode.length != 0}}
                        <li><a href="javascript:;" id="{{value1.id}}" appcode="{{value1.app_code}}" data-val="{{value1.treeText}}" class="allDel">全部</a></li>
                         {{each value1.childNode as value2}}
                              <li><a href="#" id="{{value1.id}}" type_id="{{value2.id}}" appcode="{{value1.app_code}}" class="classDel">{{value2.treeText}}</a></li>
                         {{/each}} 
                    {{/if}}
                   </ul>
                 </div>
             </div>
            {{/if}}
      {{/each}}
  </script>
  

<?php $this->beginBlock('js') ?>
    <script type="text/javascript">
        seajs.use('/modules/js/home/all/index',function(index){
            index.init();
        });
    </script>
<?php $this->endBlock() ?>
<?php
use yii\helpers\Html;
$this ->title='商城';
?>
<?php $this->beginBlock('css') ?>
    <link rel="stylesheet" href="/modules/css/index/index.css" />
    <link rel="stylesheet" href="/modules/css/base.css" />
    <!-- <link rel="stylesheet" href="/modules/css/based.css" /> -->
    <link rel="stylesheet" href="/modules/css/demo.css" />
    <!-- <link rel="stylesheet" href="/modules/css/default.css" /> -->
    <link rel="stylesheet" href="/modules/css/osSlider.css" />
    <style type="text/css">
        .sort_name{height:2.35rem;line-height:2.35rem;padding-left: 0.83rem;font-size: 0.78rem;}
        .icon_add{width:2.35rem;height:2.35rem;display: inline-block;float: right;background: url(/images/right_icon.png) no-repeat 0.8rem 0.8rem;background-size: 25% 30%;}
    </style>
<?php $this->endBlock() ?>
<header>
     <div class="Head">
        <a class="top-back" href="javascript:history.back(-1);"></a>
        <div class="Hcon">
          <h1 style="font-size: 18px;">选择城市</h1>
       </div>
       
     </div>
  </header>
  <!--header E-->
  <div class="padt1">
     <div class="searchbox">
         <em></em>
         <input class="city_se" type="text" placeholder="请输入城市名称或首字母查询" />
     </div>
     <div class="sort_letter" style="text-align: center;">当前所在城市</div>
     <div class="sort_list"><div class="num_name"></div></div>
     <div id="letter" ></div>
    <div class="sort_box" id="city_list">
    <script type="text/html" id="city_tpml">
        <div class="sort_letter" style="text-align: center;">切换城市</div>   
        {{each data as da}}
        <div class="sort_letter sort_name" data-id="{{da.id}}" data-latitudes="{{da.latitudes}}" data-longitudes="{{da.longitudes}}">{{da.name}}<span class="icon_add"></span></div>
            {{each da.childNode as dano}}
            <div class="sort_list">
                <div class="city num_name" data-id="{{dano.id}}" data-latitudes="{{dano.latitudes}}" data-longitudes="{{dano.longitudes}}">{{dano.treeText}}</div>
            </div>
            {{/each}} 
        {{/each}}
    </script> 
    </div>
    <div class="initials">
        <ul>

        </ul>
    </div>
  </div>
  

<?php $this->beginBlock('js') ?>
    <script type="text/javascript" src="/modules/js/jquery-1.9.1.js"></script>
    <script type="text/javascript" src="/mobiend/js/lib/art-template.js"></script>
    <script type="text/javascript">
        seajs.use('/modules/js/home/city/index',function(index){
            index.init();
        });
    </script>
<?php $this->endBlock() ?>
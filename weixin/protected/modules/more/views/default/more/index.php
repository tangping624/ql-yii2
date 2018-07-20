<?php
use yii\helpers\Html;
$this ->title='更多';
?>
<?php $this->beginBlock('css') ?>
    <link rel="stylesheet" href="/modules/css/index/index.css" />
    <link rel="stylesheet" href="/modules/css/base.css" />
<?php $this->endBlock() ?>
    <div class="page page-index page-fiexed clearfix" id="page">
          <!-- header S -->
          <header>
             <div class="Head">
                <a class="top-back" href="javascript:history.back(-1);"></a>
                <div class="Hcon">
                  <h1 style="font-size: 0.98rem;">更多</h1>
               </div>
             </div>
          </header>
          <!--header E-->
          <div class="padt1 padb1" style="padding-top: 0;margin-top:2.45rem;">
              <div class="Box" style="margin-top: 0">
                 <ul class="clearfix">
                    <li><b>无图版本</b> <div class="switch"></div></li>
                    <li><a href="#"><b>切换城市</b></a></li>
                    <li><a href="#"><b>切换英文</b></a></li>
                    <li><span class="fd-right">1077k</span><b>清除缓存</b></li>
                    <li><span class="fd-right">当前已是最新版本</span><b>检测更新</b></li>
                    <li><a href="#"><b>关于</b></a></li>
                 </ul>
              </div>
              <div class="morebtn"><a href="#"><em></em>医疗急救电话</a></div>
          </div>

        <?php require './modules/inc/menu.php';?>
    </div>
<?php $this->beginBlock('js') ?>
    <script type="text/javascript">
       $(function(){
          $(".switch").click(function(){
              $(this).toggleClass("on") 
           })
        })
     </script>
<?php $this->endBlock() ?>
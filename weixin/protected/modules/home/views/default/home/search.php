<?php
use yii\helpers\Html;
$this ->title='';
?>
<?php $this->beginBlock('css') ?>
    <link rel="stylesheet" href="/modules/css/base.css" />
    <link rel="stylesheet" href="/modules/css/index/index.css" />
    <style>
        .top-inptxt{
            /*width: 100%;*/
            height:1rem;
            background-color: #ebebeb;
            border: none;
        }
        .idxHead .Hsearbox {
            background-color: #ebebeb;
            margin: 0 auto;
            width: 80%;
        }
        .idxHead {
            margin: 0;
        }
    </style>
<?php $this->endBlock() ?>
    <header>

        <div class="Head">
            <a class="top-back" href="javascript:history.back(-1);"></a>
            <div class="Hcon">
                <div class="Head">
                    <div class="idxHead clearfix">
                        <div class="Hsearbox"><em></em>
                            <input type="text" value="" class="top-inptxt" placeholder="请输入关键词" />
                        </div>
                    </div>
                </div>
            </div>
            <a class="top-text" href="javascript:;" style="font-size: 0.89rem;">确定</a>
        </div>
  </header>

<div class="padt1">
      <div class="hotsearch">
          <h4>热门搜索</h4>
          <ul class="clearfix">
            <?php foreach ($data as $da) { ?>
                <li><a href="#"><?= $da['keyword'] ?></a></li>
            <?php } ?> 
          </ul>
      </div>
  </div>
<?php $this->
beginBlock('js') ?>
    <script type="text/javascript">
       $('.top-text').click(function(){
            var search = $('.top-inptxt').val();
            window.sessionStorage.seach = search;
            window.sessionStorage.type = 1;
            window.location = '/home/home/search-list';
       });
       $(document).on('click','.clearfix li a',function(){
            window.sessionStorage.seach = $(this).html();
            window.sessionStorage.type = 1;
            window.location = '/home/home/search-list';
       });
    </script>
<?php $this->
endBlock() ?>
<?php
use yii\helpers\Html;
$this ->title='游说';
?>
<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/base.css" />
<style type="text/css">
body{
    font-family: STHeiti,Microsoft YaHei,Helvetica,Arial,sans-serif!important; 
    background:#fff;
    line-height:1rem;
  }
header{position: fixed;top: 0;left: 0;right: 0;z-index: 101;background: #f7f7f7; border-bottom: 1px solid #ccc;height: 2.45rem;}
.head{margin: 0.4rem 0.55rem;line-height: 1.65rem;position: relative;}
.head .top-back{position: absolute;left: 0;top: 0;background: url(/images/top-back.png) no-repeat left center;background-size: 0.56rem auto;width: 1.65rem;height: 1.65rem;display: block;}
.head .hcon{margin: 0 1.8rem;position: relative;}
.head .hcon h1{width: 100%;text-align: center;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;color: #f47920;}
.menu .list{padding:0;margin-top: 0.56rem;background: #fff;}
.menu .img img{width: 100%;height: 210px;}
.menu .text{position: relative;padding: 0.56rem 0.56rem 0 0.56rem;}
.menu .text .my{position: absolute;right: 0.56rem;top: -0.56rem;width: 2.22rem;height: 2.22rem;border-radius: 50%;overflow: hidden;}
.menu .text .my img{width: 100%;height: 100%;}
.menu .text h1{font-size: 0.89rem;}
.menu .text p{color: #666;font-size: 0.78rem;height: 2.2rem;line-height: 1.1rem;padding: 0.3rem 0 0 0;text-overflow: -o-ellipsis-lastline;overflow: hidden;text-overflow: ellipsis;display: -webkit-box;-webkit-line-clamp: 2;-webkit-box-orient: vertical;border-bottom:1px solid #e6e6e6;}
.menu .text .texttip{line-height: 2.2rem;color: #999;margin-bottom:10px;}
.menu .text .texttip .can em{width: 0.89rem;height: 0.89rem;display: inline-block;background: url(/images/kan.png) no-repeat;background-size: 0.89rem auto;position: relative;top: 0.1rem;margin-right: 0.3rem;}
/*.ellipsis{overflow: hidden;
text-overflow:ellipsis;
white-space: nowrap;}*/
</style>
<?php $this->endBlock() ?>
<div class="page-content activity-wrap">
  <header>
     <div class="head">
        <a class="top-back" href="javascript:history.back(-1);"></a>
        <div class="hcon">
          <h1 style="font-size:18px;">游说</h1>
       </div>
       <a href="/home/home/search" style="width: 1rem;height: 1rem;background: url(/images/top-search.png) no-repeat center;position: absolute;right: 0;top: .4rem;background-size: 100% auto;"></a>
     </div>
  </header>
  <div class="scroll-wrap" id="scrollWrap" style="margin-top:2.45rem;">
        <div>
            <div id="loading" class="align-c"></div>
            <ul class="menu clearfix" id="menu">
            </ul>
            <div id="LoadMoreWrap"><div class="align-c color-gray2 f-12" id="LoadMore" style="display:none;">上滑加载更多</div></div>
        </div>
    </div> 
</div>
<script type="text/template" id="menu_tmpl">
    <% if(total > 0){ %>
    <% for (var i=0; i<list.length; i++) { %>
        <li class="list" data-id="<%= list[i].id %>">
            <div class="img">
                <img src="<%= list[i].photo %>" />
            </div>
            <div class="text">
                <div class="my"><img src="<%= list[i].headimg_url?list[i].headimg_url:list[i].name?'/images/myPhoto.png':'/images/vip.png' %>" /></div>
                    <h1 class=" ellipsis" style="width"><%= list[i].title %></h1>
                    <p class=""><%= list[i].content %></p>
                <div class="texttip clearfix">
                    <span class="can fd-left"><em></em><%= list[i].ll_sum %></span>
                    <span class="name fd-right ellipsis" style="text-align:right;"><%= list[i].name?list[i].name:'管理员' %></span>
                </div>
            </div>
        </li>
    <% } %>
    <% }else if(total==0){ %>
    <div class="align-c" style="padding-top:40px 0;">
        <img src="/images/no.png" width="108"/>
        <!-- <p class="f-12">还没有游说</p> -->
    </div>
    <% } %>
</script>
<?php $this->beginBlock('js') ?>
<script type="text/javascript">
         var api = 'lobby/lobby/';
        seajs.use('/modules/js/lobby/index',function(index){
            index.init();
        });
</script>
<?php $this->endBlock() ?>
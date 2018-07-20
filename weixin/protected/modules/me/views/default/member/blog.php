<?php
use yii\helpers\Html;
$this ->title='游说';
?>
<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/base.css" />
<link rel="stylesheet" href="/modules/css/index.css" />
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
.menu{position: relative;}
.menu .list{padding:0;margin-top: 0.56rem;}
.menu .img img{width: 100%;height: 210px;}
.menu .text{position: relative;padding: 0.56rem 0.56rem 0 0.56rem;}
.menu .text .my{position: absolute;right: 0.56rem;top: -0.56rem;width: 2.22rem;height: 2.22rem;border-radius: 50%;overflow: hidden;}
.menu .text .my img{width: 100%;height: 100%;}
.menu .text h1{font-size: 0.89rem;}
.menu .text p{color: #666;font-size: 0.78rem;height: 2.2rem;line-height: 1.1rem;padding: 0.3rem 0 0 0;text-overflow: -o-ellipsis-lastline;overflow: hidden;text-overflow: ellipsis;display: -webkit-box;-webkit-line-clamp: 2;-webkit-box-orient: vertical;}
.menu .text .texttip{line-height: 2.2rem;color: #999;border-bottom:1px solid #e6e6e6;margin-bottom:10px;}
.menu .text .texttip .can em{width: 0.89rem;height: 0.89rem;display: inline-block;background: url(/images/kan.png) no-repeat;background-size: 0.89rem auto;position: relative;top: 0.25rem;margin-right: 0.3rem;}
#add{width:50px;height:50px;position: absolute;bottom:20px;right:15px;background-color:rgba(114,114,114,0.6);z-index: 100;border-radius:50%;}
#add:after{content: "";display: block;border-left:4px solid #fff;height:26px;position: absolute;left:23px;top:12px;}
#add:before{content: "";display: block;border-top:4px solid #fff;width:26px;position: absolute;left:12px;top:23px;}
#menu .ellipsis{width:100%;}
.edit-icon{background:url(/images/edit.png) no-repeat center;background-size:1.3rem;padding:20px;}
.remove-icon{background:url(/images/delete.png) no-repeat center;background-size:1.3rem;padding:1.3rem;display:block;}
/*body{position: relative;}*/
/*.ellipsis{overflow: hidden;
text-overflow:ellipsis;
white-space: nowrap;}*/
</style>
<?php $this->endBlock() ?>
<div class="page-content activity-wrap">
    <div id="add"></div>
  <header>
     <div class="head">
        <a class="top-back" href="javascript:history.back(-1);"></a>
        <div class="hcon">
          <h1 style="font-size:18px;">游说</h1>

       </div>
       <a href="javascript:;" class="top-text2 reload" style="font-size: 0.88rem;right:2.6rem;display: none;">刷新</a>
       <a href="javascript:;" class="top-text J-delete" style="font-size: 0.7rem;">删除</a>
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
            <div class="img" style="position:relative;">
                <img src="<%= list[i].photo %>" />
                
                <div class="delete-ico" style="position:absolute;top:0px;left:0px;display:none;">
                <a href="javascript:;" class="remove-icon"></a>   
                </div>
            </div>
            <div class="text">
                <h1 class=" ellipsis" style="width"><%= list[i].title %></h1>
                <p class="" style="width:92%;"><%= list[i].content %></p>
                <div style="position:absolute;bottom:10px;right:0px;">
                <a href="/me/member/lobby-add?id=<%= list[i].id %>" class="edit-icon"></a>
                </div>
            </div>
        </li>
    <% } %>
    <% }else if(total==0){ %>
    <div class="align-c" style="padding-top: 40%;margin-top: 2.45rem;">
        <img src="/images/no.png" width="108"/>
        <!-- <p class="f-12">还没有游说</p> -->
    </div>
    <% } %>
</script>
<?php $this->beginBlock('js') ?>
<script type="text/javascript">
        var api = 'lobby/lobby/';
        seajs.use('/modules/js/me/blog',function(index){
            index.init();
        });
</script>
<?php $this->endBlock() ?>
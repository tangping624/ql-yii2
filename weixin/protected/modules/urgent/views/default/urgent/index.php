<?php
use yii\helpers\Html;
$this ->title='紧急';
?>
<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/base.css" />
<link rel="stylesheet" href="/modules/css/index/index.css" />
<style type="text/css">
  body{
    font-family: STHeiti,Microsoft YaHei,Helvetica,Arial,sans-serif!important; 
    background:#fff;
    line-height:1rem;
  }
  .menu{padding: 0 0.56rem;
    background: #fff;}
  .menu li .info{    padding: 0 0.8rem 0 9.5rem;
    /*background: url(/modules/css/images/arrowR.png) no-repeat right center;
    background-size: 1rem auto;*/}
  .menu .list{background: url(/images/arrowR.png) no-repeat right center;background-size: 0.7rem auto;} 
  .menu li .info h1{font-size: 0.78rem;
    margin: 0.2rem 0 0.8rem 0;word-break: break-all;
    text-overflow: ellipsis;
    display: -webkit-box; /** 对象作为伸缩盒子模型显示 **/
    -webkit-box-orient: vertical; /** 设置或检索伸缩盒对象的子元素的排列方式 **/
    -webkit-line-clamp: 2; /** 显示的行数 **/
    overflow: hidden;  /** 隐藏超出的内容 **/}
  .info .telnum .telicon {
    position:absolute;
    right:2rem;
    width: 1.33rem;
    height: 1.33rem;
    display: inline-block;
    background: url(/images/icon-call.png) no-repeat;
    background-size: 1.33rem auto;
    margin-left: .83rem;
  }
  .menu .info .telnum{font-size: .8rem;
    color: #666;
    line-height: 1.33rem;
}
  .menu li{padding: 0.56rem 0;border-bottom: 1px solid #e6e6e6;}
  .menu li .img{float: left;
    display: table;
    /*width: 8rem;
    height: 6rem;*/
    text-align: center;
}
.menu li .img em{    display: table-cell;width: 100%;vertical-align:middle;background-color: white;height:100%;}
.menu li .img em img{width:100%;max-width:9rem;max-height:6rem;min-height:6rem;overflow: hidden;}
</style>
<?php $this->endBlock() ?>

<!-- <script type="text/javascript" src="js/jquery-1.9.1.js"></script> -->

  <!--header S-->
<div class="page-content activity-wrap">
 <!--  <header>
     <div class="Head">
        <a class="top-back" href="javascript:history.back(-1);"></a>
        <div class="Hcon">
          <h1 style="font-size:18px;">紧急</h1>
       </div>
     </div>
  </header> -->
  <!--header E-->
  <div class="scroll-wrap" id="scrollWrap" style="">
        <div>
            <div id="loading" class="align-c"></div>
            <ul class="menu clearfix" id="menu">
            </ul>
            <div id="LoadMoreWrap" style="margin-top:10px;"><div class="align-c color-gray2 f-12" id="LoadMore" style="display:none;">上滑加载更多</div></div>
        </div>
    </div> 

   <!--<div class="padt1">
      <div class="qianz">
    	<ul>
        	<li class="clearfix">
            	<div class="img"><em><img src="" /></em></div>
                <div class="info">
                	<a href="Urgentdetail.html">
                    	<h1>国际医疗急救中心</h1>
                        <div class="telnum"><span class="fd-left">010-41154891</span><em class="telicon fd-left"></em></div>
                    </a>
                </div>
            </li>
            <li class="clearfix">
            	<div class="img"><em><img src="" /></em></div>
                <div class="info">
                	<a href="Urgentdetail.html">
                    	<h1>国际红十字会</h1>
                        <div class="telnum"><span class="fd-left">010-56451615</span><em class="telicon fd-left"></em></div>
                    </a>
                </div>
            </li>
            
        </ul>
    </div>
      
  </div>--> 
</div>
<script type="text/template" id="menu_tmpl">
    <% if(list.length > 0){ %>
    <% for (var i=0; i<list.length; i++) { %>
    <li class="clearfix list" data-id="<%= list[i].id%>">
        <div class="img"><em><img src="<%= list[i].logo%>" /></em></div>
            <div class="info">
            <a href="javascript:;">
                <h1 style=""><%= list[i].title%></h1>
                <div class="telnum">
                    <span class="fd-left" style="width: 75%;word-wrap: break-word;"><%= list[i].tel%></span>
                    <em class="telicon fd-left"></em>
                </div>
            </a>
        </div>
    </li>
    <% } %>
    <% }else if(total==0){ %>
    <div class="align-c" style="padding-top:40px;">
        <img src="/images/no.png" width="108"/>
        <!-- <p class="f-12">还没有相关商品</p> -->
    </div>
    <% } %>
</script>
<?php $this->beginBlock('js') ?>
<script type="text/javascript">
         var api = 'urgent/urgent/';
        seajs.use('/modules/js/urgent/index',function(index){
            index.init();
        });
</script>
<?php $this->endBlock() ?>
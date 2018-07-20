<?php
use yii\helpers\Html;
$this ->title='';
?>
<?php $this->beginBlock('css') ?>
    <link rel="stylesheet" href="/modules/css/index/index.css" />
    <link rel="stylesheet" href="/modules/css/base.css" />
    <link rel="stylesheet" href="/modules/css/demo.css" />
    <!-- <link rel="stylesheet" href="/modules/css/default.css" /> -->
    <link rel="stylesheet" href="/modules/css/osSlider.css" />
    <style type="text/css">
    .idxHead{margin:0;}
    .idxHead .Hsearbox{background-color: #ebebeb;margin:0 auto;width:80%;}
    #seach{background-color: #ebebeb;border: none;height: 1.62rem;width: 88%;padding: 0 3%;border-radius: .1rem;font-size: .78rem;color: #666;}
    .idxHead .Hsearbox em{background: url(/images/top-search.png) no-repeat center;background-size: 1rem 1rem;}
    .cur span{color:#f47920;}
    .menu .product{padding: 0 0.5%;float: left;width: 49%;}
    .product:nth-child(2n-1){padding: 0.56rem 0.5%;}
    .product:nth-child(2n){padding: 0.56rem 0.5%;}
    /*.product:nth-child(1),.prolist1 li:nth-child(2){padding: 0 0.5%;}*/
    .menu .product .img1{width: 100%;float: none;text-align: center;}
    .menu .product img{width:100%;height:6.5rem;background-color: #ebebeb;}
    .menu .product .info{text-align: center;padding: 0.5rem 0;min-height: 0;width: 100%;margin: 0;}
    .menu .product .info h3{font-size: .78rem;line-height: 1rem;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;}
    .prolist1 li .imgs img{width: 100%;height: 210px;}
    .prolist1{padding:0;}
    .prolist1 .li{padding: .56rem;}
    .prolist1 li{padding:0;}
    .menu .text{position: relative;padding: 0.56rem 0.56rem 0 0.56rem;}
    .menu .text .my{position: absolute;right: 0.56rem;top: -0.56rem;width: 2.22rem;height: 2.22rem;border-radius: 50%;overflow: hidden;}
    .menu .text .my img{width: 100%;height: auto;}
    .menu .text h1{font-size: 0.89rem;}
    .menu .text p{color: #666;font-size: 0.78rem;height: 2.2rem;line-height: 1.1rem;padding: 0.3rem 0 0 0;text-overflow: -o-ellipsis-lastline;overflow: hidden;text-overflow: ellipsis;display: -webkit-box;-webkit-line-clamp: 2;-webkit-box-orient: vertical;}
    .menu .text .texttip{line-height: 2.2rem;color: #999;}
    .ellipsis2{ width:90%;overflow: hidden;text-overflow: ellipsis;white-space: normal;display: -webkit-box; -webkit-line-clamp: 2;-webkit-box-orient: vertical;}
    .Head {
        margin: 0.4rem 0.55rem;
        line-height: 1.65rem;
        position: relative;
    }
    #seach{

    }
        .flex_div span{    font-size: .78rem;}
    </style>
<?php $this->endBlock() ?>
<div class="page page-index page-fiexed clearfix" id="page">
    <header class="bg-fff">
        <div class="Head">
            <a class="top-back" href="javascript:history.back(-1);"></a>
            <div class="Hcon">
                <div class="Head">
                    <div class="idxHead clearfix">
                        <div class="Hsearbox"><em></em><input type="text" name="seach" id="seach" placeholder="请输入关键词"></div>
                    </div>
                </div>
            </div>
        </div>

    </header>
    <div class="padt21 clearfix">
        <div class="rankbox" style="">
            <ul class="flex">
                <li class="flex_div cur" data-type="1">
                    <span>产品</span>
                </li>
                <li class="flex_div" data-type="2">
                    <span>商家</span>
                </li>
                <li class="flex_div" data-type="3">
                    <span>文章</span>
                </li>
            </ul>
        </div>
    </div>
    <div class="scroll-wrap" id="scrollWrap" style="margin-top: 4.45rem;">
        <div>
            <div id="loading" class="align-c"></div>
            <ul class="prolist1 menu clearfix" id="menu">
            </ul>
            <div id="LoadMoreWrap"><div class="align-c color-gray2 f-12" id="LoadMore" style="display:none;">上滑加载更多</div></div>
        </div>
    </div>
</div>
<!-- 产品 -->
<script type="text/template" id="product_tmpl">
    <% if(list.length > 0){ %>
    <% for (var i=0; i<list.length; i++) { %>
        <li class="product lists" data-appcode="<%= list[i].app_code%>" data-id="<%= list[i].id%>" data-sellerid="<%= list[i].seller_id%>">
            <div class="img1 fn" class=""><span><img src="<%= list[i].logo%>" /></span></div>
            <div class="info" style="float: none">
                <h3><%= list[i].name%></h3>
            </div>
        </li>
    <% } %>
    <% if(list.length%2!=0){%>
    <li class="product">
        <a href="javascript:;" class="clearfix">
            <div class="img fn" class=""><img src="" alt="" style="height: 6.5rem;visibility: hidden"><span></span></div>
            <div class="info">
                <h3 style="visibility: hidden">''</h3>
            </div>
        </a>
    </li>
    <% }%>
    <% }else if(list.length==0){ %>
    <div class="align-c" style="padding-top:190px;">
        <img src="/images/no.png" width="108"/>
        <!-- <p class="f-12">还没有游说</p> -->
    </div>
    <% } %>
</script>
<!-- 商家 -->
<script type="text/template" id="shop_tmpl">
    <% if(list.length > 0){ %>
    <% for (var i=0; i<list.length; i++) { %>
        <li class="clearfix li lists" data-appcode="<%= list[i].app_code%>" data-id="<%= list[i].id%>">
            <a href="javascript:;" data-id="<%= list[i].id%>">
                <div class="img" style="width: 6.5rem"><span><img src="<%= list[i].logo%>" style="width: 6.5rem;"/></span></div>
                <div class="info" style="width: 53%">
                    <h3 class="ellipsis"><%= list[i].name%></h3>
                      <p class="label" style="margin-top:0.5rem;">
                        <i class="bg-blue"><%=list[i].nametype%></i>
                      </p>
                    <p class="agent ellipsis2" style="margin-top: 0.4rem"><%= list[i].summary%></p>
                </div>
            </a>
        </li>
    <% } %>
    <% }else if(list.length==0){ %>
    <div class="align-c" style="padding-top:190px;">
        <img src="/images/no.png" width="108"/>
        <!-- <p class="f-12">还没有游说</p> -->
    </div>
    <% } %>
</script>
<!-- 文章 -->
<script type="text/template" id="text_tmpl">
    <% if(list.length > 0){ %>
    <% for (var i=0; i<list.length; i++) { %>
        <li class="lists" data-id="<%= list[i].id%>">
            <div class="imgs">
                <img src="<%= list[i].photo %>" />
            </div>
            <div class="text">
                <div class="my"><img src="<%= list[i].headimg_url?list[i].headimg_url:list[i].name?'/images/myPhoto.png':'/images/vip.png' %>" /></div>
                    <h1 class=" ellipsis" style="width"><%= list[i].title %></h1>
                    <p class=""><%= list[i].content %></p>
                <div class="texttip clearfix">
                    <span class="can fd-left"><em></em><%= list[i].ll_sum %></span>
                    <span class="name fd-right"><%= list[i].name?list[i].name:'管理员' %></span>
                </div>
            </div>
        </li>
    <% } %>
    <% }else if(list.length==0){ %>
    <div class="align-c" style="padding-top:190px;">
        <img src="/images/no.png" width="108"/>
        <!-- <p class="f-12">还没有游说</p> -->
    </div>
    <% } %>
</script>
<?php $this->
beginBlock('js') ?>
    <script type="text/javascript">
        seajs.use('/modules/js/search/index',function(index){
            index.init();
        })
    </script>
    <!--    <script src="js/portamento.js"></script>-->
<?php $this->
endBlock() ?>
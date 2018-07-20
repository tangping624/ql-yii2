<?php
use yii\helpers\Html;
$this ->title='附近';
?>
<?php $this->
beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/index/index.css" />
<link rel="stylesheet" href="/modules/css/base.css" />
<style type="text/css">
    .loaction_text{line-height: 1rem;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;width: 94%;
    display: inherit;}
    .prolist1 .menu_list .info p{text-overflow: -o-ellipsis-lastline;overflow: hidden;
text-overflow: ellipsis;display: -webkit-box;-webkit-line-clamp: 2;-webkit-box-orient: vertical;}

</style>
<?php $this->
endBlock() ?>
<div class="page page-index page-fiexed clearfix" id="page">
    <header>
        <div class="Head">
            <a class="top-back" href="javascript:history.back(-1);"></a>
            <div class="Hcon">
                <h1 style="font-size: 18px;">附近</h1>
            </div>
<!---->
        </div>
    </header>
    <!--header E-->
    <div class="padt2 padb1" style="padding-top: 0;margin-top:2.45rem;">
        <div class="rankbox" style="top:2.45rem;">
            <ul class="flex">
                <li class="flex_div cur" data-type="39de1187-4da4-ea7a-56cd-0e2f79400083">
                    <a href="javascript:;">
                        <span>旅游</span> <i></i>
                    </a>
                </li>
                <li class="flex_div" data-type="39de1187-4da4-ea7a-56cd-0e2f79400080">
                    <a href="javascript:;">
                        <span>美食</span> <i></i>
                    </a>
                </li>
                <li class="flex_div" data-type="39de1187-4da4-ea7a-56cd-0e2f79400079">
                    <a href="javascript:;">
                        <span>购物</span>
                        <i></i>
                    </a>
                </li>
            </ul>
            <div class="loaction"><em class="reast"></em>
                <span class="loaction_text">当前：定位中...</span>
            </div>
        </div>
        <div class="scroll-wrap idxRecomme" id="scrollWrap" style="margin-top:6.45rem;">
            <div>
                <div id="loading" class="align-c"></div>
                <ul class="prolist1 clearfix" id="menu" style="margin-bottom: 40px;">
                    
                </ul>
                <div id="LoadMoreWrap">
                    <div class="align-c color-gray2 f-12" id="LoadMore" style="display:none;">上滑加载更多</div>
                </div>
            </div>
        </div>
    </div>
    <?php require './modules/inc/menu.php';?></div>
    <script type="text/template" id="menu_tmpl">
    <% if(list.length > 0){ %>
    <% for (var i=0; i<list.length; i++) { %>
        <li class="menu_list" data-id="<%= list[i].id %>">
            <a href="javascript:;" class="clearfix">
                <div class="img">
                    <span>
                        <img src="<%= list[i].logo %>" />
                    </span>
                </div>
                <div class="info">
                    <h3><%= list[i].name %></h3>
                    <div class="about clearfix">
                        <i class="bg-blue"></i>
                    </div>
                    <p>
                        <%= list[i].summary %>
                    </p>
                </div>
                <div class="distance" style="background: none"><%= Number(list[i].dis).toFixed(1) %>km</div>
            </a>
        </li>
    <% } %>
    <% }else if(list.length==0){ %>
    <div class="align-c" style="padding-top:40%;" >
        <img src="/images/no.png" width="120"/>
    </div>
    <% } %>
</script>
<?php $this->
beginBlock('js') ?>
 <script type="text/javascript" src="https://3gimg.qq.com/lightmap/components/geolocation/geolocation.min.js"></script>
<script type="text/javascript">
window.WxJSSDKSign='<?=json_encode($wxjsdk)?>';
    seajs.use('/modules/js/nearby/index',function(index){
        index.init();
    });
</script>

<?php $this->
endBlock() ?>
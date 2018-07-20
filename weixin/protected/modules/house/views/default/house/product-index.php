<?php
use yii\helpers\Html;
$this ->
title='产品列表';
?>
<header>
    <div class="Head">
        <a class="top-back" href="javascript:history.back(-1);"></a>
        <div class="Hcon">
            <h1>产品列表</h1>
        </div>
    </div>
</header>
<div class="idxAd1"  id="picScroll2" >
    <div class="hd" style="display:none;"><ul></ul></div>
    <div class="bd">
        <ul>
            <li><a href="/Shopintro.html"><img src="ImgUpload/idxad11.jpg" /></a></li>
            <li><a href="/Catedetail.html"><img src="ImgUpload/idxad12.jpg" /></a></li>
        </ul>
        <ul>
            <li><a href="/Shopindex.html"><img src="ImgUpload/idxad13.jpg" /></a></li>
            <li><a href="/Shopindex.html"><img src="ImgUpload/idxad14.jpg" /></a></li>
        </ul>

    </div>
</div>
<script type="text/javascript">
    TouchSlide({
        slideCell:"#picScroll2",
        titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
        autoPage:true, //自动分页

    });
</script>
<!--header E-->
<div class="padt21">
    <div class="mart1">
        <ul class="prolist1 Abg">
            <li>
                <a href="Productdetail.html">
                    <div class="img"><span><img src="ImgUpload/yy1.jpg" /></span></div>
                    <div class="info">
                        <h3 >海湾公寓</h3>
                        <p><i class="bg-green">公寓</i></p>
                        <div class="agent">特色：采光好，室内精装修，环境优雅。</div>
                    </div>
                </a>
            </li>
            <li>
                <a href="Productdetail.html">
                    <div class="img"><span><img src="ImgUpload/yy2.jpg" /></span></div>
                    <div class="info">
                        <h3 >阳澄湖别墅</h3>
                        <p><i class="bg-green">别墅</i></p>
                        <div class="agent">特色：采光好，室内精装修，环境优雅。</div>
                    </div>
                </a>
            </li>
            <li>
                <a href="Productdetail.html">
                    <div class="img"><span><img src="ImgUpload/yy3.jpg" /></span></div>
                    <div class="info">
                        <h3 >美丽之家</h3>
                        <p><i class="bg-green">租房</i></p>
                        <div class="agent">特色：采光好，室内精装修，环境优雅。</div>
                    </div>
                </a>
            </li>
            <li>
                <a href="Productdetail.html">
                    <div class="img"><span><img src="ImgUpload/yy4.jpg" /></span></div>
                    <div class="info">
                        <h3 >巴黎花园</h3>
                        <p><i class="bg-green">地块</i></p>
                        <div class="agent">特色：采光好，室内精装修，环境优雅。</div>
                    </div>
                </a>
            </li>
            <li>
                <a href="Productdetail.html">
                    <div class="img"><span><img src="ImgUpload/yy5.jpg" /></span></div>
                    <div class="info">
                        <h3 >魅力之城出租</h3>
                        <p><i class="bg-green">中介</i></p>
                        <div class="agent">项目：导游,机票,酒店,行程定制 </div>
                    </div>
                </a>
            </li>
        </ul>
    </div>

</div>

<?php
use yii\helpers\Html;
$this ->
title='游说详细';
?>
<?php $this->
beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/base.css" />
<link rel="stylesheet" href="/modules/css/details.css" />
<style type="text/css">
body{background:#fff;}
header{position: fixed;top: 0;left: 0;right: 0;z-index: 101;background: #f7f7f7; border-bottom: 1px solid #ccc;height: 2.45rem;}
.head{margin: 0.4rem 0.55rem;line-height: 1.65rem;position: relative;}
.head .top-back{position: absolute;left: 0;top: 0;background: url(/images/top-back.png) no-repeat left center;background-size: 0.56rem auto;width: 1.65rem;height: 1.65rem;display: block;}
.head .hcon{margin: 0 1.8rem;position: relative;}
.head .hcon h1{width: 100%;text-align: center;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;color: #f47920;}
.padt1{background: #fff;}
.padt1 .g_detail{padding:0;}
.padt1 .g_detail .img img{width: 100%;height: 210px;}
.padt1 .g_detail .box{padding: 0 0.56rem;background: #fff;}
.padt1 .g_detail .box .myid{line-height: 2.22rem;margin-top: -2rem;position: relative;z-index: 2;}
.padt1 .g_detail .box .myid .my{float: left;width: 2.22rem;height: 2.22rem;border-radius: 50%;overflow: hidden;}
.padt1 .g_detail .box .myid .my img{width: 100%;height: 100%;vertical-align: inherit;}
.padt1 .g_detail .box .myid p{font-size: 0.67rem;color: #fff;padding-left: 2.6rem;}
.padt1 .g_detail .box .text{padding: 0.5rem 0;}
.padt1 .g_detail .box .text .tit{text-align: center;}
.padt1 .g_detail .box .text .tit h1{font-size: 0.89rem;}
.padt1 .g_detail .box .text .tit .tip{color: #999;font-size: 0.67rem;margin-top: 0.56rem;}
.padt1 .g_detail .box .text .tit .tip span{margin-right: 0.5rem;}
.padt1 .g_detail .box .text .con{margin-top: 0.56rem;text-indent: 1.5rem;font-size: 0.78rem;line-height: 1.1rem;color: #666;}
.padt1 .g_detail .box .text .con img{display: block}
#page_content{background-color: #fff;}
</style>

<?php $this->
endBlock() ?>

<input type="hidden" id="cookie" value="<?= Html::encode($cookie)?>">
<input type="hidden" id="shopid" value="<?= Html::encode($details['id'])?>">
<header>
    <div class="head">
        <a class="top-back" href="javascript:history.back(-1);"></a>
        <div class="hcon">
            <h1 style="font-size:18px;">游说详情</h1>
        </div>
        <a class="icon dz <?= $details['prise']?'yi-dz':'wei-dz'?>" href="javascript:;"></a>
        <a class="icon sc <?= $details['collection']?'yi-sc':'wei-sc'?>" href="javascript:;"></a>
        <!-- <a href="/home/home/search" style="width: 1rem;height: 1rem;background: url(/images/top-search.png) no-repeat center;position: absolute;right: 0;top: .4rem;background-size: 100% auto;"></a> -->
    </div>
</header>
<!--header E-->
<div class="padt1" style="padding-top:0px;margin-top:2.45rem;">
    <div class="g_detail">
        <div class="img">
            <img src="<?= $details['photo'] ?>"/>
        </div>
        <div class="box">
        <?php $head=$details['headimg_url'];?>
            <div class="myid clearfix">
                <div class="my">
                <?php if($head){?> 
                    <img src="<?= $head ?>" />
               
                <?php }else if(!$details['headimg_url']&&$details['source']=='2'){?>
                    <img src="/images/myPhoto.png" />
                <?php }else{?>
                    <img src="/images/vip.png" />
                <?php } ?>
                </div>
                <p><?= $details['name']?$details['name']:'管理员' ?></p>
            </div>
            <div class="text">
                <div class="tit">
                    <h1 style="line-height:1.5;"><?= $details['title'] ?></h1>
                    <div class="tip">
                        <span></span>
                        <span><?= explode(' ',$details['created_on'])[0]?></span>
                    </div>
                </div>
                <div class="con">
                    <?= $details['content'] ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->
beginBlock('js') ?>
    <script>
    seajs.use('/mobiend/js/mod/app',function(login){
            var id=$('#shopid').val();
            var isLogin=$('#cookie').val();
            //    点赞
            $('body').on('click','.dz',function(){
                var isDz=$(this).attr('class').indexOf('wei-dz')==-1?true:false;
                var me=$(this);
                if(!isLogin){
                    $.Env.showMsg('亲，您尚未登录哦');
                    setTimeout("location.href='/me/me/login-index'",1000);
                }else{
                    if(!isDz){//没有点赞

                        $.ajaxEx({
                            url:'/lobby/lobby/praise?id='+id+'&type=2',
                            data:'',
                            success:function(data){
                                if (!data.result) {
                                    $.Env.showMsg(data.msg);
                                } else {
                                    me.removeClass('wei-dz').addClass('yi-dz');
                                    //$('.dzNum').html(parseInt($('.dzNum').html())+1)
                                    $.Env.showMsg('已点赞');
                                }
                            },
                            error:function(){
                                $.Env.showMsg('网络连接不可用,请稍后重试');
                            }

                        })
                    } else{
                        $.Env.showMsg('已经点过点赞了哦');
                    }
                }
            })
            // 收藏
            $('body').on('click','.sc',function(){
                var me=$(this);
                isSc=$(this).attr('class').indexOf('wei-sc')!=-1?false:true;
                if(!isLogin){
                    $.Env.showMsg('亲，您尚未登录哦');
                    setTimeout("location.href='/me/me/login-index'",1000);
                }else {
                    if (!isSc) {//没有收藏
                        $.ajaxEx({
                            url: '/lobby/lobby/praise?id=' + id + '&type=1',
                            data: '',
                            success: function (data) {
                                if (!data.result) {
                                    $.Env.showMsg(data.msg);
                                } else {
                                    me.removeClass('wei-sc').addClass('yi-sc');
                                    $.Env.showMsg('收藏成功');
                                }
                            },
                            error: function () {
                                $.Env.showMsg('网络连接不可用,请稍后重试');
                            }

                        })
                    } else {
                        $.ajaxEx({
                            url: '/lobby/lobby/cancel?id=' + id + '&type=1',
                            data: '',
                            success: function (data) {
                                if (!data.result) {
                                    $.Env.showMsg(data.msg);
                                } else {
                                    me.removeClass('yi-sc').addClass('wei-sc');
                                    $.Env.showMsg('已取消收藏');
                                }
                            },
                            error: function () {
                                $.Env.showMsg('网络连接不可用,请稍后重试');
                            }

                        })
                    }
                }
            })
        });
    </script>
<?php $this->
endBlock() ?>
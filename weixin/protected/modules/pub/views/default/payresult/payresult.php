 <!DOCTYPE html>
<html lang="zh-cn">
    <head>
        <title>支付提示</title>
        <?php require './mobiend/inc/meta.html'; ?>
        <?php require './mobiend/inc/global.html'; ?> 
    <style type="text/css">
        body{background-color:#eee;}
        .msg{padding-top:50px; text-align:center;}
        .msg .pay-i{margin-bottom:20px;}
        .msg .pay-result{margin-bottom:26px;font-size:20px;}
        .btn-box { margin-bottom:30px;  text-align:center;padding:0 10px;}
        .btn-box a{
            text-align: center; display:inline-block;
            padding:0px 10px;
            height:38px;line-height: 35px;font-size:16px;
            background:#eee;box-sizing:border-box;
        }
        .btn-box a.on{color:#f83b64;border:2px solid #f83b64;border-radius:12px;}
    </style> 
    <div id="page_content" class="page-content">
        <div class="msg" >
            <p class="pay-i"><img src="/modules/css/img/<?= $ok ? 'ok' : 'fail' ?>.png" width="140" class=""/></p>
            <p class="pay-result"><?= $ok ? '支付成功！' : '支付失败！' ?></p>
        </div>
        <div class="btn-box clearfix">
            <a class="on" href="/shop/shoph/index?public_id=<?= $public_id ?>&openid=<?= $openid ?>" target="_self">回到商城首页</a>
            <a class="on" href="/shop/order/order-list" target="_self">查看所有订单</a>
        </div>
    </div>
        <?php require './mobiend/inc/mix.html'; ?>
        <?php require './modules/inc/copyright.php'; ?>
    </body>
</html>
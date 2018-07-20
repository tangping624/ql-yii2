<!DOCTYPE html>
<html>
<head><meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" /><title>
	微信支付
</title>

    <script language="javascript" type="text/javascript">
        <?php if($success) {?>
        // 当微信内置浏览器完成内部初始化后会触发WeixinJSBridgeReady事件。
        document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {            
            WeixinJSBridge.invoke('getBrandWCPayRequest', 
            <?= $jsApiParameters ?>,
            function(res) {
                if (res) {
                    window.location.href = decodeURIComponent(<?=  json_encode($ret_url)?>) + "&pay_result=" + res.err_msg;
                } else {
                    window.location.href = decodeURIComponent(<?=  json_encode($ret_url)?>) + "&pay_result=getBrandWCPayRequestError";
                }
            });
        }, false);
        <?php } else { ?>
            <?php if (empty($msg)) { ?>
            alert('商户支付配置有误');
            <?php } else { ?>
            alert(<?=  json_encode($msg)?>);
            <?php } ?>
            window.location.href = decodeURIComponent(<?=  json_encode($ret_url)?>) + "&pay_result=getBrandWCPayRequestError";
        <?php }?>
    </script>

</head>
<body>
   <div>
        <img src="/modules/images/global/pay_bg.png" alt="" style="margin: 0 auto; width:50%; position: fixed; left:50%; top:50%; margin:-78px 0 0 -99px"/>
    </div>
</body>
</html>

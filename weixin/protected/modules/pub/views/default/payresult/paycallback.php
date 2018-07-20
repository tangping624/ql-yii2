<!DOCTYPE html>
<?php use app\framework\utils\WebUtility; ?>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <title>支付提示</title>
</head>

<body>
<script type="text/javascript">
    setTimeout(function() {
        // 防止用户的后退，产生重复订单
        location.href = "<?= WebUtility::createUrl('pub/payresult/firm', ['order_no'=>$order_no,'public_id' =>$public_id,'memberid'=>$memberid]) ?>";
    }, 10);
</script>
</body>
</html>
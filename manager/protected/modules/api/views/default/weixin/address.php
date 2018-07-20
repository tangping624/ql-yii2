<!DOCTYPE html>
<html>
<head><meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" /><title>
	我的地址
</title>

    <script language="javascript" type="text/javascript">
        <?php if($success) {?>
        // 当微信内置浏览器完成内部初始化后会触发WeixinJSBridgeReady事件。
        document.addEventListener('WeixinJSBridgeReady', function () {
            WeixinJSBridge.invoke('editAddress', 
            <?= $jsApiParameters ?>,
            function(res) {
                if (res) {
                    var queryStr = '';
                    if (res.err_msg == 'edit_address:ok') {
                        queryStr = '&consignee='+res.userName
                                    +'&phone='+res.telNumber
                                    +'&province='+res.proviceFirstStageName
                                    +'&city='+res.addressCitySecondStageName
                                    +'&county='+res.addressCountiesThirdStageName
                                    +'&detail='+res.addressDetailInfo
                                    +'&zipcode='+res.addressPostalCode;
                        
                        window.location.href = decodeURIComponent(<?=  json_encode($ret_url)?>) + queryStr;
                    } else if (res.err_msg == 'edit_address:cancel' || res.err_msg == 'edit_address:fail') {
                        //queryStr += '&gobackMark=editCancelGotoGoodsDetail';
                        return history.back(); //失败或取消时改为js跳转
                    } else if (res.err_msg == 'editAddress:fail_no permission to execute') {
                        return location.reload(); //没有权限时尝试刷新当前页面
                    } else{
                        alert(res.err_msg);
                        return history.back(); //失败或取消时改为js跳转
                    }                  
                } else {
                    window.location.href = decodeURIComponent(<?=  json_encode($ret_url)?>) + queryStr;
                }
            });
        }, false);
        <?php } else { ?>
            alert(<?=  json_encode($msg)?>);      
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

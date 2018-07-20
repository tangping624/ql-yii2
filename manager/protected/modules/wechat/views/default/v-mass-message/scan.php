<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <meta name="msapplication-tap-highlight" content="no" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>群发消息安全认证</title>
    <?php require './mobiend/inc/global.html';?>
    <link rel="stylesheet" href="/modules/css/v-wechat/global.min.css?v=5bcd0e0daa" />
</head>
<body>
    <div id="page_content">
        <div class="page">
            <div class="page-content">
                <div class="align-c" style="margin-top: 40px;">
                    <img src="/modules/images/message/success.png" width="140"/>
                </div>

                <?php
                if ($data['type']=='admin_scan') {
                    ?>
                    <div class="login-btn align-c p10">
                        <div class="f-16 c3 mb15" id="title"><?=$data['title']?></div>
                        <div class="f-14 c9 mb15" id="desc"><?=$data['subject']?></div>
                        <a href="javascript:;" class="btn mb15" target="_self" id="admin_confirm">确 定</a>
                        <a href="javascript:;" class="btn sub-btn" target="_self" id="closeWindow">关 闭</a>
                    </div>
                <?php
                } elseif ($data['type']=='admin_auth') {
                    ?>
                    <div class="login-btn align-c p10">
                        <div class="f-16 c3 mb15" id="title"><?=$data['title']?></div>
                        <div class="f-14 c9 mb15 pl10 " id="desc"><?=$data['subject']?></div>
                        <a href="javascript:;" class="btn mb15" target="_self" id="admin_confirm_apply">同意操作</a>
                        <a href="javascript:;" class="btn sub-btn" target="_self" id="admin_refuse_apply">拒 绝</a>
                    </div>
                <?php
                } elseif ($data['type']=='member_scan') {
                    ?>
                    <div class="login-btn align-c p10">
                        <div class="f-16 c3 mb15" id="title"><?=$data['title']?></div>
                        <div class="f-14 c9 mb15" id="desc"><?=$data['subject']?></div>
                        <a href="javascript:;" class="btn mb15" target="_self" id="sendtoamdin">确 定</a>
                        <a href="javascript:;" class="btn sub-btn" target="_self" id="closeWindow">关 闭</a>
                    </div>
                <?php
                } else {
                    ?>
                    <div class="login-btn align-c p10">
                        <div class="f-16 c3 mb15"><?=$data['title']?></div>
                        <div class="f-14 c9 mb15"><?=$data['subject']?></div>
                    </div>
                <?php
                }?>
            </div>
        </div>
    </div>
    
    <?php require './mobiend/inc/mix.html';?>
    <?php require './modules/inc/copyright.php';?>
    
    <script type="text/javascript">
        var WxJSSDKSign = '<?= json_encode($wx)?>';
        
        // **G_VERSIONINFO_START**
        var G_VERSIONINFO = [
            "modules/js/v-wechat/v-mass-message/auth"
        ];
        // **G_VERSIONINFO_END**

        seajs.updateRes(function(auth) {
            auth.init();
        }, true, $.seajsDebug);
</script>
</body>
</html>
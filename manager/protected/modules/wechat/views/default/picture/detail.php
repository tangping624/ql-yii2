<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <meta name="msapplication-tap-highlight" content="no"/>
    <title><?= $data['title'] ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        #page_content {
            position: relative;
            padding: 20px 15px 15px;
            background-color: #fff;
            font-family: 'Helvetica Neue', Helvetica, 'Hiragino Sans GB', 'Microsoft YaHei', Arial, sans-serif;
        }

        #page_content h2 {
            margin-bottom: 10px;
            line-height: 1.4;
            font-weight: 400;
            font-size: 24px;
        }

        #page_content .desc {
            margin-bottom: 18px;
            line-height: 20px;
            font-size: 0;
        }

        #page_content .content * {
            max-width: 100%;
        }

        #page_content .desc .text {
            font-size: 16px;
            color: #8c8c8c;
            margin-right: 8px;
            margin-bottom: 10px;
        }

        #page_content .desc a.text {
            color: #607fa6;
            text-decoration: none;
        }
    </style>
    <?php require './mobiend/inc/global.html'; ?>
</head>
<body>
<div id="page_content">
    <h2><?= $data['title'] ?></h2>

    <div class="desc">
        <span class="text"><?= $data['modified_on'] ?></span>
        <a class="text" href="<?= $data['attention_url'] ?>"><?= $data['account_name'] ?></a>
    </div>
    <div class="content">
        <?= $data['is_cover_showin_body'] == 1 ? '<img src="' . $data['cover_url'] . '" />' : '' ?>
        <?= $data['body'] ?>
    </div>
</div>
<?php require './mobiend/inc/mix.html'; ?>
<?php require './modules/inc/copyright.php'; ?>
<script type="text/javascript">
    var WxJSSDKSign = '<?= json_encode($wx)?>';
    var detail_id = '<?=$data['id']?>';
    var detail_title = '<?=$data['title']?>';
    var detail_cover = '<?=$data['cover_url']?>';
    var shareropenid = '<?=$sharer['shareropenid']?>';
    var sharerfanid = '<?=$sharer['sharerfanid']?>';
    var sharermemberid = '<?=$sharer['sharermemberid']?>';
    // **G_VERSIONINFO_START**
    var G_VERSIONINFO = [
        "modules/js/wechat/material-detail/detail"
    ];
    // **G_VERSIONINFO_END**

    seajs.updateRes(function (detail) {
        detail.init();
    }, true, $.seajsDebug);
</script>
</body>
</html>
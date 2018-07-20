<?php
/**
 * Created by PhpStorm.
 * User: weizs
 * Date: 2015/6/15
 * Time: 17:31
 */
    $request=Yii::$app->request;
    $accountId=$request->get('account');
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="prefix" content="<?= Yii::$app->response->getHeaders()->get('prefix') ?>">
    <title>选择语音</title>
    <link href="/frontend/css/bootstrap/dist/css/bootstrap.min.css?v=b547efe9b3" rel="stylesheet">
    <link rel="stylesheet" href="/modules/css/wechat/material/voice.min.css?v=f7cd193c66"/>
    <style type="text/css">
        body{background-color: #fff;}
        .data-content{margin-right: 0;}
        .m-item{cursor:pointer;position: relative;}
        .appmsg_mask{display: none;position: absolute;top: 0;left: 0;width: 100%;height: 100%; background-color: #000;filter: alpha(opacity = 60);-moz-opacity: .6;-khtml-opacity: .6;opacity: .6;z-index: 1;}
        .icon_card_selected {background: url("/frontend/js/widgets/weixinEdition/images/base_z.png") 0 -6085px no-repeat;width: 46px; height: 46px;vertical-align: middle;display: inline-block;}
        .m-item .icon_card_selected {display: none;position: absolute;top: 50%;left: 50%;margin-top: -23px;margin-left: -23px;line-height: 999em;overflow: hidden;z-index: 1;}
        .m-item.selected .icon_card_selected {display: inline-block;}
        .m-item.selected .appmsg_mask {display: block;}
    </style>
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/frontend/js/lib/compatible.js"></script>
    <![endif]-->
</head>
<body>
    <div class="art-box">
        <div class="art-box-content manage-content" style="padding:15px 30px;">
            <input type="hidden" name="account_id" id="account_id"  value="<?=$accountId?>"/>
            <div class="upload-area clearfix">
                <a href="javascript:;" class="btn btn-primary pull-left" id="upload_btn">上传</a>
                <div class="tips">大小: 不超过5M,    长度: 不超过60s,    格式: mp3, wma, wav, amr</div>
                <div class="process"></div>
            </div>
            <div class="data-content clearfix" id="data_content" style="height:400px;overflow: auto;"></div>
        </div>
        <div class="art-box-footer">
            <button type="button" class="btn btn-primary" id="select_audio">确定</button>
            <button type="button" class="btn btn-secondary" id="cancel_select">取消</button>
        </div>
    </div>

    <script type="text/template" id="item_template">
        <div class="m-item" data-id="<%- id %>" data-index="<%- i %>" >
            <div class="m-content">
                <div class="m-play">
                    <a href="javascript:;" class="play-btn">
                        <span class="audio-icon"></span>
                    </a>
                </div>
                <span class="m-title"><%- name %></span>
            </div>
            <div class="appmsg_mask"></div>
            <i class="icon_card_selected">已选择</i>
        </div>
    </script>
    <script type="text/template" id="upload_process_template">
        <div class="process-state">
            <div class="process-tips">正在保存</div>
            <div class="wrap">
                <div class="file-info">
                    <div class="file-name"><%- file_name%></div>
                    <div class="file-size">(<%- file_size%>)</div>
                </div>
                <div class="bar-wrap">
                    <div class="bar"></div>
                </div>
                <a href="javascript:;" class="cancel">取消</a>
            </div>
        </div>
    </script>
    <script type="text/javascript" src="/frontend/js/lib/global.js"></script>
    <script type="text/javascript" src="/frontend/3rd/plupload/plupload.full.min.js"></script>
    <script type="text/javascript">var audioDataCache=null;</script>
<!--    <script type="text/javascript" src="/modules/js/wechat/material/voice.js" flag="build"></script>-->
    <script type="text/javascript">
        seajs.use('/modules/js/wechat/material/voice.js',function(){
            $('#data_content').on('click','.m-item',function(){
                $('#data_content .m-item').removeClass('selected');
                $(this).addClass('selected');
            });

            $('#cancel_select').on('click',function(){
                top.selectAudioBox.close();
            });

            $('#select_audio').on('click',function(){
                top.selectAudioBox.close();
                var index = $('#data_content .m-item.selected').attr('data-index');
                var audioData = audioDataCache[index];
                top.selectAudioBox.onSelect(audioData);
            });
        });
    </script>
</body>
</html>
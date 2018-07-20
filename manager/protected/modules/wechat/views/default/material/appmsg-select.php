<?php
    $request=Yii::$app->request;
    $accountId=$request->get('account');
    $view=$request->get('view', 'card');
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="prefix" content="<?= Yii::$app->response->getHeaders()->get('prefix') ?>">
    <link rel="icon" href="http://oss-cn-hangzhou.aliyuncs.com/yunshequ-new/predefine/yun-she-qu.ico" otype="image/x-icon"/>
    <link rel="shortcut icon" href="http://oss-cn-hangzhou.aliyuncs.com/yunshequ-new/predefine/yun-she-qu.ico" type="image/x-icon"/> 
    <title>选择素材</title>
    <link href="/frontend/css/bootstrap/dist/css/bootstrap.min.css?v=b547efe9b3" rel="stylesheet">
    <link rel="stylesheet" href="/modules/css/wechat/material/index.min.css?v=cccd164cbd"/>
    <style type="text/css">
        body{background-color: #fff;}
        .search-wrap{border-bottom: 1px solid #E7E7EB;padding-bottom: 15px!important;}
        #new_msg{margin-top:20px;}
        .msg-list{height:412px;overflow: auto;}
        .msg-list .msg-list-inner{padding:15px 30px!important;}
        .page-row{padding:10px 30px!important;}
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
        <div class="art-box`-content manage-content" style="padding:0;">
            <input type="hidden" name="account_id" id="account_id"  value="<?=$accountId?>"/>
            <div class="padding search-wrap">
                <div class="clearfix">
                    <div class="search-bar pull-left js-searchcon" style="width:300px;margin-top:20px;">
                        <input type="text" class="search-input" placeholder="标题/作者/摘要" id="search_input">
                        <span class="x-icon x-icon-clear" id="x_clear" style="display: inline-block;">×</span>
                        <span class="search-btn search-icon"></span>
                    </div>
                    <a href="javascript:;" class="btn btn-primary pull-right" id="new_msg">新建图文消息</a>
                </div>
            </div>
            <div class="view-type" style="display: none;">
                <a href="javascript:;" class="card-view on" data-view="card" title="卡片式">卡片式</a>
            </div>
            <div class="appmsg-list clearfix">
                <div class="msg-list card">
                    <div class="padding msg-list-inner">
                        <div class="msg-col">
                            <div class="col-inner" id="msg_list_1"></div>
                        </div>
                        <div class="msg-col">
                            <div class="col-inner" id="msg_list_2"></div>
                        </div>
                        <div class="msg-col">
                            <div class="col-inner" id="msg_list_3"></div>
                        </div>
                    </div>
                </div>
                <div class="padding page-row clearfix" id="page_row" style="visibility: hidden;">
                    <div class="page-box">
                        <span class="page-nav-area">
                            <a href="javascript:;" class="btn-pr page-prev hidden">
                                <i class="arrow"></i>
                            </a>
                            <span class="page-num"></span>
                            <a href="javascript:;" class="btn-pr page-next hidden">
                                <i class="arrow"></i>
                            </a>
                        </span>
                        <span class="goto-area">
                            <input type="text" class="inp">
                            <a href="javascript:;" class="btn-pr bg-white page-go">跳转</a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="art-box-footer">
            <button type="button" class="btn btn-primary" id="select_appmsg">确定</button>
            <button type="button" class="btn btn-secondary" id="cancel_select">取消</button>
        </div>
    </div>

    <script type="text/template" id="msgTpl">
        <div class="m-item<%- (articles=articles||[]).length>1?' multi':'' %>" data-index="<%- i %>" data-id="<%- id %>" data-mediaid="<%- media_id %>">
            <div class="m-content-wrap">
                <% for(var idx=0;idx < articles.length;idx++){ %>
                <div class="m-content">
                    <h4><a href="javascript:;"><%- articles[idx]['title'] %></a></h4>
                    <% if(!idx){ %><div class="m-date"><%- modified_on||'' %></div><% } %>
                    <div class="m-cover">
                        <img src="<%- articles[idx]['cover_url'] %>" />
                    </div>
                    <% if(!idx){ %><p class="m-desc"><%- articles[idx]['summary']||'' %></p><% } %>
                </div>
                <% }%>
            </div>
            <div class="appmsg_mask"></div>
            <i class="icon_card_selected">已选择</i>
        </div>
    </script>
    <script type="text/javascript" src="/frontend/js/lib/global.js"></script>
    <script type="text/javascript">var appmsgDataCache=null;</script>
<!--    <script type="text/javascript" src="/modules/js/wechat/material/index.js" flag="build"></script>-->
    <script type="text/javascript">
        seajs.use('/modules/js/wechat/material/index.js',function(){
            $('.msg-list').on('click','.m-item',function(){
                $('.msg-list .m-item').removeClass('selected');
                $(this).addClass('selected');
            });

            $('#cancel_select').on('click',function(){
                top.selectAppmsgBox.close();
            });

            $('#select_appmsg').on('click',function(){
                top.selectAppmsgBox.close();
                var index = $('.msg-list .m-item.selected').attr('data-index');
                var appmsgData = appmsgDataCache[index];
                top.selectAppmsgBox.onSelect(appmsgData);
            })
        });

    </script>
</body>
</html>
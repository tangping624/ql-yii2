<?php
    $this->title = Yii::$app->params['system_name'];
?>

<?php $this->beginBlock('css') ?>
<link rel="stylesheet" type="text/css" href="/frontend/js/widgets/weixinEdition/css/msg_sender.css?v=a0ad0a19f5" />
<link rel="stylesheet" type="text/css" href="/modules/css/wechat/autoreply/autoreply.min.css?v=61e3abfce7" />
<?php $this->endBlock() ?>

<div class="manage-content">
    <div class="padding mb30 border-bottom">
        <h4 class="manage-title">自动回复</h4>
         <input type="hidden" id="account_id" value="<?=$data?>"/> 
    </div>

    <div class="padding">
        <ul class="reply-tabs clearfix" id="reply_type" data-type="welcome">
            <li><a href="<?= $this->context->createUrl('/wechat/auto-reply/index?type=welcome&public_id='.$data)?>">被添加自动回复</a></li>
            <li><a href="<?= $this->context->createUrl('/wechat/auto-reply/index?type=autoreply&public_id='.$data)?>">消息自动回复</a></li>
            <li><a href="<?= $this->context->createUrl('/wechat/auto-reply/kf-keyword?public_id='.$data)?>">自动转客服关键字</a></li>
            <li class="on no-extra"><a href="<?= $this->context->createUrl('/wechat/auto-reply/keyword?public_id='.$data)?>">关键词自动回复</a></li>
        </ul>
    </div>
    
    <div class="keyword-wrap padding" id="keyword_wrap">
        <a href="javascript:;" class="btn btn-primary js-addrule" style="margin-bottom:15px;"><span class="glyphicon glyphicon-plus"></span>添加规则</a>
        <div class="keyword_rules"></div>
    </div>
</div>

<script type="text/template" id="keywordrule_tmpl">
    <div class="panel panel-default panel-reset panel-rule <% if(dropdown_closed){ %>dropdown_closed<% }else{ %>dropdown_opened<% } %>">
        <div class="panel-heading clearfix">
            <% if(name){ %>规则：<%-name%><% }else{ %><%-ruleTitle%><% } %>
            <a href="javascript:void(0);" class="icon_dropdown_switch pull-right"><i class="arrow arrow_up"></i><i class="arrow arrow_down"></i></a>
        </div>
        <div class="rule-view">
            <div class="keywords clearfix">
                <label>关键词</label>
                <ul class="label-content">
                    <% for(var i=0; i<keywords.length; i++){ %>
                        <li><%=keywords[i]['showkeyword']%></li>
                    <% } %>
                </ul>
            </div>
            <div class="replys clearfix">
                <label>回复</label>
                <div class="label-content"><%-reply_type%></div>
            </div>
        </div>
        <div class="rule-edit">
            <div class="rule-name-wrap clearfix">
                <label><span class="icon-point"></span>规则名</label>
                <div class="rule-name form">
                    <input type="text" class="form-control rulename-input" value="<%- name%>" maxlength="60">
                    <p class="color-gray">规则名最多60个字</p>
                </div>
            </div>
            
            <div class="rule-keyword-wrap">
                <div class="rule-keyword-title clearfix">
                    <label><span class="icon-point"></span>关键字</label>
                    <a href="javascript:;" class="pull-right add-keyword">添加关键字</a>
                </div>
                <ul class="rule-keyword-list">
                    <% for(var j=0; j<keywords.length; j++){ %>
                        <li class="clearfix"><span class="keyword-name"><%=keywords[j]['showkeyword']%></span>
                            <div class="pull-right">
                                <a href="javascript:;" class="align-m match-link" data-index="<%-j%>"><% if(keywords[j]['is_exact']){ %>已全匹配<% }else{ %>未全匹配<% } %></a>&nbsp;&nbsp;&nbsp;&nbsp;
                                <span class="icon-merge icon-edit" data-index="<%-j%>"></span>&nbsp;&nbsp;&nbsp;
                                <span class="icon-merge icon-delete" data-index="<%-j%>" style="vertical-align:top"></span>
                            </div>
                        </li>
                    <% } %>
                </ul>
            </div>
    
            <div class="rule-reply-wrap">
                <div class="rule-reply-title">
                    <label><span class="icon-point"></span>回复</label>
                </div>
                <div class="weixinEdition"></div>
            </div>
    
            <div class="rule-btns align-r">
                <button type="button" class="btn btn-primary js-saverule">保存</button>&nbsp;
                <button type="button" class="btn btn-secondary js-delete-rule">删除</button>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="addkeyword_templ">
    <div class="add-keyword-wrap">
        <div class="art-box-content">
            <div class="js-wxeditor"></div>
        </div>
        <div class="art-box-footer">
            <button type="button" class="btn btn-primary" id="submit_addkeyword">确认</button>
            <button type="button" class="btn btn-secondary" id="cancel_addkeyword">取消</button>
        </div>
    </div>
</script>

<?php $this->beginBlock('js') ?>
<script type="text/javascript" src="/modules/build/script/script-a3bfc31062.js?v=d3a6e81e66" data-build></script>
<script type="text/javascript">
    var __SCRIPT = [
        '/frontend/3rd/plupload/plupload.full.min.js'
    ];
    __REQUIRE('/modules/js/wechat/auto-reply/keyword.js?v=9e04e569d1');
</script>
<?php $this->endBlock() ?>
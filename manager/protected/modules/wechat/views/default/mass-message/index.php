<?php
    $this->title = Yii::$app->params['system_name'];
?>

<?php $this->beginBlock('css') ?>
<link rel="stylesheet" type="text/css" href="/frontend/js/widgets/weixinEdition/css/msg_sender.css?v=a0ad0a19f5" />
<link rel="stylesheet" type="text/css" href="/modules/css/wechat/groupsend/groupsend.min.css?v=c0e3fbda42" />
<?php $this->endBlock() ?>

<div class="manage-content">
    <div class="padding">
        <h4 class="manage-title">群发功能</h4>
        <input type="hidden" id="account_id" value="<?=$accountId?>"/> 
    </div>
    
    <ul class="tab-nav mb30">
        <li class="on">
            <span>新建群发消息</span>
        </li>
        <li>
            <a href="<?= $this->context->createUrl('/wechat/mass-message/list')?>">已发送</a>
        </li>
    </ul>
            
    <div class="padding">
        <div class="form" style="margin-bottom: 20px;">
            <div class="clearfix">
                <label for="account_id" class="pull-left" style="margin:5px 10px 0 0;">群发对象</label>
                <select class="form-control invisible pull-left" name="msg_target" id="msg_target" style="width:200px;">
                    <option value="粉丝">全部</option>
                    <option value="会员">会员</option> 
                </select>
            </div>
            <div class="select-level select-wrap" style="margin-top: 10px;display: none;" id="level_select">
                <label class="pull-left" style="margin:5px 10px 0 0;">会员等级</label>
                <div class="pull-overflow" style="padding-top: 6px;">
                    <label class="form-checkbox selected checkbox-all" data-type="all">
                        <i class="icon-checkbox"></i>
                        <span class="align-m">全部</span>
                        <input type="checkbox" class="form-checkbox-input" checked="checked">
                    </label>
                    <?php
                    for ($i=0; $i<count($level_data); $i++) {?>
                        <label class="form-checkbox form-item-checkbox selected">
                            <i class="icon-checkbox"></i>
                            <span class="align-m"><?=$level_data[$i]['name']?></span>
                            <input type="checkbox" class="form-checkbox-input" value="<?=$level_data[$i]['id']?>" data-name="<?=$level_data[$i]['name']?>" checked="checked">
                        </label>
                        <?php
                    }?>
                </div>
            </div>
          
        </div>
    <div id="js_msgSender"></div>
        <div style="margin-top: 20px;">
            <?php
            if ($approver=='') { ?>
                <p style="color:#f00;font-size:12px;margin-bottom: 10px;">请先设置群发消息管理员，然后提交审批</p>
            <?php
            }?>
            <a href="javascript:;" class="btn btn-primary<?=$approver==''?' btn-disable':'' ?>" id="send_msg">提交审批</a>&nbsp;&nbsp;
            <a href="javascript:;" class="btn btn-secondary" id="preview">预览</a>
        </div>
    </div>
</div>

<script type="text/template" id="preview_templ">
    <div class="art-box-content">
        <div class="preview-wrap" style="padding:40px 0 40px 40px;">
            <p>关注公众号后，才能接收图文消息预览</p>
            <div class="preview-input">
                <input type="tel" placeholder="请输入会员手机号码" id="mobile" maxlength="11" />
                <p class="tips-info f12" style="margin-top:5px;"></p>
                <div id="store_mobile" class="store-mobile clearfix">
                    <?php
                    if(isset($preview_history)){
                        foreach ($preview_history as $item) {
                            ?>
                            <div class="mobile-item" data-mobile="<?=$item['mobile']?>"><?=$item['name'].'&nbsp;'.$item['mobile']?><!--<span class="close">×</span>--></div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="art-box-footer">
        <button type="button" class="btn btn-primary js-okbtn" id="submit_preview">确定</button>
        <button type="button" class="btn btn-secondary js-cancelbtn">取消</button>
    </div>

</script>

<script type="text/template" id="masscheck_templ">
    <div class="art-box-content">
        <img src="<%=codeurl%>" width="250" />
        <p style="font-size:12px;color:#999;margin-top:10px;" id="codr_tips">请扫描二维码，若您不是群发消息管理员，会向管理员发送申请，该申请30分钟内有效。</p>
    </div>
</script>

<?php $this->beginBlock('js') ?>
<script type="text/javascript" src="/modules/build/script/script-5254b67e46.js?v=d3a6e81e66" data-build></script>
<script type="text/javascript">
    var currAccountId = '<?=$accountId?>';
    var approver = '<?=$approver?>';
</script>
<script type="text/javascript">
    var __SCRIPT = [
        '/frontend/3rd/plupload/plupload.full.min.js'
    ];
    __REQUIRE('/modules/js/wechat/mass-message/index.js?v=c696a6d122');
</script>
<?php $this->endBlock() ?>
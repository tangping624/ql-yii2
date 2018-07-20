<?php
use app\models\Organization;

$this->title = Yii::$app->params['system_name'];
    $id = \Yii::$app->request->get('id');
?>

<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/wechat/account/config.min.css?v=53813a6c8a" />
<?php $this->endBlock() ?>

<div class="manage-content">
    <h4 class="padding manage-title">公众号配置</h4>
    <ul class="tab-nav mb30">
        <li class="on"><span>商户信息</span></li>
        <li><a href="<?= $this->context->createUrl("wechat/msg-forward-setting/index", ['id'=>$id]) ?>">第三方消息广播</a></li>
    </ul>
    <div class="padding">
        <div class="breadcrumbs">
            <a href="<?= $this->context->createUrl("wechat/account/index") ?>" class="icon-merge icon-goback" title="返回上一层">返回上一层</a>
            <a href="<?= $this->context->createUrl("wechat/account/index") ?>" class="parent">公众号管理</a> /
            <span>公众号配置</span>
        </div>
    </div>
    <div class="padding form" id="config_form">
        <div class="clearfix account-info" style="padding: 0 15px;width: 650px;">
            <div class="form-group clearfix">
                <label class="col-md-2">所属组织</label>
                <div class="col-md-10 gray"><?=$data['corp_name']?></div>
            </div>
            <div class="form-group clearfix">
                <label class="col-md-2">公众号名称</label>
                <div class="col-md-10 gray"><?=$data['name']?></div>
            </div>
            <div class="form-group clearfix">
                <label class="col-md-2">微信号</label>
                <div class="col-md-10 gray"><?=$data['wechat_number']?></div>
            </div>
            <div class="form-group clearfix">
                <label class="col-md-2">微信类型</label>
                <div class="col-md-10 gray"><?=$data['type']?></div>
            </div>
            <div class="form-group clearfix">
                <label class="col-md-2">AppID</label>
                <div class="col-md-10 gray"><?=$data['app_id']?></div>
            </div>
        </div>
        <div class="panel panel-default panel-reset">
            <div class="panel-heading">商户信息</div>
            <table class="table">
                <tr>
                    <td width="100">商户号</td>
                    <td>
                        <span class="text"><?=$data['mch_id']?></span>
                        <div class="edit hide">
                            <input type="text" class="form-control form-text-control" value="<?=$data['mch_id']?>" data-value="<?=$data['mch_id']?>" data-column="mch_id">
                        </div>
                    </td>
                    <td width="100"><a href="javascript:;" class="edit-btn">修改</a></td>
                </tr>
                <tr>
                    <td>商户密钥</td>
                    <td>
                        <span class="text"><?=$data['mch_key']?></span>
                        <div class="edit hide">
                            <input type="text" class="form-control form-text-control" value="<?=$data['mch_key']?>" data-value="<?=$data['mch_key']?>" data-column="mch_key">
                        </div>
                    </td>
                    <td><a href="javascript:;" class="edit-btn">修改</a></td>
                </tr>
                <tr>
                    <td>支付证书</td>
                    <td>
                        <span class="text1"><?=$data['is_import_ssl_cert']?'已导入':'未导入'?></span>
                    </td>
                    <td><a href="javascript:;" class="edit-btn" id="import_cert">导入</a></td>
                </tr>
                <tr>
                    <td>支付密钥</td>
                    <td>
                        <span class="text1"><?=$data['is_import_ssl_key']?'已导入':'未导入'?></span>
                    </td>
                    <td><a href="javascript:;" class="edit-btn" id="import_key">导入</a></td>
                </tr>
            </table>
        </div>
        
    </div>
</div>

<?php $this->beginBlock('js') ?>
<script type="text/javascript" src="/modules/build/script/script-8aabcc534c.js?v=d3a6e81e66" data-build></script>
<script type="text/javascript">
    var __SCRIPT = [
        '/frontend/3rd/plupload/plupload.full.min.js'
    ];
    __REQUIRE('/modules/js/wechat/account/config.js?v=7d9146253b');
</script>
<?php $this->endBlock() ?>
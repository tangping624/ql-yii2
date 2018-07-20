<?php
$this->title = Yii::$app->params['system_name'];
$id = \Yii::$app->request->get('id');
?>

<?php $this->beginBlock('css') ?>
    <link rel="stylesheet" href="/modules/css/wechat/account/config.min.css?v=53813a6c8a"/>
<?php $this->endBlock() ?>

    <div class="manage-content">
        <h4 class="padding manage-title">公众号配置</h4>
        <ul class="tab-nav mb30">
            <li><a href="<?= $this->context->createUrl("wechat/account/config", ['id' => $id]) ?>">商户信息</a></li>
            <li class="on"><span>第三方消息广播</span></li>
        </ul>
        <div class="padding">
            <div class="breadcrumbs">
                <a href="<?= $this->context->createUrl("wechat/account/index") ?>" class="icon-merge icon-goback"
                   title="返回上一层">返回上一层</a>
                <a href="<?= $this->context->createUrl("wechat/account/index") ?>" class="parent">公众号管理</a> /
                <span>公众号配置</span>
            </div>
        </div>

        <div class="padding grid">
            <div class="grid-toolbar">
                <div class="grid-btns clearfix">
                    <button href="javascript:;" class="btn btn-primary pull-right" id="add_btn">
                        <span class="glyphicon glyphicon-plus"></span>
                        添加
                    </button>
                </div>
            </div>

            <div class="grid-content clearfix" id="config_grid">
                <table class="table">
                    <thead>
                    <tr>
                        <th width="150">第三方名称</th>
                        <th width="300">URL</th>
                        <th width="300">Token</th>
                        <th width="100">操作</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <script type="text/template" id="row_template">
        <td><%-partner_name%></td>
        <td><%-url%></td>
        <td><%-token%></td>
        <td>
            <a href="javascript:;" class="modify" data-id="<%-_id%>">编辑</a>
            <a href="javascript:;" class="del">删除</a>
        </td>
    </script>

    <script type="text/template" id="edit_template">
        <form class="form form-base form-horizontal" id="edit_form">
            <div class="art-box-content">
                <div class="form-group">
                    <label for="partner_name" class="col-md-2">第三方名称</label>

                    <div class="col-md-10">
                        <input type="text" class="form-control" id="partner_name" name="partner_name" value="<%-partner_name||''%>"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="url" class="col-md-2">URL</label>

                    <div class="col-md-10">
                        <input type="text" class="form-control" id="url" name="url" value="<%-url||''%>"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="token" class="col-md-2">Token</label>

                    <div class="col-md-10 form-inline">
                        <input type="text" class="form-control col-md-10" id="token" name="token" value="<%-token||''%>"/>
                        <button type="button" class="btn btn-secondary pull-right col-md-2" id="random_token">随机生成</button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="key" class="col-md-2">EncodingAESKey</label>

                    <div class="col-md-10 form-inline">
                        <input type="text" class="form-control col-md-10" id="secret_key" name="secret_key" value="<%-secret_key||''%>"/>
                        <button type="button" class="btn btn-secondary pull-right col-md-2" id="random_key">随机生成</button>
                    </div>
                </div>
                <input type="hidden" id="id" name="id" value="<%-id||''%>">
                <input type="hidden" id="account_id" name="account_id" value="<%-account_id||''%>">
            </div>
            <div class="art-box-footer">
                <button type="button" class="btn btn-primary" id="submit_btn">确定</button>
                <button type="button" class="btn btn-secondary">取消</button>
            </div>
        </form>
    </script>

<?php $this->beginBlock('js') ?>
    <script type="text/javascript">
        __REQUIRE('/modules/js/wechat/msg-forward-setting/index.js?v=e4ccc6c765');
    </script>
<?php $this->endBlock() ?>
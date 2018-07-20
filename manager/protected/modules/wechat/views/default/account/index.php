<?php
$this->title = Yii::$app->params['system_name'];
?>
<?php $this->beginBlock('css') ?>
    <link rel="stylesheet" href="/modules/css/wechat/account/index.min.css?v=1b753905de"/>
<?php $this->endBlock() ?>

    <div class="manage-content">
        <div class="padding mb30 border-bottom">
            <h4 class="manage-title">公众号管理</h4>
        </div>
        <div class="padding">
            <div class="grid">
                <div class="grid-toolbar">
                    <div class="grid-btns clearfix">
                        <div class="pull-right">
                            <a href="<?= $this->context->createUrl('/wechat/account/wechat-auth') ?>" class="btn btn-primary">添加公众号</a>
                        </div>
                    </div>

                </div>

                <div class="grid-content" id="grid">
                    <table class="table form">
                        <thead>
                        <tr>
                            <th width="60">序号</th>
                            <th>公众号</th>
                            <th width="100">头像</th>
                            <th width="200">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (count($data)>0) {
                            for ($i = 0; $i < count($data); $i++) {
                                $row = $data[$i];
                                $auth_url = $this->context->createUrl('/wechat/account/wechat-auth', ['id' => $row['id']]);
                                ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><span><?= $row['name'] ?></span><?=$row['is_authed']?"":"<span class=\"is-cancel\">公众号已取消对云服务的授权&nbsp;<a href=\"$auth_url\">马上授权</a></span>"?></td>
                                    <td><img src="<?= $row['headimg_url'] ?>" width="80" height="80"></td>
                                    <td>
                                        <a href="<?= $auth_url ?>">重新授权</a>
                                        <a href="<?= $this->context->createUrl('/wechat/account/config', ['id' => $row['id']]) ?>">公众号配置</a>
                                        <a href="javascript:;" class="del" data-id="<?= $row['id'] ?>">删除</a>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td class="empty-td align-c" colspan="4">暂无数据</td>
                            </tr>
                            <?php
                        }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $this->beginBlock('js') ?>
    <script type="text/javascript">
        __REQUIRE('/modules/js/wechat/account/index.js?v=fd120974d9');
    </script>
<?php $this->endBlock() ?>
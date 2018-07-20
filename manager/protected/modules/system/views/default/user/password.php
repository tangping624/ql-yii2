<?php
/**
 * Created by PhpStorm.
 * User: lvq
 * Date: 2015/6/8
 * Time: 11:05
 */
$this->title = Yii::$app->params['system_name'];
$user = $data['user_info'];
?>

<?php $this->beginBlock('css') ?>
    <link href="/modules/css/system/user/password.min.css?v=eec80c8f65" rel="stylesheet">
<?php $this->endBlock() ?>


<div class="manage-content">
    <h4 class="manage-title padding mb30 border-bottom">用户信息</h4>

    <div class="padding user-info">
        <div class="title"><?= $user['name'] ?></div>
        <div class="sub-title"><?= $user['account'] ?></div>
        <div class="detail-info">
            <div class="clearfix">
                <div class="col-md-1">手机</div>
                <div class="col-md-11"><?= $user['mobile'] ?></div>
            </div>
            <div class="clearfix">
                <div class="col-md-1">邮箱</div>
                <div class="col-md-11"><?= $user['email']?:'' ?></div>
            </div>
            <div class="clearfix">
                <div class="col-md-1">密码</div>
                <div class="col-md-11"><a href="javascript:;" id="modify_pwd">修改密码</a></div>
            </div>
        </div>
    </div>
</div>

<script type="text/template" id="password">
    <form class="form-align form form-base form-horizontal password_form" id="password_form">
        <div class="padding form-group-area">
            <div class="form-group">
                <label class="col-md-2" for="old_password">当前密码</label>
                <div class="col-md-10">
                    <input type="password" class="form-control" id="old_password" name="old_password"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2" for="new_password">新密码</label>
                <div class="col-md-10">
                    <input type="password" class="form-control" id="new_password" name="new_password"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2" for="confirm_password">确认密码</label>
                <div class="col-md-10">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password"/>
                </div>
            </div>
        </div>
        <div class="align-c clearfix btn-area">
            <button type="button" id="save_password" class="btn btn-primary mr14">确定</button>
            <button type="button"  id="cancel_password" class="btn btn-secondary">取消</button>
        </div>
    </form>
</script>

<?php $this->beginBlock('js') ?>
<script type="text/javascript">
    __REQUIRE('/modules/js/system/user/password.js?v=55073f9c79');
</script>
<?php $this->endBlock() ?>
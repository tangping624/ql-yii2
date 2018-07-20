<?php
use yii\helpers\Html;

$this->title = Yii::$app->params['system_name'];
?>
<?php $this->beginBlock('css') ?>
    <!--页面样式代码-->
    <link href="/modules/css/global/public.css" rel="stylesheet">
    <!--<link href="/modules/css/page/tree.css" rel="stylesheet">-->
    <link href="/modules/css/page/popup.css" rel="stylesheet">
     
<?php $this->endBlock() ?>
    <div class="popup-container">
        <form id="user_form" class="form form-base form-horizontal">
            <div class="popup-content">
                <input id="id" type="hidden" name="id" value="<?= $user->id; ?>" readonly="readonly"/>

                <div class="form-area">
                    <div class="form-item">
                        <label class="form-field">姓名</label>

                        <div class="form-tag-wrap">
                            <input value="<?= Html::encode($user->name); ?>" type="text" class="form-control" id="name"
                                   name="name"/>
                        </div>
                    </div>
                    <div class="form-item">
                        <label class="form-field">帐号</label>

                        <div class="form-tag-wrap">
                            <input value="<?= Html::encode($user->account); ?>" type="text" class="form-control"
                                   placeholder="" id="account" name="account">
                        </div>
                    </div>
                    <div class="form-item"> 
                        <label class="form-field">手机</label>
                        <div class="form-tag-wrap">
                            <input type="text" value="<?= Html::encode($user->mobile); ?>"
                                   class="form-control ime-disabled"
                                   placeholder="" id="mobile" name="mobile" onpaste="return false;"
                                   onKeypress="return (/[\d.]/.test(String.fromCharCode(event.keyCode)))"
                                   onkeyup="value=value.replace(/[^\d]/g,'')">
                        </div>
                    </div> 
                    <div class="form-item"> 
                        <label class="form-field">
                            <span class="ff-text">邮箱</span>
                            <span class="ff-mark">(选填)</span>
                        </label>
                        <div class="form-tag-wrap">
                            <input value="<?= Html::encode($user->email); ?>" type="text" class="form-control"
                                   placeholder="" id="email" name="email">
                        </div>
                    </div> 
                </div>
            </div>
            <div class="form-bottom align-c">
                <button type="button" class="btn-pr ok-btn" id="submit_btn">确定</button>
                <button type="button" class="btn-pr sub-btn" id="cancel_btn">取消</button>
            </div>
        </form>
    </div>
<?php $this->beginBlock('js') ?>
    <!--页面js代码-->
    <script type="text/javascript">
        $(function () {
            seajs.use('/modules/js/system/user/userinfo.js', function (user) {
                //js加载完毕初始化
                user.init();
            });
        });
    </script>
<?php $this->endBlock() ?>
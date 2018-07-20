<?php

$this->title = Yii::$app->params['system_name'];
?>
<?php $this->beginBlock('css') ?>
    <!--页面样式代码-->
    <link href="/modules/css/global/public.css" rel="stylesheet">
    <link href="/modules/css/page/popup.css" rel="stylesheet">
<?php $this->endBlock() ?>
    <div class="popup-container">
        <form id="user_form" class="form form-base">
            <div class="popup-content">
                <div class="form-area">
                    <div class="form-item">
                        <label class="form-field">密码</label>

                        <div class="form-tag-wrap">
                            <input type="text" class="form-control" id="password" name="password"
                                   onkeyup="value=value.replace(/[^\w\.\/]/ig,'')"/>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="form-bottom align-c">
            <button type="button" class="btn-pr ok-btn" id="submit_btn">确定</button>
            <button type="reset" class="btn-pr sub-btn" id="cancel">取消</button>
        </div>
    </div>
<?php $this->beginBlock('js') ?>
    <script type="text/javascript">
        seajs.use('/modules/js/missions/officers/editPwd.js', function (editPwd) {
            editPwd.init();
        });
    </script>
<?php $this->endBlock() ?>
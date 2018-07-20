<?php
/**
 * Created by PhpStorm.
 * User: kongy
 * Date: 2015/4/15
 * Time: 10:18
 */
use \yii\helpers\Html;

?>
<?php $this->beginBlock('css') ?>
<!--页面样式代码-->
<link href="/modules/css/global/public.css" rel="stylesheet">
<link href="/modules/css/page/popup.css" rel="stylesheet">
<?php $this->endBlock() ?>
<div class="popup-container add-contractor-popup">
    <form id="contractor_form" class="form form-base">
        <div class="popup-content has-padding">
            <div class="form-area">
                <div class="form-item">
                    <label class="form-field">密码</label>

                    <div class="form-tag-wrap">
                        <input type="password" class="form-control" id="password"
                               name="password"  maxlength="30"/>
                    </div>
                </div> 
            </div>
        </div>
        <div class="form-bottom align-c">
            <button type="button" class="btn-pr ok-btn" id="submit_btn">确定</button>
            <button type="button" class="btn-pr cancel-btn" id="cancel_btn">取消</button>
        </div>
    </form>
</div>
<?php $this->beginBlock('js') ?>
<!--页面js代码-->
<script type="text/javascript">
    $(function () {
        seajs.use('/modules/js/system/user/chgpassword.js', function (chgpassword) {
            //js加载完毕初始化
            var id = "<?= $id ?>"; 
            chgpassword.init(id);
        });
    });
</script>

<?php $this->endBlock() ?>

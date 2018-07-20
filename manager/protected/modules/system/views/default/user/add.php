<?php 
use \yii\helpers\Html;  
$this->title = \Yii::$app->params['system_name'];
?>
<?php $this->beginBlock('css') ?>
    <!--页面样式代码-->
    <link href="/modules/css/global/public.css" rel="stylesheet"> 
    <link href="/modules/css/page/popup.css" rel="stylesheet"> 
    <style>
        .form-error{color: #e15f63 !important;}
    </style>
<?php $this->endBlock() ?>
    <div class="popup-container">
        <form id="user_form" class="form form-base">
            <div class="popup-content">
                <div class="form-area">
                    <div class="form-item">
                        <label class="form-field">姓名</label> 
                        <div class="form-tag-wrap">
                            <input type="text" class="form-control" id="name" name="name"/>
                        </div>
                    </div>
                    <div class="form-item">
                        <label class="form-field">帐号</label>

                        <div class="form-tag-wrap">
                            <input type="text" class="form-control" id="account" name="account"/>
                        </div>
                    </div>
                    <div class="form-item">
                        <label class="form-field">
                            <span class="ff-text">手机</span> 
                        </label> 
                        <div class="form-tag-wrap">
                            <input type="text" class="form-control" id="mobile" name="mobile" onpaste="return false;"
                                   onKeypress="return (/[\d.]/.test(String.fromCharCode(event.keyCode)))"
                                   onkeyup="value=value.replace(/[^\d]/g,'')"/>
                        </div>
                    </div> 
                    <div class="form-item">
                      <label class="form-field">
                            <span class="ff-text">邮箱</span> 
                            <span class="ff-mark">(选填)</span>
                        </label>  
                        <div class="form-tag-wrap">
                            <input type="text" class="form-control" id="email" name="email"/>
                        </div>
                    </div>  
                </div>
            </div>
            <div class="form-bottom align-c">
                <button type="button" class="btn-pr ok-btn" id="submit_new_btn">保存并添加下一个</button>
                <button type="button" class="btn-pr sub-btn" id="submit_btn">保存</button>
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
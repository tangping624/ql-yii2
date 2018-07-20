<?php
use yii\helpers\Html;
$this ->title='修改登录密码';
?>
<?php $this->beginBlock('css') ?>
    <link rel="stylesheet" href="/modules/css/index/index.css" />
    <link rel="stylesheet" href="/modules/css/base.css" />
<?php $this->endBlock() ?>
  <header>
     <div class="Head">
        <a class="top-back" href="javascript:history.back(-1);"></a>
        <div class="Hcon">
          <h1 style="font-size: 0.98rem;">修改登录密码</h1>
       </div>
       <a href="javascript:;" class="top-text" style="font-size: 0.88rem;">确定</a>
     </div>
  </header>
  <!--header E-->
  <div class="padt1">
     <div class="formbox">
        <ul class="clearfix" id="login_form">
           <li><input type="password" name="oPwd" placeholder="输入原始密码" /></li>
           <li><input type="password" name="nPwd" placeholder="输入新密码" /> </li>
           <li><input type="password" name="isPwd" placeholder="确认新密码" /> </li>
        </ul>
     </div>
     <p class="validate_error" style="display:none;"></p>
  </div>
<?php $this->beginBlock('js') ?>
    <script type="text/javascript">
        seajs.use(['/modules/js/me/changepwd','/mobiend/js/mod/app'],function(login){
           login.init();
        });
    </script>
<?php $this->endBlock() ?>
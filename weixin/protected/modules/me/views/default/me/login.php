<?php
use yii\helpers\Html;
$this ->title='登录';
?>
<?php $this->beginBlock('css') ?>
    <link rel="stylesheet" href="/modules/css/index/index.css" />
    <link rel="stylesheet" href="/modules/css/base.css" />
<?php $this->endBlock() ?>
  <header>
     <div class="Head">
        <a class="top-back" href="javascript:history.back(-1);"></a>
        <div class="Hcon">
          <h1 style="font-size: 0.98rem;">登录</h1>
       </div>
     </div>
  </header>
  <div class="padt1" style="padding-top: 0;margin-top:2.45rem;">
     <div class="formbox" style="margin-top:0">
        <ul class="clearfix" id="login_form">
           <li><input type="text" name="number" value="" placeholder="输入手机号" /></li>
           <li><input type="password" name="password" value="" placeholder="输入密码" /></li>
        </ul>
     </div>
     <!-- <div class="formbtn"><button onClick="window.open('Member.html','_self')">登录</button></div> -->
     <div class="formbtn"><button>登录</button></div>
     <div class="L-entry" style="font-size: 0.98em;"><a href="/me/me/forget-pwd">忘记密码</a> <s></s> <a href="/me/me/register">立即注册</a></div>
     <p class="validate_error" style="display:none;"></p>
  </div>
<?php $this->beginBlock('js') ?>
    <script type="text/javascript">
        seajs.use(['/modules/js/me/index','/mobiend/js/mod/app'],function(login){
           login.init();
        });
    </script>
<?php $this->endBlock() ?>
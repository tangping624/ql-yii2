<?php
use yii\helpers\Html;
$this ->title='注册';
?>
<?php $this->beginBlock('css') ?>
    <link rel="stylesheet" href="/modules/css/index/index.css" />
    <link rel="stylesheet" href="/modules/css/base.css" />
<?php $this->endBlock() ?>
  <header>
     <div class="Head">
        <a class="top-back" href="javascript:history.back(-1);"></a>
        <div class="Hcon">
          <h1 style="font-size: 0.98rem;">注册</h1>
       </div>
     </div>
  </header>
  <div class="padt1" style="padding-top: 0;margin-top:2.45rem;">
     <div class="formbox" style="margin-top:0">
        <ul class="clearfix" id="login_form">
           <li><input type="text" name="number" value="" placeholder="输入手机号" /></li>
           <li><input type="text" name="code" value="" placeholder="输入短信验证码" /> <div class="Getcode" id="getCode">获取验证码</div></li>
        </ul>
     </div>
     <div class="formbtn"><button>下一步</button></div>
      <p class="validate_error" style="display:none;"></p>
  </div>
<?php $this->beginBlock('js') ?>
    <script type="text/javascript">
        seajs.use(['/modules/js/me/register','/mobiend/js/mod/app'],function(login){
           login.init();
        });
    </script>
<?php $this->endBlock() ?>
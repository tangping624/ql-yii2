<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>麒麟文化后台系统</title>
    <link href="/frontend/css/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/modules/css/login.css?v=20151222" rel="stylesheet">
    <link rel="icon" href="http://carlife.oss-cn-beijing.aliyuncs.com/root/icon/clogo.ico"
          otype="image/x-icon"/>
    <link rel="shortcut icon" href="http://carlife.oss-cn-beijing.aliyuncs.com/root/icon/clogo.ico"
          type="image/x-icon"/>
    <!--[if lt IE 9]>
    <script type="text/javascript"  src="/frontend/js/lib/compatible.js"></script>
    <![endif]-->
    <script type="text/javascript">
        if (window.top != window.self && top.postMessage) {
            top.postMessage('{"dataType":"redirectLogin","url":"'+window.location.href+'"}','*');
        }
    </script>
</head>
<body>
    <div class="login-container">
        <div class="header">
            <div class="wrap">
                <img src="/modules/images/cy963.png" style="width:22%;"/>
            </div>
        </div>
        <div class="content">
            <div class="wrap">
                
                <div class="login-wrap clearfix">
                    <?php
                    if (empty($logined) || !$logined) {
                        ?>
                        <form method="post" onsubmit="return beforeSubmit()" >
                            <div class="pull-overflow login-form">
                                <strong>登录</strong>
                                <div class="form-input">
                                    <span class="login-icon user-icon pull-left"></span>
                                    <div class="pull-overflow">
                                        <input type="text" placeholder="用户名" id="user_name" name="userName" value="<?= isset($model->userName) ? $model->userName : '' ?>">
                                    </div>
                                </div>
                                <div class="form-input">
                                    <span class="login-icon lock-icon pull-left"></span>
                                    <div class="pull-overflow">
                                        <input type="password" placeholder="密码" id="password" value="<?= isset($model->password) ? $model->password : '' ?>">
                                    </div>
                                    <input type="hidden" id="passwd_enc" name="password">
                                </div>
                                <?php
                                if (isset($captcha_code_show) && $captcha_code_show==true) {
                                ?>
<!--                                <div class="clearfix form-inline">-->
<!--                                    <div class="captcha-code">-->
<!--                                        <input type="text" name="captchaCode" size="10" maxlength="6" placeholder="验证码"/>-->
<!--                                    </div>-->
<!--                                    <img id="captcha" class="captcha-img" src="/securimage/securimage_show.php" alt="验证码" />-->
<!--                                    <a href="javascript:;" onclick="document.getElementById('captcha').src = '/securimage/securimage_show.php?' + Math.random(); return false">换一张</a>-->
<!--                                </div>-->
                                <?php
                                }?>
                                    <div class="form-checkbox clearfix" style="margin-top:5px">
                                        <span class="checkbox-input"><input type="checkbox"id="remember"/></span>&nbsp;
                                        <label for="remember">记住登录帐号</label>
                                    </div>
                                    <div class="login-btn-wrap">
                                        <button type="submit" class="login-btn" id="login_btn">登 录</button>
                                    </div>

                                <div class="color-red" style="min-height:20px;margin-bottom: 5px;font-size:14px;<?= isset($error) ? '' : 'visibility:hidden;' ?>" >
                                    <?php
                                    if (isset($error)) {?>
                                        <?php
                                        if (is_array($error)) {?>
                                            <?php
                                            foreach ($error as $f => $msg) {
                                                ?>
                                                <?= $msg[0] ?>
                                                <br/>
                                            <?php
                                            }?>
                                        <?php
                                        } else {?>
                                            <?= $error ?>
                                        <?php
                                        }?>
                                    <?php
                                    }?>
                                </div>

                            </div>
                        </form>
                    <?php
                    } else {?>
                        <div style="text-align:center;font-size: 14px;">欢迎，<strong><?= $displayName ?></strong></div>
                    <?php
                    }?>
                </div>
            </div>
        </div>
        
    </div>
    
    <script type="text/javascript" src="/frontend/js/lib/jquery/jquery-1.11.2.min.js"></script>
    <script type="text/javascript" src="/frontend/js/lib/jquery.cookie/jquery.cookie.js"></script>
    <script type="text/javascript" src="/frontend/js/lib/placeholder.js"></script>
    
    <script type="text/javascript">
 
        
        function beforeSubmit() {
            document.getElementById('passwd_enc').value = document.getElementById('password').value;
            //记住登录账号
            if ($("#remember").prop("checked")) {
//                var enterprise = $("#enterprise").val();
                var username = $("#user_name").val();
//                $.cookie("loginorganizationame", enterprise, {expires: 7});
                $.cookie("loginUsername", username, {expires: 7});
                $.cookie("loginRemember", true, {expires: 7});
            } else {
//                $.cookie("loginorganizationame", '', {expires: 7});
                $.cookie('loginUsername', '', {expires: -1});
                $.cookie("loginRemember", false, {expires: 7});
            }

            return true;
        }
        
        $(function () {
            $('#login_btn').on('click',function(){
                $(this).text('正在登录...');
            });

//            $('#enterprise').keypress(function (e) {
//                if (e.keyCode == 0x0A || e.keyCode == 0x0D) {
//                    if ($.trim(this.value)) {
//                        $('#user_name').focus();
//                    }
//                    e.preventDefault();
//                }
//            });

            $('#user_name').keypress(function (e) {
                if (e.keyCode == 0x0A || e.keyCode == 0x0D) {
                    if ($.trim(this.value)) {
                        $('#password').focus();
                    }
                    e.preventDefault();
                }
            });

            $('#password').keypress(function (e) {
                if (e.keyCode == 0x0A || e.keyCode == 0x0D) {
                    $('#login_btn').click();
                    e.preventDefault();
                }
            });

//            // 如果后端从Returnurl提取了企业号(txtOrganizationName不为空)，则就不用从cookie取。
//            var organizationame = $.cookie("loginorganizationame");
//            if ($("#enterprise").val() == "" && organizationame) {
//                $("#enterprise").val(organizationame);
//            }

            var username = $.cookie("loginUsername");
            if (username && username != 'null' && !$("#user_name").val()) {
                $("#user_name").val(username);
            }

            var remember = $.cookie("loginRemember");
            if (remember=='true') {
                $("#remember").prop("checked", true);
                $('.checkbox-input').removeClass('checkbox-input-on');
            } else {
                $("#remember").prop("checked", false);
                $('.checkbox-input').addClass('checkbox-input-on');
            }
            
            $("#remember").on('click',function(){
                checked($(this));
            });
            
            function checked(checkbox){
                if ($(checkbox).prop("checked")) {
                    $('.checkbox-input').addClass('checkbox-input-on');
                } else {
                    $('.checkbox-input').removeClass('checkbox-input-on');
                }
            }
            checked($("#remember"));
        });
    </script>
</body>
</html>

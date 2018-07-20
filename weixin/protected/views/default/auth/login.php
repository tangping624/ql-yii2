<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>客户经营平台</title>
    <link href="/frontend/css/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="loginpage">
    <div class="manage-header">
        <div class="container-fixed">
            <h3 class="logo">客户经营平台</h3>
        </div>
    </div>

    <div class="container">
        <img src="/frontend/images/global/login-bg.png" alt="" width='100%'>
        <div class="content-wrap">
            <div class="tips color-white f24">&quot;品牌社区&quot; 服务平台</div>

            <div class="login-form">
                 <form method="post" onsubmit="return beforeSubmit()">
                    <p class="f18 mb18">登录</p>
                <div class="text-wrap">
                    <span class="icon icon-name"></span>
                    <input type="text" placeholder="用户名" id="user_name" name="LoginForm[userName]" value="<?= isset($model->userName) ? $model->userName : '' ?>">
                </div>
                <div class="text-wrap">
                    <span class="icon icon-pass"></span>
                    <input type="password" placeholder="密码" id="password">
                    <input type="hidden" id="passwd_enc" name="LoginForm[password]">
                </div>

                <p class="error-info color-red <?= isset($error) ? '' : 'hide' ?>" id="error_info"><?= isset($error) ? $error : '' ?></p>
                <div class="login">
                    <input type="checkbox" id="remember" />
                    <label for="remember" class="f14">记住登录账号</label> 
                    <button id="login_btn" class="btn-pr ok-btn f14 login-btn">登录</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <div class="manage-footer color-gray align-c f14" style="margin-top:20px">
        Copyright &copy; <?php echo date('Y');?> 明源云服务 版权所有  鄂ICP备15011531号-1
    </div>

<script type="text/javascript" src="/frontend/js/lib/jquery/jquery-1.11.2.min.js"></script>
<!-- <script type="text/javascript" src="/frontend/js/lib/md5-min.js"></script> -->
<script type="text/javascript" src="/frontend/js/lib/jquery.cookie/jquery.cookie.js"></script>
<script type="text/javascript">
    function beforeSubmit()
    {
        document.getElementById('passwd_enc').value = document.getElementById('password').value;
        //记住登录账号
        if ($("#remember").prop("checked")) {
            var enterprise = $("#enterprise").val();
            var username = $("#user_name").val();
            $.cookie("loginorganizationame", enterprise, { expires: 7 });
            $.cookie("loginUsername", username, { expires: 7 });
            $.cookie("loginRemember", true, { expires: 7 });
        } else {
            $.cookie("loginorganizationame", '', { expires: 7 });
            $.cookie('loginUsername', '', { expires: -1 });
            $.cookie("loginRemember", false, { expires: 7 });
        }

        return true;
    }

    $(function () {
        $('#enterprise').keypress(function (e) {
            if (e.keyCode == 0x0A || e.keyCode == 0x0D) {
                if ($.trim(this.value)) {
                    $('#user_name').focus();
                }
                e.preventDefault();
            }
        });

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

        // 如果后端从Returnurl提取了企业号(txtOrganizationName不为空)，则就不用从cookie取。
        var organizationame = $.cookie("loginorganizationame");
        if ($("#enterprise").val() == "" && organizationame) {
            $("#enterprise").val(organizationame);
        }

        var username = $.cookie("loginUsername");
        if (username && username != 'null' && !$("#user_name").val()) {
            $("#user_name").val(username);
        }

        var remember = $.cookie("loginRemember");
        if (remember) {
            $("#remember").prop("checked", true);
        } else {
            $("#remember").prop("checked", false);
        }
    });
</script>
</body>
</html>
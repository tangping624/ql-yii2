<?php
use yii\helpers\Html;
$this ->title='我的';
?>
<?php $this->beginBlock('css') ?>
    <link rel="stylesheet" href="/modules/css/index/index.css" />
    <link rel="stylesheet" href="/modules/css/base.css" />
    <link rel=stylesheet href=https://res.wx.qq.com/open/libs/weui/1.1.0/weui.min.css>
    <style>
      .dialog-cls-box{
        position: absolute;
        z-index: 999;
        max-width: 86%;
        top: 0;
        left: 0;
        overflow: hidden;
      }
      .dialog-msg-wrap{
        overflow: hidden;
        padding: 12px;
      }
      .dialog-cls-wrap{
        background-color: rgba(0,0,0,0.6);
        border-radius: 7px;
        padding: 10px;
      }
      .dialog-cls-box div, .dialog-cls-box a{
        font-family: "微软雅黑";
      }
      .dialog-msg-content{
        font-size: 14px;
        color: white;
      }
      #loading{display: block;position: absolute;top: 40%;}
    </style>
<?php $this->endBlock() ?>
  <header>
     <div class="Head">
        <a class="top-back" href="javascript:history.back(-1);"></a>
        <div class="Hcon">
          <h1 style="font-size: 0.98rem;">我的</h1>
       </div>
     </div>
  </header>
  <!--header E-->
  <img src='/images/loading.gif' id="loading" style="display: none;">
  <div class="padt1 padb1" style="padding-top: 0；margin-top:2.45rem;">
  <input type="hidden" value="<?= $data['id'] ?>" id="id">
     <div class="Box mart0">
        <ul class="clearfix">
           <li class="userimg">
             <div href="javascript:;" id="upimg">
                <div class="img fd-right"><img src="<?= $data['headimg_url'] ?>" onerror="javascript:this.src='/images/myPhoto.png'" class="img_src"/></div>
                <b>头像</b>
             </div>
             <div class="weui-cells weui-cells_form" id=uploaderCustom style="position: absolute;display: block;width: 100%;top: 0;opacity: 0;">
                  <div class=weui-cell>
                      <div class=weui-cell__bd>
                          <div class=weui-uploader>
                              <div class=weui-uploader__bd>
                                  <ul class=weui-uploader__files id=uploaderCustomFiles></ul>
                                  <div style="height: 40px;"> <input id=uploaderCustomInput class=weui-uploader__input type="file" accept="image/gif,image/jpeg,image/jpg,image/png,image/svg"></div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
           </li>
           <li>
             <a href="/me/me/name?id=<?= $data['id'] ?>&name=<?= $data['name'] ?>">
                <span class="fd-right" id="<?= $data['id'] ?>"><?= $data['name'] ?></span>
                <b>昵称</b>
             </a>
           </li>
           <li>
             <a href="/me/me/pwd?id=<?= $data['id'] ?>&mobile=<?= $data['mobile'] ?>">
                <b>登录密码</b>
             </a>
           </li>
        </ul>
     </div>
  </div>
  <div class="dialog-cls-box dialog-msg-box hide" id="dialog_msg_box" style="position: fixed;top: 314.5px;"><div class="dialog-cls-wrap dialog-msg-wrap" id="dialog_msg_wrap"><div class="dialog-msg-content" id="dialog_msg_content"></div></div></div>
  <!--footer-->
  <footer>
     <div class="fixbtn"><a href="/me/me/login-out">退出当前账号</a></div>
  </footer>
  <script type="text/javascript" src="/modules/js/jquery-1.9.1.js"></script>
  <script type="text/javascript" src="/modules/js/me/weui.js"></script>
<?php $this->beginBlock('js') ?>
<script type="text/javascript">
/* 图片手动上传 */
    var uploadCustomFileList = [];
    // 这里是简单的调用，其余api请参考文档
    weui.uploader('#uploaderCustom', {
        url: '/me/me/upload-image',
        auto: false,
        compress: {
          width:1600,
          height:1600,
          quality:1
        },
        onBeforeQueued: function(files) {
            if (["image/jpg", "image/jpeg", "image/png", "image/gif"].indexOf(this.type) < 0) {
                weui.alert('请上传图片');
                return false;
            }
            if (this.size > 10 * 1024 * 1024) {
                weui.alert('请上传不超过10M的图片');
                return false;
            }
            if (files.length > 2) { // 防止一下子选中过多文件
                weui.alert('最多只能上传1张图片，请重新选择');
                return false;
            }
        },
        onQueued: function() {
            $('.img_src').attr('src',this.url);
            uploadCustomFileList.push(this);
            uploaderCustomBtn();
            $('#loading').show();
            var leFt = ($(window).width()-$('#loading').outerWidth())/2;
            $('#loading').css('left',leFt);
            // console.log(this.base64); // 如果是base64上传，file.base64可以获得文件的base64
            return true; // 阻止默认行为，不显示预览图的图像
        },
        onBeforeSend: function(data, headers) {
            //console.log(this, data, headers);
        },
        onProgress: function(procent) {
            //console.log(this, procent);
        },
        onSuccess: function(ret) {
            //console.log(ret);
        },
        onError: function(err) {
            console.log(this, err);
        }
    });
    function uploaderCustomBtn () {
        uploadCustomFileList.forEach(function (file) {
            var formData = new FormData();
            formData.append('file', file, file.name);
            $.ajax({
                url: '/me/me/upload-image',
                type: "POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                console.log(data);
                    if(data.status =='1'){
                        $.ajax({
                          type:'get',
                          url:'/me/me/update-photo',
                          data:{
                            id:$('#id').val(),
                            headimg_url:data.thumbnail
                          },
                          success:function(data){
                            $('#loading').hide();
                            $('#dialog_msg_content').html(data.msg);
                            $('#dialog_msg_box').show();
                            var leFt = ($(window).width()-$('#dialog_msg_wrap').outerWidth())/2;
                            $('#dialog_msg_box').css('left',leFt);
                            setTimeout("$('#dialog_msg_box').hide()",2000);
                            setTimeout(function(){
                            $('#dialog_msg_box').hide(0,function(){location.href="/me/me/index";});
                            },2000);
                          }
                        });
                    }
                }
            });
        });
    }

</script>
<?php $this->endBlock() ?>

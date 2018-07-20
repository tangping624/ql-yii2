<?php
use yii\helpers\Html;
$this ->title='发布游说';
?>
<?php $this->
beginBlock('css') ?>
<link rel="stylesheet" type="text/css" href="/modules/css/index/index.css"/>
<link rel="stylesheet" href="/modules/css/base.css" />
<link rel="stylesheet" href="/modules/css/details.css" />
<link rel=stylesheet href="/modules/css/weui.min.css">
    <script type="text/javascript" src="/modules/js/me/weui.js"></script>
<style type="text/css">
    #blog_title{border:none;height :24px;line-height: 24px;width:100%;}
    #blog_content{border:none;height:200px;line-height: 24px;width:100%;}
    #blog_save{background: #f47920;color: #fff;border-radius: .2rem;font-size: .89rem;line-height: 2.25rem;text-align: center;margin: .56rem 1rem;}
    .weui-cells:after, .weui-cells:before{display: none;}
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
        <div class="Hcon"><h1>发布游说</h1></div>
        <a class="icon" href="javascript:;"></a>
        <a class="icon sc" href="javascript:;"></a>
    </div>
</header>
<div style="margin-top:2.45rem;padding:0 10px;border-bottom: 1px solid #e6e6e6;background:#fff;">
   <div style="padding:8px 0px;">
    <input type="text" name="title" id="blog_title" placeholder="请输入标题" value="<?= $data?$data['title']:'' ?>">
    </div>
</div>
<div style="margin-top:0px;padding:0 10px;border-bottom: 1px solid #e6e6e6;background:#fff;">
     <textarea name="content" id="blog_content" cols="30" rows="10" placeholder="请输入内容" ><?= $data? $data['content']:'' ?></textarea>
</div>
<div class="weui-cells weui-cells_form" id=uploaderCustom style="margin:0;">
    <div class=weui-cell>
        <div class=weui-cell__bd>
            <div class=weui-uploader>
                <div class=weui-uploader__bd>
                
                    <ul class=weui-uploader__files id=uploaderCustomFiles>
                    <?php if($data){?>
                     <li class="weui-uploader__file" data-id="1" data-url="<?= $data?$data['photo']:''?>" style="background-image: url(<?= $data?$data['photo']:''?>);">  
                     </li>
                     <?php }?>   
                    </ul>
                    <div class=weui-uploader__input-box  style="<?= $data?'display:none':''?>"> <input id=uploaderCustomInput class=weui-uploader__input type="file" accept="image/gif,image/jpeg,image/jpg,image/png,image/svg"> </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="uploaderCustomBtn" style="display: none;"></div>
<div id="blog_save" data-id="<?= $data?$data['id']:'' ?>">发布</div>
<div class="dialog-cls-box dialog-msg-box hide" id="dialog_msg_box" style="position: fixed;top: 314.5px;"><div class="dialog-cls-wrap dialog-msg-wrap" id="dialog_msg_wrap"><div class="dialog-msg-content" id="dialog_msg_content"></div></div></div>
<?php $this->beginBlock('js') ?>
<script type="text/javascript">
</script>
<script>
 /* 图片手动上传 */
    var uploadCustomFileList = [];
    var imgsrc,statu,statu1;
    var blog_id=$("#blog_save").attr("data-id");
    $("#uploaderCustomFiles").click(function(){
        if(blog_id||statu){
            statu = 0;statu1 = 0;imgurl="";imgurl="";
            uploadCustomFileList = [];
            $("#uploaderCustomFiles li").remove();
            $('.weui-uploader__input-box').show();
            // $(".weui-uploader__input").trigger("change");
            $(".weui-uploader__input").click();  
        }
            // upload();
    });
    // 这里是简单的调用，其余api请参考文档
    weui.uploader('#uploaderCustom', {
        url: '/me/me/upload-image',
        auto: false,
        compress:{
            width: 99999,
            height: 99999,
            quality: 1
        },
        onBeforeQueued: function (files) {
            if (["image/jpg", "image/jpeg", "image/png", "image/gif"].indexOf(this.type) < 0) {
                weui.alert('请上传图片');
                return false;
            }
            $('.weui-uploader__input-box').hide();
        },
        onQueued: function () {
            console.log(this);
            uploadCustomFileList.push(this);
            uploaderCustomBtn();
            //console.log(this);
            // console.log(this.base64); // 如果是base64上传，file.base64可以获得文件的base64
            
            // this.upload(); // 如果是手动上传，这里可以通过调用upload来实现
            
            // return true; // 阻止默认行为，不显示预览图的图像
        },
        onBeforeSend: function (data, headers) {
            //console.log(this, data, headers);
            // $.extend(data, { test: 1 }); // 可以扩展此对象来控制上传参数
            // $.extend(headers, { Origin: 'http://127.0.0.1' }); // 可以扩展此对象来控制上传头部
            // return false; // 阻止文件上传
        },
        onProgress: function (procent) {
            //console.log(this, procent);
        },
        onSuccess: function (ret) {
            //console.log(this, ret);
            //imgsrc = ret.original;
        },
        onError: function (err) {
            //console.log(this, err);
        }
    });
    function uploaderCustomBtn () {
        statu1 = 1;
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
                        dialogMsg('图片上传成功');
                        imgsrc = data.original;
                        statu = 1;
                    }
                }
            });
        });
    }
    // var imgurl=$(".weui-uploader__file").css("background-image");
    var imgurl=$(".weui-uploader__file").attr("data-url");
    $('#blog_save').click(function(){
        if($('#blog_title').val().length == 0){
            dialogMsg('标题不能为空');
            return false;
        }
        if($('#blog_content').val().length == 0){
            dialogMsg('内容不能为空');
            return false;
        }
        if(!imgsrc&&!imgurl){
            if(statu1){
                dialogMsg('图片上传中');
            }else{
                dialogMsg('请上传图片');  
            }
            return false;
        }
        var data={};
        data.title=$('#blog_title').val();
        data.content=$('#blog_content').val();
        data.photo=imgsrc?imgsrc:imgurl;
        if(blog_id){
           data.id=blog_id;
        }
        $.ajax({
            type:'post',
            url:'/me/member/save',
            data:data,
            success:function(data){
               if(data.result){
                  dialogMsg('发布成功');
                  setTimeout("window.location='/me/member/my-blog'",2000);
               }
            }
        });
    });
    function dialogMsg(msg){
        $('#dialog_msg_content').html(msg);
        $('#dialog_msg_box').show();
        var leFt = ($(window).width()-$('#dialog_msg_wrap').outerWidth())/2;
        $('#dialog_msg_box').css('left',leFt);
        setTimeout("$('#dialog_msg_box').hide()",2000);
    }
</script>
<?php $this->endBlock() ?>
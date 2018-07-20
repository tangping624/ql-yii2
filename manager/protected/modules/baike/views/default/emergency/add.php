<?php
use yii\helpers\Html;
$isEdit = true;
if(!isset($model)) {$model = (object)null;$isEdit = false;}
?>

<?php $this->beginBlock('css') ?>
    <link rel="stylesheet" href="/modules/css/global.css" />
    <style type="text/css">
        .img-action>span{padding-left: 15px;padding-right: 15px;cursor: pointer;}
        .inp-short{width: 120px; margin-right:5px; background-color:#fff; height: 30px; border: 1px solid #CECECE; padding:5px;margin-left:75px;}
        .advert-category{  width:100%;  height:60px!important;  overflow: hidden;  }
        .color-red1{margin-left:87px !important;color:rgb(225,95,99)!important;}
        .advert-feature>input{  margin-right: 17px;  }
        .title{  width:101%!important;  }
        .recommend div input{width:100%!important;}
        .title_maxlength{position:absolute;top:0px;right:38%;color:#CECECE;text-align: right;height:30px;line-height:30px;padding-right:5px;}
        .color-red1{color: #e15f63;margin-top:5px}

    </style>
<?php $this->endBlock() ?>
<?php
?>
    <div class="manage-content">
        <input type="hidden" value="<?= $isEdit?>" id="is_edit"/>
        <input type="hidden" value="<?= $isEdit?$model->id:''?>" id="emergencyid"/>

        <h4 class="padding manage-title">紧急管理</h4>
        <div class="page-nav title-bottom">
            <a href="<?= $this->context->createUrl("/baike/emergency/index")?>" class="icon-merge icon-goback" title="返回上一层">返回上一层</a>
            <a href="<?= $this->context->createUrl("/baike/emergency/index")?>" class="td-u">紧急管理</a> / <span><?= $isEdit ? '编辑紧急' : '新增紧急' ?></span>
        </div>
        <div class="page-con clearfix" >
            <!-- <div class="fl left-box">
                <p class="color-gray" id="title">紧急标题</p>
                <div class="cover-box" id="cover_box">
                    <?php if($isEdit) {?>
                        <img width="100%"; height="100%"; src="<?=$model->logo?>">
                    <?php }else{?>
                        <span>封面图片</span>
                    <?php }?>
                </div>
            </div> -->
            <div  id="form" style="padding-left:80px;width:80%;">
                <!-- <span class="icon-merge trigon" style="display:block"></span> -->
                <div  class="advert-category " style="position: relative;width:100%;">
                    <span>标题</span>
                    <input style="width:50%;margin-left:55px;" type="text" maxlength="60"  class="inp-short" required="true" id="emergency_title" name="emergency_title" value="<?= $isEdit ? Html::encode($model->title) : '' ?>" onkeyup="javascript:setShowLength(this, 60,'title_maxlength');">
                    <span id="title_maxlength" class="title_maxlength">0/60</span>
                </div>
                <div  class="advert-category" >
                    <span>电话</span>
                    <input style="width:50%;margin-left:55px;" type="text" maxlength="60"  class="inp-short" required="true" id="emergency_tel" name="emergency_tel" value="<?= $isEdit ? Html::encode($model->tel) : '' ?>" > 
                </div> 
                <div  class="advert-category" >
                    <span>地址</span>
                    <input style="width:50%;margin-left:55px;" type="text" maxlength="60"  class="inp-short" required="true" id="emergency_address" name="emergency_address" value="<?= $isEdit ? Html::encode($model->address) : '' ?>" > 
                </div>   
               
                <div class="imgup-wrap clearfix" id="imgup_wrap">
                    <p class=" fl" >上传图片<span class="color-gray"></span></p>
                    <div class="img-wraps clearfix fl" id="img_wraps" style="margin-right:260px;margin-left:30px;">
                    <?php if($isEdit){ ?>
                            <div class="img-wrap">
                                <div class="img-box">
                                    <img src="<?=$model->logo ?>" alt="" class="js-img" />
                                </div>
                                <div class="opeate">
                                    <!-- <span class="opt-btn f-btn">
                                        <span class="icon-merge icon"></span>
                                    </span> -->
                                    <span class="opt-btn d-btn" style="width:100%;text-align:center;">
                                        <span class="icon-merge icon" style="margin:6px 0px;"></span>
                                    </span>
                                </div>
                            </div>
                    <?php }else{ ?>
                    <?php } ?>
                    
                    </div>
                    <button class="btn-pr bg-white upload-btn clearfix" id="upload_btn" style="">上传</button>
                    <p class="color-gray" style="margin-left:86px;">已上传图片<span id="up_num">0</span>/1，只能传一张图片作为封面，每张最大支持500k，支持jpg/gif/png格式</p>
                </div>
                <div class="text-wrap clearfix" id="uewrap_0" >
                    <p class="fl" style="">
                        <span class="name">紧急介绍</span>
                        <!-- <span class="icon-merge icon-edit" data-toggle="tooltip" title="编辑信息" data-toggle="popover"></span> -->
                        <!-- <span class="color-gray">（填）</span> -->
                    </p>
                    <div style="width:550px;height:200px;margin-left:30px;" id="uedesc_0" class="ueedit-box fl clearfix" sign="ext"></div>
                    <script type="text/template" class="js-detail0" id="js-detail">
                     <?= $isEdit ? Html::encode($model->content) : '' ?>
                    </script>
                </div>
            </div>
        </div>
        <div class="submit" style="text-align: center"><button class="btn-pr bg-green color-white" id="submit_btn">保存</button></div>
    </div>
    <script type="text/template" id="img_templ">
    <div class="img-wrap" id="<%-id%>">
        <div class="img-box">
            <p class="wait">等待中...</p>
            <div class="per"><span class="pct"></span></div>
        </div>
        <div class="opeate"> 
            <!-- <span class="opt-btn f-btn">
                <span class="icon-merge icon"></span>
            </span> -->
            <span class="opt-btn d-btn" style="width:100%;text-align:center;">
                <span class="icon-merge icon" style="margin:6px 0px;"></span>
                </span>
        </div>
    </div>
</script>
<script type="text/template" id="de_templ">
    <div class="tips-wrap">
        <div class="delete-info">确定删除？</div>
        <button class="btn-pr bg-green color-white delete-btn">确定</button>
        <button class="btn-pr bg-white fr cancel-btn">取消</button>
    </div> 
</script>

<?php $this->beginBlock('js') ?>
    <script type="text/javascript" src="/frontend/3rd/ueditor/ueditor.config.js"></script>
    <script type="text/javascript" src="/frontend/3rd/ueditor/ueditor.all.min.js"></script>
    <script type="text/javascript" src="/frontend/3rd/ueditor/lang/zh-cn/zh-cn.js"></script>
    <script type="text/javascript" src="/frontend/3rd/plupload/plupload.full.min.js"></script>
    <script type="text/javascript" src="/frontend/css/bootstrap/dist/js/bootstrap.js"></script>
    <script type="text/javascript" src="/modules/js/public/public.js"></script>
<script type="text/javascript">
    seajs.use('/modules/js/baike/emergency-add',function(add){
        add.init();
    });
</script>
<?php $this->endBlock() ?>
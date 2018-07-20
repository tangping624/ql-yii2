<?php 
    use yii\helpers\Html;
    $isEdit = true;
    if(count($data) == 0) {$isEdit = false;}
?>
<?php $this->beginBlock('css') ?>
 <link rel="stylesheet" href="/modules/css/global.css" />
<link href="/modules/css/global/public.css" rel="stylesheet">
<link href="/modules/css/page/popup.css" rel="stylesheet">
 <style type="text/css"> 
    .submit{margin:60px 30px 30px;text-align: center;border-top: 1px solid #E7E7EB;padding-top:30px;}
    .submit button{width:100px;} 
    .media_cover{width: 20%;margin-right: 2%;height:83px;text-align: center;margin-left:90px; }
    .media_cover .add_wrap{display:block;text-decoration: none;}
    .media_cover .add_wrap p{color:#d9d9d9;}
    .media_add{background: url("/frontend/js/widgets/weixinEdition/images/base_z.png") 0 -2839px no-repeat;width: 36px; height: 36px;vertical-align: middle;display: inline-block;overflow: hidden;}
    .imgup-wrap{padding-bottom: 20px;}
    .imgup-wrap .title{margin-bottom:3px;}
    .img-wrap{width: 100%;height:115px;text-align: center;margin-bottom: -75px;}
    .img-wrap p{text-align: center;margin-top:20px;font-size:12px;color:#B2B2B2;}
    .img-wrap .per{background-color:#ECECEC;width:100px;height:10px; margin: 10px auto;}
    .img-wrap .pct{float:left;width:10%;background-color: green;height:100%;}
/*.first-box{margin-left:0;}*/
    .img-wrap .img-box{height:100%;border:1px solid #E7E7EB;background-color: white;}
    .img-wrap .img-box img{width:100%;height:100%;}
    .img-wrap .opeate{border:1px solid #E7E7EB;border-top:0;border-right:0;height:30px;}
    .img-wrap .opeate > span{float:left;height:100%;width:50%;border-right:1px solid #E7E7EB;cursor: pointer;}
    .img-wrap .opeate > span:hover{background-color: #ECECEC}
    .img-wrap .icon{display:inline-block;margin: 6px 0 0 15px;height: 17px;}
    .img-wrap .f-btn .icon{background-position: -211px 0; width: 16px;}
    .img-wrap .d-btn .icon{background-position: -315px 0; width: 15px;}
    .form-bottom{position: absolute;width: 100%;bottom: 0;}
    .icon-merge {background: url(/frontend/images/global/icon-merge.png?t=20150407) no-repeat;}
    .dn{display: none;}
    .ok-btn:hover{background: #44b549}
     /*.color-red1{position: relative}*/
</style> 
<?php $this->endBlock() ?>
    <input type="hidden" name="isEdit" id="isEdit" value="<?= $isEdit ?>">
    <div class="popup-container" style="width: 725px">
        <form id="user_form" class="form">
            <div class="popup-content">
                <div class="form-area">
                    <input type="hidden" name="parent_id" id="parent_id">
                    <div class="form-item">
                        <label class="form-field">分类名称</label>
                        <div class="form-tag-wrap">
                            <input type="text" class="form-control" id="name" name="name" value="<?= $isEdit ? Html::encode($data['name']) :''?>"/>
                            <p class="color-red1" style="display: none">请填写分类名称</p>
                        </div>
                    </div>
                    <?php if($status){?>
                    <div class="form-item order">
                        <label class="form-field">序号</label>
                        <div class="form-tag-wrap">
                            <input type="text" class="form-control" id="orderby" name="orderby" value="<?= $isEdit ? Html::encode($data['orderby']) :''?>" style="width:40%;"/>
                            <p class="color-red1" style="display: none">请填写序号</p>
                        </div>
                    </div>
                    <?php }?>
                    <div class="form-item">
                        <div class="imgup-wrap" id="imgup_wrap">
                            <label class='form-field'>分类图标</label>  
                            <div class="media_cover clearfix" id="media_cover">
                            <?php if($isEdit) {?> 
                                <div class="img-wrap">
                                    <div class="img-box">
                                        <img src="<?= Html::encode($data['icon']) ?>" class="js-img" _src="<?= Html::encode($data['icon']) ?>" onerror="javascript:this.src='/modules/images/no.png'">
                                    </div>
                                    <div class="opeate">
                                        <span class="opt-btn f-btn"  id="upload_btn1">
                                            <span class="icon-merge icon"></span>
                                        </span>
                                        <span class="opt-btn d-btn">
                                            <span class="icon-merge icon"></span>
                                        </span>
                                    </div>
                                </div>
                            <?php }?>
                            <div class="msg_processbar">
                                <span class="upload-processbar-width-wrap"><span class="upload-processbar-width"></span></span>
                            </div>
                        </div>
                        <a href="javascript:;" id="upload_btn" class="add_wrap add-media <?= $isEdit? 'dn' :''?>" style="position: relative; z-index: 1;top:-70px;left:150px;">
                            <span class="media_add"></span>
                            <p class="color-gray add-media-tips" style="margin-left:-8px;">上传图片</p>
                        </a>
                    </div>
                </div>
            </div>
            <div class="form-bottom align-c">
                <button type="button" class="btn-pr ok-btn" id="submit_btn" data-id="<?= $isEdit ? Html::encode($data['id']) : ''?>">保存</button>
                <button type="button" class="btn-pr sub-btn" id="cancel">关闭</button>
            </div>
        </form>
    </div>
 <!-- 模板 -->
<!-- 上传图片的模版 -->
<script type="text/template" id="img_templ">
    <div class="img-wrap" id="<%-id%>">
        <div class="img-box">
            <p class="wait">等待中...</p>
            <div class="per"><span class="pct"></span></div>
        </div>
        <div class="opeate"> 
             <span class="opt-btn f-btn" id="upload_btn1">
                <span class="icon-merge icon"></span>
            </span>
            <span class="opt-btn d-btn" >
                <span class="icon-merge icon"></span>
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
<!-- 模板 -->
<?php $this->beginBlock('js') ?> 
<script type="text/javascript" src="/frontend/3rd/plupload/plupload.full.min.js"></script>
<script type="text/javascript"> 
    seajs.use('/modules/js/type/type/add',function(index){
       index.init();
    });
</script>
<?php $this->endBlock() ?>



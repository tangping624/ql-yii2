<?php
use yii\helpers\Html;
$isEdit = true;
if(!isset($model)) {$model = (object)null;$isEdit = false;}
?>
<?php //var_dump($adsenses);exit;?>
<?php $this->beginBlock('css') ?>
    <link rel="stylesheet" href="/modules/css/global.css" />
    <style type="text/css">
        .page-nav{    margin: 26px 30px 0px}
        .img-action>span{padding-left: 15px;padding-right: 15px;cursor: pointer;}
        .inp-short{width: 120px; margin-right:5px; background-color:#fff; height: 30px; border: 1px solid #CECECE; padding:5px;}
        .advert-category{  width:100%;overflow: hidden;  }
        .advert-feature>input{  margin-right: 17px;  }
        .inp-pr{  width:151px;  }
        .title_maxlength{position:absolute;top:0px;right:38px;color:#CECECE;text-align: right;height:30px;line-height:30px;padding-right:5px;}
        .color-red1{color: #e15f63 !important;margin-top:5px;margin-left: 75px}

    </style>
<?php $this->endBlock() ?>
<?php
?>
    <div class="manage-content">
    <input type="hidden" value="<?= $isEdit?>" id="is_edit"/>
    <input type="hidden" value="<?= $isEdit?$model->id:''?>" id="advertid"/>

    <h4 class="padding manage-title">广告管理</h4>
    <div class="page-nav title-bottom">
        <a href="<?= $this->context->createUrl("/advertise/advert/index")?>" class="icon-merge icon-goback" title="返回上一层">返回上一层</a>
        <a href="<?= $this->context->createUrl("/advertise/advert/index")?>" class="td-u">广告管理</a> / <span><?= $isEdit ? '广告编辑' : '广告管理' ?></span>
    </div>
    <div class="page-con clearfix">
        <div class=" right-form" id="form" style="left:8%;background-color: #fff;border:none">
            <div  class="mb30 clearfix" style="position: relative;">
                <span class="">标题</span>
                <input type="text" maxlength="60"  class="inp-short" required="true" id="advert_title" name="advert_title" value="<?= $isEdit ? Html::encode($model->title) : '' ?>" onkeyup="javascript:setShowLength(this, 60,'title_maxlength');" style="width: 80%;margin-left: 44px">
                <span id="title_maxlength" class="title_maxlength"><?=$isEdit?mb_strlen($model->title):0?>/60</span>
            </div>
            <div class="advert-category mb30 clearfix">
                <span class="title">广告位</span>
                <select id="advert_adsense" class="form-control" style="width:200px; height:32px; border:1px solid #CECECE;margin-left: 30px;display: inline-block">
                    <?php if($isEdit){ ?>
                        <option value=""></option>
                        <?php foreach($adsenses as $i=>$adsenses) { ?>
                            <option value="<?= $adsenses->id ?>" <?php if($adsenses->id === $model->adsenseid){ echo 'selected="selected"'; } ?> ><?= Html::encode($adsenses->name) ?></option>
                        <?php } ?>
                    <?php }else{ ?>
                        <option value=""></option>
                        <?php foreach($adsenses as $i=>$adsenses) { ?>
                            <option value="<?= $adsenses->id ?>"><?= Html::encode($adsenses->name) ?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
                <p class="color-red1" style="display:none;">广告位不能为空</p>
            </div>
            <div class="imgup-wrap clearfix" id="imgup_wrap" style="position: relative">
                <p class="title fl" style="position: absolute;top: -4px">上传图片<span class="color-gray"></span></p>
                <?php if($isEdit){ ?>
                    <?php foreach($images as $i=>$images) { ?>
                        <div class="img-wrap" style="position: relative;float: none;margin-left: 70px">
                            <div class="img-box">
                                <img src="<?=$images->original_url ?>" alt="" class="js-img" _src="<?= Html::encode($images->thumb_url) ?>" />
                            </div>
                            <div class="opeate">
			             		<span class="opt-btn f-btn">
<!--			                		<span class="icon-merge icon"></span>-->
			                		<span class="glyphicon glyphicon-link" style="color: #aaa;margin:7px 0 0 36px;font-size: 17px;"></span>
			            		</span>
                                <span class="opt-btn d-btn" >
			                		<span class="icon-merge icon"></span>
			            		</span>
                            </div>
                            <div style="position: absolute;top: 41px;left: 200px;">
                                <span>链接地址</span>
                                <input type="text" value="<?= $isEdit ? Html::encode($images->link_url) : '' ?>" id="link_url" readonly style="border: none;width: 500px">
                            </div>

                        </div>
                    <?php } ?>
                <?php }else{ ?>
                <?php } ?>
                <div class="img-wraps clearfix" id="img_wraps">
                </div>
                <button class="btn-pr bg-white upload-btn" id="upload_btn" style="margin-left: 77px">上传</button>
                <p class="color-gray" style="margin-left: 76px;display: none"><span class="tips"></span>，每张最大支持500k，支持jpg/gif/png格式
                    <span class="recommend" style="display: none">建议上传图片的宽高比保持3：1</span></p>
            </div>
        </div>
    </div>
    <div class="submit">
        <hr>
        <button style="display:block;margin:0 auto;" class="btn-pr bg-green color-white" id="submit_btn">保存</button>
    </div>
    <script type="text/template" id="img_templ">
        <div class="img-wrap" id="<%-id%>" style="float: none;position: relative;margin-left: 76px">
            <div class="img-box">
                <p class="wait">等待中...</p>
                <div class="per"><span class="pct"></span></div>
            </div>
            <div class="opeate">
             <span class="opt-btn f-btn">
                <span class="icon-merge icon"></span>
            </span>
            <span class="opt-btn d-btn" >
                <span class="icon-merge icon"></span>
            </span>
            </div>
            <div style="position: absolute;top: 41px;left: 200px;">
                <span>链接地址</span>
                <input type="text" value="" id="link_url" readonly style="border: none;width: 500px">
            </div>
        </div>
    </script>
    <!--其他信息框删除模板-->
    <script type="text/template" id="de_ue_templ">
        <div class="tips-wrap">
            <div class="delete-info">确定删除？</div>
            <button class="btn-pr bg-green color-white js-delete-ue">确定</button>
            <button class="btn-pr bg-white fr cancel-btn">取消</button>
        </div>
    </script>
    <!--编辑名称模板-->
    <script type="text/template" id="edit_templ">
        <div class="tips-wrap">
            <p>编辑名称</p>
            <input type="text" class="inp inp-pr edit-inp" value="{name}">
            <p class="color-red edit-error hide">不能为空</p>
            <div class="mt14">
                <button class="btn-pr bg-green color-white edit-btn">确定</button>
                <button class="btn-pr bg-white fr cancel-btn">取消</button>
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
    <script type="text/template" id="de_pte_templ">
        <div class="tips-wrap">
            <div class="delete-info">确定删除？</div>
            <button class="btn-pr bg-green color-white js-delete-ue">确定</button>
            <button class="btn-pr bg-white fr cancel-btn">取消</button>
        </div>
    </script>
    <script type="text/template" id="link_templ">
        <div class="tips-wrap" style="margin: 30px auto;width: 350px;">
            <div  class="text-wrap" style="position: relative;">
                <input type="text" class="inp-short" required="true" id="advert_title" name="advert_title" value="">
            </div>
            <div class="align-c">
                <button class="btn-pr bg-green color-white js-confirm">确定</button>
                <button class="btn-pr bg-white fr btn-close" style="left: -15%;position: relative;margin-left: 30px">取消</button>
            </div>

        </div>
    </script>
    <!-- 模板 -->
    <?php $this->beginBlock('js') ?>
    <script type="text/javascript" src="/frontend/3rd/plupload/plupload.full.min.js"></script>
    <script type="text/javascript" src="/frontend/css/bootstrap/dist/js/bootstrap.js"></script>
    <script type="text/javascript" src="/modules/js/public/public.js"></script>
    <script type="text/javascript">
        seajs.use('/modules/js/advertise/add',function(add){
            add.init();
        });
    </script>
<?php $this->endBlock() ?>
<?php
use yii\helpers\Html;
$isEdit = true;
if(!isset($model)) {$model = (object)null;$isEdit = false;}
?>
<?php //var_dump($model);exit;?>
<?php $this->beginBlock('css') ?>
    <link rel="stylesheet" href="/modules/css/global.css" />
    <style type="text/css">
        .img-action>span{padding-left: 15px;padding-right: 15px;cursor: pointer;}
        .inp-short{width: 120px; margin-right:5px; background-color:#fff; height: 30px; border: 1px solid #CECECE; padding:5px;margin-left:52px;}
        .advert-feature>input{  margin-right: 17px;  }
        .recommend div input{width:100%!important;}
        .title_maxlength{position:absolute;top:0px;right:31%;color:#CECECE;text-align: right;height:30px;line-height:30px;padding-right:5px;}
        .right-form{left: 8%;background: none;border: none;width: 100%}
        .page-nav{margin: 26px 30px 0}
        .img-wrap .opeate > span{width: 100%;}
        .img-wrap .icon{position: relative;left: 25%;}
    </style>
<?php $this->endBlock() ?>
<?php
?>
    <div class="manage-content">
        <input type="hidden" value="<?= $isEdit?>" id="is_edit"/>
        <input type="hidden" value="<?= $isEdit?$model->id:''?>" id="advertid"/>

        <h4 class="padding manage-title">百科管理</h4>
        <div class="page-nav title-bottom">
            <a href="<?= $this->context->createUrl("/baike/manage/index")?>" class="icon-merge icon-goback" title="返回上一层">返回上一层</a>
            <a href="<?= $this->context->createUrl("/baike/manage/index")?>" class="td-u">百科管理</a> / <span><?= $isEdit ? '编辑百科' : '新增百科' ?></span>
        </div>
        <div class="page-con clearfix">
            <div class="right-form" id="form">
                <div  class="text-wrap clearfix bk-title" style="position: relative;">
                    <span>标题</span>
                    <input style="width:60%;margin-left: 52px;" type="text" maxlength="60"  class="inp-short" required="true" id="baike_title" name="baike_title" value="<?= $isEdit ? Html::encode($model->title) : '' ?>" onkeyup="javascript:setShowLength(this, 60,'title_maxlength');">
                    <span id="title_maxlength" class="title_maxlength">0/60</span>
                </div>
                <div class="text-wrap">

                        <div >
                            <span >分类</span>
                            <select id="baike_type" class="inp-short" style="width:200px; height:30px; border:1px solid #CECECE;">
                                <?php if($isEdit){ ?>
                                    <option value=""></option>
                                    <?php foreach($category as $i=>$value) { ?>
                                        <option value="<?= $value->id ?>" <?php if($value->id === $model->wiki_category_id){ echo 'selected="selected"'; } ?> ><?= Html::encode($value->name) ?></option>
                                    <?php } ?>
                                <?php }else{ ?>
                                    <option value=""></option>
                                    <?php foreach($category as $i=>$value) { ?>
                                        <option value="<?= $value->id ?>"><?= Html::encode($value->name) ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            <p class="color-red1" style="display:none;margin-left: 85px;margin-top: 5px">百科分类不能为空</p>
                        </div>
                </div>
                <div class="imgup-wrap clearfix" id="imgup_wrap">
                    <p class="title fl">上传图片<span class="color-gray"></span></p>
                    <div class="img-wraps clearfix" id="img_wraps">
                    <?php if($isEdit){ ?>
                            <div class="img-wrap fl"  style="margin-left: 29px;margin-right: 610px;">
                                <div class="img-box">
                                    <img src="<?=$model->logo ?>" alt="" class="js-img" />
                                </div>
                                <div class="opeate">
                                    <span class="opt-btn d-btn" >
                                        <span class="icon-merge icon"></span>
                                    </span>
                                </div>
                            </div>
                    <?php }else{ ?>

                    <?php } ?>
                    </div>
                    <div style="margin-left: 85px;margin-top: -27px;" class="upload_wrapper">
                        <button class="btn-pr bg-white upload-btn" id="upload_btn">上传</button>
                        <p class="color-gray mt10">只能传一张图片，最大支持500k，建议上传尺寸小于500k，支持jpg/gif/png格式</p>
                    </div>

                </div>
                <div class="text-wrap" id="uewrap_0" style="height: 270px">
                    <span class="name fl">百科介绍</span>
                    <div style="width:80%;height:200px;margin-left: 30px" id="uedesc_0" class="ueedit-box fl" sign="ext">

                    </div>
                    <script type="text/template" class="js-detail0" id="js-detail"><?php if($isEdit){?><?= Html::encode($model->content)?><?php }?></script>
                </div>
            </div>
        </div>
        <div class="submit" style="text-align: center"><button class="btn-pr bg-green color-white" id="submit_btn">保存</button></div>
    </div>
    <!-- 上传图片的模版 -->
    <script type="text/template" id="img_templ">
        <div class="img-wrap" id="<%-id%>" style="margin-left: 29px">
            <div class="img-box">
                <p class="wait">等待中...</p>
                <div class="per"><span class="pct"></span></div>
            </div>
            <div class="opeate">
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
<?php $this->beginBlock('js') ?>
    <script type="text/javascript" src="/frontend/3rd/ueditor/ueditor.config.js"></script>
    <script type="text/javascript" src="/frontend/3rd/ueditor/ueditor.all.min.js"></script>
    <script type="text/javascript" src="/frontend/3rd/ueditor/lang/zh-cn/zh-cn.js"></script>
    <script type="text/javascript" src="/frontend/3rd/plupload/plupload.full.min.js"></script>
    <script type="text/javascript" src="/frontend/css/bootstrap/dist/js/bootstrap.js"></script>
    <script type="text/javascript" src="/modules/js/public/public.js"></script>
<script type="text/javascript">
    seajs.use('/modules/js/baike/manage-add',function(add){
        add.init();
    });
</script>
<?php $this->endBlock() ?>
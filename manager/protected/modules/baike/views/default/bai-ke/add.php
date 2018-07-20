<?php
/**
 * Created by PhpStorm.
 * User: tx-04
 * Date: 2017/3/27
 * Time: 14:46
 */
    use yii\helpers\Html;
    $isEdit = true;
    if(!isset($model)) {$model = (object)null;$isEdit = false;}
?>
<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/global.css" />
<style type="text/css">
    .form-horizontal{margin:0px 30px 30px;}
    .submit{margin:250px 30px 30px;text-align: center;padding-top:30px;}
    .submit button{width:100px;}
    .inp-pr{width: 60% !important;border:1px solid #CECECE;height: 30px}
    .title_maxlength{position:absolute;top:0px;right:31%;color:#CECECE;text-align: right;height:30px;line-height:30px;padding-right:5px;}
</style>
<?php $this->endBlock() ?>
<div class="manage-content">
    <input type="hidden" value="<?= $data ? Html::encode($data->id) : '' ?>" id="id"/>
    <h4 class="padding manage-title">百科分类</h4>
    <div class="page-nav title-bottom">
        <a href="<?= $this->context->createUrl("/baike/bai-ke/index")?>" class="icon-merge icon-goback" title="返回上一层">返回上一层</a>
        <a href="<?= $this->context->createUrl("/baike/bai-ke/index")?>" class="td-u">百科分类</a> / <span><?= $data ? Html::encode('编辑分类') : Html::encode('新增分类') ?></span>
    </div>
    <form class="form form-base form-horizontal" id="edit_form">
        <div class="form-content" style="padding-left: 30px">
            <div class="form-group" >
                <span class='col-md-1' style="margin-top: 8px">分类名称</span>
                    <input type="text" class="inp inp-pr" id="typename" name="typename" value="<?= $data ? Html::encode($data->name) : '' ?>" placeholder="请输入分类名称" maxlength="60" onkeyup="javascript:setShowLength(this, 60,'title_maxlength');">
                <span id="title_maxlength" class="title_maxlength">0/60</span>
            </div>
            <div class="submit"><button class="btn-pr bg-green color-white" id="submit_btn">保存</button></div>
    </form>
</div>

<!-- 模板 -->
<?php $this->beginBlock('js') ?>
<script type="text/javascript" src="/modules/js/public/public.js"></script>
<script type="text/javascript">
    seajs.use('/modules/js/baike/add',function(add){
        add.init();
    });
</script>
<?php $this->endBlock() ?>



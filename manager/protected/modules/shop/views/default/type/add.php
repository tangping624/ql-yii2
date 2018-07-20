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
function convertUrlQuery($query)
{
    $queryParts = explode('&', $query);
    $params = array();
    foreach ($queryParts as $param) {
        $item = explode('=', $param);
        $params[$item[0]] = $item[1];
    }
    return $params;
}
$url=parse_url($_SERVER['REQUEST_URI']);
//var_dump(convertUrlQuery($url['query']));
$appcode=convertUrlQuery($url['query'])['app_code'];
function getUrl($appcode){
    if($appcode=='urgent') return '/baike/emergency/shop-type?app_code=urgent';
    if($appcode=='house') return '/house/house/shop-type?app_code=house';
    if($appcode=='shop') return '/shop/type/index?app_code=shop';
    if($appcode=='tour') return '/tour/tour/shop-type?app_code=tour';
    if($appcode=='invest') return '/invest/invest/shop-type?app_code=invest';
    if($appcode=='cooperation') return '/cooperation/cooperation/shop-type?app_code=cooperation';
}
$url=getUrl($appcode);
//var_dump($url);exit;
?>

<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/global.css" />
<style type="text/css">
    .form-horizontal{margin:0px 30px 30px;}
    .submit{margin:240px 30px 30px;text-align: center;border-top: 1px solid #E7E7EB;padding-top:30px;}
    .submit button{width:100px;}
    .inp-pr{width: 60% !important;border:1px solid #CECECE}
    .title_maxlength{position:absolute;top:0px;right:31%;color:#CECECE;text-align: right;height:30px;line-height:30px;padding-right:5px;}
</style>
<?php $this->endBlock() ?>
<input type="hidden" value="<?php echo $url?>" id="url">
<div class="manage-content">
    <input type="hidden" value="<?= $isEdit ? Html::encode($model->id) : '' ?>" id="id"/>
    <h4 class="padding manage-title">商品分类</h4>
    <div class="page-nav title-bottom">
        <a href="<?= $this->context->createUrl("$url")?>" class="icon-merge icon-goback" title="返回上一层">返回上一层</a>
        <a href="<?= $this->context->createUrl("$url")?>" class="td-u">商品分类</a> / <span><?= $isEdit ? Html::encode('编辑分类') : Html::encode('新增分类') ?></span>
    </div>
    <form class="form form-base form-horizontal" id="edit_form">
        <div class="form-content" style="padding-left: 30px">
            <div class="form-group" >
                <span class='col-md-1' style="margin-top: 8px">分类名称</span>
                <input type="text" class="inp inp-pr" id="typename" name="typename" value="<?= $isEdit ? Html::encode($model->name) : '' ?>" placeholder="请输入分类名称" maxlength="60" onkeyup="javascript:setShowLength(this, 60,'title_maxlength');">
                <span id="title_maxlength" class="title_maxlength">0/60</span>
            </div>
            <div class="submit" style="border-top:0px;">
                <button class="btn-pr bg-green color-white submit_btn " id="">保存</button>
                <button class="btn-pr bg-green color-white submit_btn next" id="" style="padding:6px 14px;<?=$isEdit?'display:none':'display:inline-block';?>">保存并新增</button>
                 
            </div>
    </form>
</div>

<!-- 模板 -->
<?php $this->beginBlock('js') ?>
<script type="text/javascript" src="/modules/js/public/public.js"></script>
<script type="text/javascript">
    seajs.use('/modules/js/shop/type/add',function(add){
        add.init();
    });
</script>
<?php $this->endBlock() ?>



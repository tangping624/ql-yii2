<?php 
    use yii\helpers\Html;
    $isEdit = true;
    if(empty($model)) {$model = (object)null;$isEdit = false;}
?>

<?php $this->beginBlock('css') ?>
<link href="/frontend/css/bootstrap/dist/css/bootstrap.min.css?t=2015" rel="stylesheet">
<link rel="stylesheet" href="/modules/css/global.css" />
 <style type="text/css">  
        .inline-block{display: inline-block}
        .z-input-block{width:100%;}
        .color-red1{margin-left:87px !important;color:rgb(225,95,99)!important;}
         dl{padding-bottom: 5px; padding-right:15px;}
         #type_checkbox .check{vertical-align:text-bottom;margin-bottom:2px;}
        .img-action{bottom:0;width:100%;background: rgba(0,0,0,0.3);color:#fff;line-height: 30px;}
        .img-action>span{padding-left: 15px;padding-right: 15px;cursor: pointer;}
        .inp-short{width: 120px; display: inline-block; margin-right:5px; background-color:#fff; height: 30px; border: 1px solid #CECECE; padding:5px;}
        .inp-pr1{width:160px;border:1px solid #cecece;height:30px;margin-right: 15px;}
        .inline-block>.input-radio{margin-left: 15px;}
        .submit{padding: 0 25px;text-align: center;}
        .submit hr{border-top: 1px solid #CECECE;}
        .btn-pr{padding: 6px 33px;}
        #upload_btn{border: 1px solid #CECECE;}
        .right-form{background-color:#ececec;margin-bottom:0;}
        .ps-add-wrap{margin-bottom: 30px;}   
        .input_maxlength{position:absolute;top:23px;left:490px;color:#CECECE;width:50px;text-align: right;line-height:30px;padding-right:5px;}
        .trait1_maxlength{position:absolute;top:22px;left:95px;color:#CECECE;text-align: right;height:30px;line-height:30px;padding-right:5px;}
        .trait2_maxlength{position:absolute;top:22px;left:225px;color:#CECECE;text-align: right;height:30px;line-height:30px;padding-right:5px;}
        .trait3_maxlength{position:absolute;top:22px;left:355px;color:#CECECE;text-align: right;height:30px;line-height:30px;padding-right:5px;}
        .img-wrap{border:1px solid #000;}
        .form-control{padding:4px;border-radius:4px;}
        .form-control:focus{padding:4px;border-radius:4px;box-shadow: inset 0 1px 1px rgba(0,0,0,.075);}
        #datavalid_goodsname_error_msg{position: absolute;top: 53px;left: 0;}
        #datavalid_price_error_msg{position: absolute;top: 53px;left: 0;}
        #datavalid_goodstype_error_msg{position: absolute;top: 53px;left: 0;}
        #datavalid_summary_error{float:left;}
        #datavalid_uedesc_0_error{float:left;}
        .page-nav-area{
            margin-bottom:10px;
        }
        .title_maxlength{position:absolute;top:0px;right:12%;color:#CECECE;text-align: right;height:30px;line-height:30px;padding-right:5px;}
 </style>
<?php $this->endBlock() ?>
<div class="manage-content">
    <!--新增编辑判断-->
    <input type="hidden" value="<?= Html::encode($isEdit)?>
    " id="is_edit"/>
    <!--游说id-->
    <input type="hidden" value="<?= $isEdit ? Html::encode($model['id']
    ) : '' ?>" id="newsId"/>
    <input type="hidden"  value="<?= $isEdit ? Html::encode($model['seller_id']
    ) : '' ?>" name="" id="seller">
    <h4 class="padding manage-title">房产管理</h4>
    <!--判断新增商品 编辑商品-->
    <div class="padding title-bottom">
        <a href="<?= $this->
            context->createUrl("/house/house/index")?>" class="icon-merge icon-goback" title="返回上一层">返回上一层
        </a>
        <a href="<?= $this->
            context->createUrl("/house/house/index")?>" class="color-black">房产管理
        </a>
        /
        <span>
            <?= $isEdit ? '<b>编辑房产</b>
        ' : ' <b>新增房产</b>
        ' ?>
        </span>
    </div>
    <div class="page-con clearfix" >
            <div  id="form" style="padding-left:80px;width:90%;">
                <!-- <span class="icon-merge trigon" style="display:block"></span> -->
                <div  class="advert-category " style="position: relative;width:100%;margin:20px;">
                    <span>商品名称</span>
                    <input style="width:77%;margin-left:30px;" type="text" maxlength="60"  class="inp-short" required="true" id="name" name="name" value="<?= $isEdit ? Html::encode($model['name']) : '' ?>" onkeyup="javascript:setShowLength(this, 60,'title_maxlength');">
                    <span id="title_maxlength" class="title_maxlength">0/60</span>
                </div>
                <div  class="advert-category clearfix" style="position: relative;width:90%;margin:20px;">
                 <div style="float:left;width:58%;">
                    <span style="">选择商家</span>
                
                     <input type="text" id="seller_id" name="seller_id" data-seller="<?= $isEdit ? $model['seller_id'] : '' ?>" value="<?= $isEdit ? Html::encode($model['seller_name']) : '' ?>" class="inp-short" style="width:70%;margin-left:30px;padding-right:24px;">
                    <span class="glyphicon glyphicon-search" style="font-size:15px;top:4px;left:-28px;"></span>
                    <div style="position:absolute;z-index:1000;margin-left:89px;width:40.7%;display:none;top:30px;" class="shop_grid" >
                    <div id="shop_grid">
                    <table class="table">
                        <thead>
                          <tr class="notgoodsorder on">
                            
                            <th style="width:50%">商家名称</th>
                            <th  style="width:20%">是否选择</th>
                            
                          </tr>
                        </thead>
                        <tbody style="background:#fff;">
                          <tr><td colspan="9" style="height:70px; text-align: center;">请先搜索商家</td></tr>
                        </tbody>
                    </table>
                    </div>
                    </div>
                 </div>

                 <div style="float:left;width:42%;display:<?= $isEdit ?'block':'none'?>;" class="products">
                    <span>产品分类</span>
                     <select class="inp-short" style="width:66%;margin-left:30px;" id="type_id" name="type_id" data-type="<?=$isEdit ? $model['type_id']:''?>" >
                         <option ><?= $isEdit ? Html::encode($model['tname']) : '' ?></option>
                     </select>
                 </div>
                </div>
                
                <div class="imgup-wrap clearfix" id="imgup_wrap" style="margin:20px;">
                    <p class=" fl" >上传图片<span class="color-gray"></span></p>
                    <div class="img-wraps clearfix fl" id="img_wraps" style="margin-left:30px;margin-right:420px;">
                    <?php if($isEdit){ ?>
                            <div class="img-wrap">
                                <div class="img-box">
                                    <img src="<?=$model['logo'] ?>" alt="" class="js-img" />
                                </div>
                                <div class="opeate">
                                    <span class="opt-btn d-btn" style="width:100%;text-align:center;">
                                        <span class="icon-merge icon" style="margin:6px 0px;"></span>
                                    </span>
                                </div>
                            </div>
                    <?php }else{ ?>
                    <?php } ?>
                    
                    </div>
                    <div style="margin-left: 88px;">
                    <button class="btn-pr bg-white upload-btn clearfix" id="upload_btn" style="">上传</button>
                    <p class="color-gray" style="">只能传一张图片，最大支持500k，建议上传尺寸小于500k，支持jpg/gif/png格式</p>
                    </div>
                </div>
                <div class="text-wrap clearfix" id="goodsinfoname" style="margin:20px;">
                    <p class="fl">
                        <span class="name">基本信息</span> 
                    </p>
                    <div style="height:250px;width:625px;margin-left:30px;" id="summary" class="ueedit-box fl clearfix"></div>
                    <script type="text/template" class="js-detail" id="js-detail"><?= $isEdit?Html::encode($model['summary']):''?></script>                         
                </div>
                <div class="text-wrap clearfix" id="uewrap_0" style="margin:20px;">
                    <p class="fl">
                        <span class="name">商品简介</span>
                    </p>
                    <div style="height:250px;width:625px;margin-left:30px;" id="uedesc_0" class="ueedit-box fl clearfix" sign="ext"></div>
                    <script type="text/template" class="js-detail0" ><?= $isEdit?Html::encode($model['content']):''?></script>                         
                </div>  
            </div>
        </div>
        <div class="submit" style="text-align: center"><button class="btn-pr bg-green color-white submit_btn" id="">保存</button>
        <button class="btn-pr bg-green color-white submit_btn next" id="" style="padding:6px 12px;<?=$isEdit?'display:none':'display:inline-block';?>">保存并新增</button>
        </div>
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
             
            <span class="opt-btn d-btn" style="width:100%;text-align:center;">
                <span class="icon-merge icon" style="margin-left:0px;"></span>
            </span>
        </div>
    </div>
</script>
<script type="text/template" id="grid_shop">
  <div class="grid-content clearfix" id="shop_grid">
    <table class="table">
        <thead>
          <tr class="notgoodsorder on">
            
            <th style="width:50%">商家名称</th>
            <th  style="width:20%">是否选择</th>
            
          </tr>
        </thead>
        <tbody>
          <tr><td colspan="9" style="height:70px; text-align: center;">请先搜索商家</td></tr>
        </tbody>
    </table>
</div>
<div class="art-box-footer"><button  type="button" class="btn btn-secondary btn-close" id="close">关闭</button></div>
</script>
<script type="text/template" id="holiday_templ">
       <!--  <td><%-__data.i%></td> -->
        <td><span class="name_check"><%-__data.name%></span></td>
        <td><input type="checkbox" name="" class="checkout" data-id="<%-__data.id%>">
        <button class="bg-green color-white sure" style="padding:3px 10px;margin-left:5px;border-radius: 3px;font-size:10px;">确定</button>
        </td>
    </script>
<!--图片删除模板-->
 <script type="text/template" id="de_templ">
    <div class="tips-wrap">
        <div class="delete-info">确定删除？</div>
        <button class="btn-pr bg-green color-white delete-btn">确定</button>
        <button class="btn-pr bg-white fr cancel-btn">取消</button>
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

<!-- 模板 -->
<?php $this->beginBlock('js') ?> 
<script type="text/javascript" src="/frontend/3rd/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="/frontend/3rd/ueditor/ueditor.all.min.js"></script>
<script type="text/javascript" src="/frontend/3rd/ueditor/lang/zh-cn/zh-cn.js"></script>
<script type="text/javascript" src="/frontend/3rd/plupload/plupload.full.min.js"></script>

<script type="text/javascript"> 
    seajs.use('/modules/js/house/add',function(add){
        add.init();
    });
</script>
<?php $this->endBlock() ?>








      
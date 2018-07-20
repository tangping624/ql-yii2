
<?php 
    use yii\helpers\Html;
    $isEdit = true;
    if(empty($data)) {$data = (object)null;$isEdit = false;}
?>
<?php $this->beginBlock('css') ?>
<link href="/frontend/css/bootstrap/dist/css/bootstrap.min.css?t=2015" rel="stylesheet">
<link rel="stylesheet" href="/modules/css/global.css" />
 <style type="text/css">  
        .inline-block{display: inline-block;vertical-align: top;}
        .z-input-block{width:100%;}
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
        .color-red1{color: #e15f63 !important;}
 </style>
<?php $this->endBlock() ?>

<div class="manage-content">
    <!--新增编辑判断-->
    <input type="hidden" value="<?= Html::encode($isEdit)?>" id="is_edit"/> 
    <!--商品id-->
    <input type="hidden" value="<?= $isEdit ? Html::encode($data->id) : '' ?>" id="id"/>
    <!--是否上架-->
    <input type="hidden" value="<?= $isEdit ? Html::encode($data->is_recommend) : '' ?>" id="is_recommend"/>
    <input type="hidden" value="<?= $isEdit ? Html::encode($data->is_deleted) : '' ?>" id="is_deleted"/>
    <input type="hidden" value="<?= $isEdit ? Html::encode($data->type_id) : '' ?>" id="is_types"/>
    <input type="hidden" value="<?= $isEdit ? Html::encode($data->type_pid) : '' ?>" id="is_type"/>
    <input type="hidden" value="<?= $isEdit ? Html::encode($data->city_id) : '' ?>" id="is_citys"/>
    <input type="hidden" value="<?= $isEdit ? Html::encode($data->city_pid) : '' ?>" id="is_city"/>
    <input type="hidden" value="<?= $isEdit ? Html::encode($tsg) : '' ?>" id="is_tsg"/>
    <input type="hidden" value="<?= $isEdit ? Html::encode($data->latitudes) : '' ?>" id="is_lat"/>
    <input type="hidden" value="<?= $isEdit ? Html::encode($data->longitudes) : '' ?>" id="is_lng"/>
   <h4 class="padding manage-title">商家管理</h4>
    <!--判断新增商品 编辑商品-->
    <div class="padding title-bottom">
        <a href="<?= $this->context->createUrl("/merchant/merchant/index")?>" class="icon-merge icon-goback" title="返回上一层">返回上一层</a>
        <a href="<?= $this->context->createUrl("/merchant/merchant/index")?>" class="color-black">商家管理</a> / <span><?= $isEdit ? '<b>编辑商家</b>' : '<b>新增商家</b>' ?></span>
    </div>
    <div class="page-con clearfix"style="margin-top:30px">
        <!--商品封面图片-->
        <div class="fl left-box">
            <p class="color-gray" id="title"><?= $isEdit ? Html::encode($data->name) : '' ?></p>
            <div class="cover-box" id="cover_box">
                <?php if($isEdit){ ?>  
                    <img width="100%" height="100%" src="<?= Html::encode($data->logo) ?>">
                <?php }else{ ?> 
                    <span>封面图片</span>   
                <?php } ?>  
            </div>
        </div>
        <div class="fr right-form" id="form">
            <span class="icon-merge trigon"></span> 
            <!--商品名称 且长度不能超过60-->
            <div  class="text-wrap" style="position: relative;">
                <span>商家名称</span> 
                <input type="text" maxlength="60" required class="inp-short" id="goodsname" name="goodsname" value="<?= $isEdit ? Html::encode($data->name) : '' ?>" onkeyup="javascript:setShowLength(this, 60, 'name_maxlength');">
                <span id="name_maxlength" class="input_maxlength">0/60</span>             
            </div>          
            <div style="margin-bottom: 20px;">
                <dl class="inline-block" style="position: relative;padding-right:0px;">
                      <!--电话-->
                    <p class="title">联系人</p>
                    <div class="price">
                        <label style="font-weight: normal;"><input type="text" class="inp-pr1" name="linkman" id="linkman" value="<?= $isEdit ? Html::encode($data->linkman) : '' ?>" /></label>
                    </div>
                </dl>
                <dl class="inline-block" style="position: relative;padding-right:0px;">
                      <!--电话-->
                    <p class="title">电话</p>
                    <div class="price">
                        <label style="font-weight: normal;"><input type="text"  class="inp-pr1" name="linktel" id="linktel" value="<?= $isEdit ? Html::encode($data->linktel) : '' ?>" /></label>
                    </div>
                </dl>
                
            </div>
            <div style="margin-bottom: 20px;">
                <dl class="inline-block" style="position: relative;padding-right:0px;">
                      <!--传真-->
                    <p class="title">传真</p>
                    <div class="price">
                        <label style="font-weight: normal;"><input type="text" class="inp-pr1" name="fax" id="fax" value="<?= $isEdit ? Html::encode($data->fax) : '' ?>" /></label>
                    </div>
                </dl>
                <dl class="inline-block" id="" style="position: relative;">
                    <p class="title">邮箱</p>
                    <div class="price">
                        <label style="font-weight: normal;width:170px;"><input type="text"  class="inp-pr1" name="mail" id="mail" value="<?= $isEdit ? Html::encode($data->mail) : '' ?>" /></label>
                    </div>
                </dl>
            </div>    
            <!--商品分类-->
            <div style="margin-bottom: 20px;">
                
                <dl class="inline-block" id="typeid" style="position: relative;">
                    <p class="title">商家类型</p>
                    <select name="type" id="type" class="form-control" style="width:200px; height:30px; border:1px solid #CECECE;">
                        <option value="" ></option>
                        <?php foreach($type as $i=>$types) { ?>
                        <option  value="<?= $types['id'] ?>" data-code="<?= $types['code'] ?>"><?= $types['name'] ?></option>
                        <?php } ?> 
                    </select>
                    <p class="color-red1" style="display:none;">请填写商家类型</p>
                </dl>
                
                <dl class="inline-block" style="position: relative;top:22px;">
                    <!-- <span id="type_checkbox" style="display: none;margin-bottom: 20px;">
                        <p class="title">标签</p>
                        <?php foreach($tag as $i=>$tags) { ?>
                            <label  style="margin-right:10px;"><input name="types" class='check' type="checkbox" value="<?= $tags['id'] ?>" /><?= $tags['name'] ?></label>
                        <?php } ?> 
                    </span> -->
                    <select name="types" id="types" class="form-control" style="width:200px; height:30px; border:1px solid #CECECE;display: none;">
                        <script type="text/template" id="type_tmp">  
                            <option value=""></option>
                        <% for(var i=0;i<data.length;i++){ %>
                            <option value="<%= data[i].id %>"><%= data[i].name %></option>
                        <% } %>
                        
                        </script> 
                    </select>
                </dl>
            </div>
            <div>
                <dl class="inline-block" style="position: relative;">
                      <!--电话-->
                    <p class="title">序号</p>
                    <div class="price">
                        <?php $isEdit&&$sort=Html::encode($data->sort)?>
                        <label><input type="text" class="inp-pr1" name="sort" id="sort" value="<?php if($isEdit){if($sort=='1410065407'){ echo '';}else { echo $sort;}}else{echo '';}?>" onkeyup="(this.v=function(){this.value=this.value.replace(/\.\d$|[^\d.]/g,'');}).call(this)" onblur="this.v&amp;&amp;this.v()"/></label>
                    </div>
                </dl>
                <dl class="inline-block">
                    <!--立即上架-->                 
                    <p class="title">是否推荐</p>                       
                    <label><input type="radio" value="1" name="is_recommend" class="input-radio" style="background-color:#fff;" /><span class="check-text">&nbsp;&nbsp;是</span></label>
                    <label><input type="radio" value="0" name="is_recommend" class="input-radio" style="background-color:#fff;" /><span class="check-text">&nbsp;&nbsp;否</span></label>                  
                </dl>
                    <p>商家位置</p>
                    <div>
                        <input type="text" class="inp inp-pr inp-short" placeholder="经度" readonly="readonly" id="advert_longitudes" name="advert_longitudes" value="<?= $isEdit ? Html::encode($data->longitudes) : '' ?>">
                        <input type="text" class="inp inp-pr inp-short" placeholder="纬度" readonly="readonly"  id="advert_latitudes" name="advert_latitudes" value="<?= $isEdit ? Html::encode($data->latitudes) : '' ?>">
                        <a href="javascript:;" class="map td-u" id="select_map">从地图里选取</a>
                    </div>
                    <input type="text" class="inp inp-pr" placeholder="地址" id="advert_address" name="advert_address" value="<?= $isEdit ? Html::encode($data->address) : '' ?>" style="width:100%">
            </div>
            <div style="margin-bottom: 20px;">
                <dl class="inline-block" id="regions" style="position: relative;">
                    <p class="title">区域</p>
                    <select name="region" id="region" class="form-control" style="width:200px; height:30px; border:1px solid #CECECE;">
                        <option value=""></option>
                        <?php foreach($city as $i=>$citys) { ?>
                        <option  value="<?= $citys['id'] ?>"><?= $citys['name'] ?></option>
                        <?php } ?> 
                    </select>
                </dl>
                 <dl class="inline-block" style="position: relative;top:22px;">
                    <select name="town" id="town" class="form-control" style="width:200px; height:30px; border:1px solid #CECECE;display: none;">
                        <script type="text/template" id="town_tmp">  
                            <option value=""></option>
                        <% for(var i=0;i<data.length;i++){ %>
                            <option value="<%= data[i].id %>"><%= data[i].name %></option>
                        <% } %>
                        </script> 
                    </select>
                </dl>
            </div>
            <div  class="text-wrap" style="position: relative;">
                <span>简介</span> 
                <textarea id="summary" maxlength="150" cols="86" rows="3" style="border:1px solid #CECECE;"><?= $isEdit ? Html::encode($data->summary) : '' ?></textarea>          
            </div>
            <div  class="text-wrap" style="position: relative;">
                <p class="title">特别提醒</p>
                <textarea id="remind" cols="86" rows="3" style="border:1px solid #CECECE;"><?= $isEdit ? Html::encode($data->remind) : '' ?></textarea>            
            </div>
            <!--上传图片-->
            <div class="imgup-wrap" id="imgup_wrap">
                <p class="title">上传图片(<span id="up_num"><?= $isEdit ? Html::encode(count($photo)) : 0 ?>                                
                        </span>/60)<span class="color-gray"> (每张图片最大2M 支持jpg/gif/png)</span></p>
                <div class="img-wraps clearfix" id="img_wraps">
                     <?php if($isEdit){ ?>  
                        <?php foreach($photo as $i=>$photo) { ?>
                        <div class="img-wrap">
                            <div class="img-box">
                                <img src="<?=$photo['thumb_url'] ?>" alt="" class="js-img" _src="<?= Html::encode($photo['original_url']) ?>" />
                            </div>                      
                            <div class="opeate"> 
                                <span class="opt-btn f-btn">
                                    <span class="icon-merge icon"></span>
                                </span>
                                <span class="opt-btn d-btn" >
                                    <span class="icon-merge icon"></span>
                                </span>
                            </div>
                        </div>
                        <?php } ?> 
                    <?php }else{ ?>
                    <?php } ?> 
                </div>              
                <button class="btn-pr bg-white upload-btn" id="upload_btn">上传</button>
            </div>
            <!--商品介绍--> 
            <div class="text-wrap" id="goodsinfoname">
                <p>
                    <span class="name">商家详情</span> 
                </p>
                <div style="height:250px;" id="goodsinfo" class="ueedit-box"></div>
                <script type="text/template" class="js-detail" id="js-detail"><?= $isEdit ? Html::encode($data->content) : '' ?></script>                         
            </div>          
        </div>      
    </div>
    <div class="submit">
        <hr />
         <!--保存-->
            <button class="btn-pr bg-green color-white" id="submit_btn">保存</button>         
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
             <span class="opt-btn f-btn">
                <span class="icon-merge icon"></span>
            </span>
            <span class="opt-btn d-btn" >
                <span class="icon-merge icon"></span>
            </span>
        </div>
    </div>
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
<!--删除模板-->
<script type="text/template" id="de_pte_templ">
    <div class="tips-wrap">
        <div class="delete-info">确定删除？</div>
        <button class="btn-pr bg-green color-white js-delete-ue">确定</button>
        <button class="btn-pr bg-white fr cancel-btn">取消</button>
    </div>
</script>


<!-- 模板 -->
<?php $this->beginBlock('js') ?> 
<script type="text/javascript" src="/frontend/3rd/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="/frontend/3rd/ueditor/ueditor.all.min.js"></script>
<script type="text/javascript" src="/frontend/3rd/ueditor/lang/zh-cn/zh-cn.js"></script>
<script type="text/javascript" src="/frontend/3rd/plupload/plupload.full.min.js"></script>
<script type="text/javascript" src="/frontend/css/bootstrap/dist/js/bootstrap.js"></script>

<script type="text/javascript"> 
    seajs.use('/modules/js/merchant/merchant/add',function(add){
        add.init();
    });
</script>
<?php $this->endBlock() ?>








      
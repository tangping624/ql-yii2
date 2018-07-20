<?php
    use yii\helpers\Html;
    $this->title = Yii::$app->params['system_name'];
    $isEdit = true;
    if(count($data) == 0) {$isEdit = false;}
?>
<?php $this->beginBlock('css') ?>
    <!--页面样式代码-->
    <link href="/modules/css/global/public.css" rel="stylesheet">
    <link href="/modules/css/page/popup.css" rel="stylesheet">
    <style type="text/css">
        .color-red1{color: #e15f63 !important;}
    </style>
<?php $this->endBlock() ?>
    <input type="hidden" name="isEdit" id="isEdit" value="<?= $isEdit ?>">
    <div class="popup-container">
        <div class="fr right-form" id="form">
            <div class="popup-content">
                <div class="form-area">
                    <input type="hidden" name="parent_id" id="parent_id">
<!--                    <input type="hidden" name="fullcode" id="fullcode">-->
                    <div class="form-item">
                        <label class="form-field">名称</label>
                        <div class="form-tag-wrap">
                            <input type="text" class="form-control" id="name" name="name" value="<?= $isEdit ? Html::encode($data['name']) :''?>" style="width:75%;" />
                            <button class="selcity btn-pr" style="position:absolute;right:0;top:0;padding:4px 30px;background:#2f9833;color:#fff;">确定</button>
                        </div>
                    </div>
                    <div class="form-item">
                        <div class="form-tag-wrap clearfix" style="margin-left:0;">
                            <div style="float: left;">
                                <label class="form-field">经度</label>
                                <input type="text" class="form-control" placeholder="经度" id="lng" name="longitudes" value="<?= $isEdit ? Html::encode($data['longitudes']) :''?>" style="width:120px;margin: 0 33px 0 90px;" disabled="true">
                            </div>
                            <div style="float: left;">
                                <label class="form-field">纬度</label>
                                <input type="text" class="form-control" placeholder="纬度" id="lat" name="latitudes" value="<?= $isEdit ? Html::encode($data['latitudes']) :''?>" style="width:120px;margin-left:90px;" disabled="true">
                            </div>
                        </div>
                    </div>
                    <div class="form-item">
                        <p>位置</p>
                        <div id="l-map" style="height: 250px;"></div>
                    </div>
                    <div class="form-item" >
                        <p>
                            <span class="name" id="advert_productinofname">城市介绍</span>
                            <span class="icon-merge icon-edit"></span>
                            <span class="color-gray">（必填）</span>
                        </p>
                        <div style="height:250px;" id="advert_productinof" class="ueedit-box" ></div>
                        <script type="text/template" class="js-detail" id="js-detail"><?= $isEdit ? Html::encode($data['content']) :''?></script>
                    </div>
                </div>
            </div>
            <div class="form-bottom align-c">
                <button type="button" class="btn-pr ok-btn" id="submit_btn" data-id="<?= $isEdit ? Html::encode($data['id']) :''?>">保存</button>
                <button type="button" class="btn-pr sub-btn" id="cancel">关闭</button>
            </div>
        </div>
    </div>
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
<?php $this->beginBlock('js') ?>
    <!--页面js代码-->
    <script type="text/javascript" src="/frontend/3rd/ueditor/ueditor.config.js"></script>
    <script type="text/javascript" src="/frontend/3rd/ueditor/ueditor.all.min.js"></script>
    <script type="text/javascript" src="/frontend/3rd/ueditor/lang/zh-cn/zh-cn.js"></script>
    <script type="text/javascript" src="/modules/js/public/public.js"></script>
   <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAWxxGyOJkLnC6MSF67Woxn015idn1dFgo"
    ></script>
    <script type="text/javascript">
        seajs.use('/modules/js/city/city/add',function(index){
            index.init();
        });
    </script>
<?php $this->endBlock() ?>
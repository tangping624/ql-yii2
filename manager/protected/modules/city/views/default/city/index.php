<?php 
    $this->title = Yii::$app->params['system_name'];
?>

<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/global.css"/>
<link href="/modules/css/global/public.css" rel="stylesheet">
<link href="/modules/css/page/page.css" rel="stylesheet"> 
<style> 
    .manage-container{
        margin-top:36px;
    }
    .js-tree .tree-text{
        max-width: 210px;
        white-space:nowrap; 
        text-overflow:ellipsis; 
        -o-text-overflow:ellipsis;
        overflow:hidden;
    }
</style>
<?php $this->endBlock() ?>

    <div class="manage-content">
        <div class="manage-main">
            <div class="public-panel organization-framework">
                <div class="pp-side">
                    <h4 class="public-tit">
                        区域城市管理
                        <span class="fonticon fonticon-plus add" mode="add" data-title="添加城市" id="addType"></span>
                    </h4>
                    <div class="js-tree">

                    </div>
                </div>
                <div class="pp-main">
                    <div class="public-tit"></div>

                    <div class="grid" id="user_grid">
                        
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script type="text/template" id="grid_template">
        <div class="popup-container">
            <div class="fr" id="form">
                <div class="popup-content">
                    <div class="form-area">
                        <input type="hidden" name="parent_id" id="parent_id">
                        <input type="hidden" name="fullcode" id="fullcode">
                        <div class="form-item clearfix">
                            <p class="fl">名称</p>
                            <div class="form-tag-wrap" style="margin-left: 50px;">
                                <input type="hidden" class="form-control" id="name" name="name" value="<%- list.name %>"/>
                                <p><%- list.name %></p>
                            </div>
                        </div>
                        <div class="form-item">
                        <div class="form-tag-wrap clearfix" style="margin-left:0;">
                            <div style="float: left;">
                                <p class="fl">经度</p>
                                <div class="form-tag-wrap" style="margin-left: 50px;">
                                    <input type="hidden" id="lng" name="longitudes" value="<%- list.longitudes %>">
                                    <input type="hidden" class="form-control" value="<%- list.longitudes %>"/>
                                    <p><%- list.longitudes %></p>
                                </div>
                            </div>
                            <div style="float: left;margin-left:50px;">
                                <p class="fl">纬度</p>
                                <div class="form-tag-wrap" style="margin-left: 50px;">
                                    <input type="hidden" id="lat" name="latitudes" value="<%- list.latitudes %>">
                                    <input type="hidden" class="form-control" value="<%- list.longitudes %>"/>
                                    <p><%- list.latitudes %></p>
                                </div>
                            </div>
                        </div>
                    </div>
                        <div class="form-item clearfix">
                            <p class="fl">位置</p>
                            <div>
                                <input type="hidden" class="inp inp-pr inp-short" placeholder="经度" id="lng" name="longitudes" disabled="true" value="<%- list.longitudes %>">
                                <input type="hidden" class="inp inp-pr inp-short" placeholder="纬度" id="lat" name="latitudes" disabled="true" value="<%- list.latitudes %>">
                            </div>
                            <div id="l-map" style="height: 250px;margin-left: 50px;width:472px;"></div>
                        </div>
                        <div class="form-item clearfix">
                            <p class="fl">
                                <span class="name" id="advert_productinofname">简介<span>
                            </p>
                            <div class="ueedit-box" style="margin-left: 50px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>    
    </script>
<?php $this->beginBlock('js') ?>
   <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAWxxGyOJkLnC6MSF67Woxn015idn1dFgo"
    ></script>
    <script>
        __REQUIRE('/modules/js/city/city/index');
        // seajs.use('/modules/js/missions/officers/index',function(index){
        //     index.init();
        // });
    </script>
<?php $this->endBlock() ?>
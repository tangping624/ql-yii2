<?php 
    $this->title = Yii::$app->params['system_name'];
?>
         
<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/wechat/manage/wechat.min.css?v=2c80139ac6" />
<?php $this->endBlock() ?>
<?php
    $account=$data['account'];
    $selected_apps=$data['selected_apps'];
    $corp_id=$data['corp_id'];
?>
<div class="manage-content">
    <div class="padding mb40 border-bottom">
        <h4>添加公众号</h4>
    </div>
    <div class="padding">
        <div class="step-wrap step-one">
            <p class="step-1"><span>第一步：填写公众号基本信息</span></p>
            <p class="step-2"><span>第二步：在微信公众平台填入配置信息</span></p>
        </div>
        <form class="form-align form form-base form-horizontal add-organization" id="organization_form">
            <div class="form-content">
                <input type="hidden" id="id" name="id" value="<?=$account['id']?>">
                <div class="form-group">
                    <label for="corp_id" class="col-md-3 form-label">所属组织</label>
                    <div class="col-md-9">
                        <select id="corp_id" class="form-control" name="corp_id" <?=$corp_id&&$corp_id!=''?' disabled':'' ?>>
                            <?php  foreach ($data["orgs"] as $org) { ?>
                                <option value="<?=$org['id']?>" <?=$account['corp_id']==$org['id']||$corp_id==$org['id']?'selected="selected"':''?>><?=$org['name']?></option>
                            <?php }?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="name" class="col-md-3 form-label">公众号名称</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" placeholder="" id="name" name="name" value="<?=$account['name']?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="original_id" class="col-md-3 form-label">原始ID</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" placeholder="" id="original_id" name="original_id" value="<?=$account['original_id']?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="wechat_number" class="col-md-3 form-label">微信号</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" placeholder="" id="wechat_number" name="wechat_number" value="<?=$account['wechat_number']?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="type" class="col-md-3 form-label">公众号类型</label>
                    <div class="col-md-9">
                        <select class="form-control" name="type" id="type">
                            <option value="服务号" <?=$account['type']=='服务号'?'selected="selected"':''?>>服务号</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="app_id" class="col-md-3 form-label">AppID</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" placeholder="" id="app_id" name="app_id" value="<?=$account['app_id']?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="app_secret" class="col-md-3 form-label">AppSecret</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" placeholder="" id="app_secret" name="app_secret"  value="<?=$account['app_secret']?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="mch_id" class="col-md-3 form-label">商户号</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" placeholder="" id="mch_id" name="mch_id"  value="<?=$account['mch_id']?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="mch_key" class="col-md-3 form-label">商户密钥</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" placeholder="" id="mch_key" name="mch_key"  value="<?=$account['mch_key']?>">
                    </div>
                </div>
                <!--
                <div class="form-group">
                    <label for="apps" class="col-md-3 form-label">公众号绑定的应用</label>
                    <div class="col-md-9 apps-list">
                        <?php
                                $app_list=array();
                                foreach ($data["apps"] as $app) {
                                $selected=in_array($app['app_code'],$selected_apps);
                                if($selected){
                                    $app_list[]=array('id'=>$app['id'],'app_code'=>$app['app_code']);
                                }
                            ?>
                            <label class="form-checkbox<?=$selected?' selected':''?>">
                                <i class="icon-checkbox"></i>
                                <span class="align-m"><?=$app['app_name']?></span>
                                <input type="checkbox" class="form-checkbox-input" value="<?=$app['id']?>" code="<?=$app['app_code']?>"  name="apps" <?=$selected?'checked="checked"':''?>>
                            </label>
                        <?php }?>
                        <input type="hidden" name="app_list" id="app_list" value='<?=json_encode($app_list)?>' />
                    </div>
                </div>
                -->
            </div>
            
            <div class="form-bottom align-c">
                <button type="button" class="btn-pr ok-btn" id="save_btn">下一步</button>
            </div>
        </form>
    </div>
</div>

<?php $this->beginBlock('js') ?> 
<script type="text/javascript" src="/modules/js/wechat/manage/add.js" flag="build"></script> 
<?php $this->endBlock() ?>
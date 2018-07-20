<?php
$this->title = Yii::$app->params['system_name'];
$request = \Yii::$app->request;
$is_auth = $request->get('auth', $request->get('amp;auth', $request->get('amp;amp;auth')))==1;
$corp_id = $request->get('corp_id', $request->get('amp;corp_id', $request->get('amp;amp;corp_id')));
$is_group = count($orgs)>0;
?>
<?php $this->beginBlock('css') ?>
    <link rel="stylesheet" href="/modules/css/wechat/account/wechat-auth.min.css?v=d3bc49bfe6"/>
<?php $this->endBlock() ?>

<div class="manage-content">
    <div class="padding mb30 border-bottom">
        <h4 class="manage-title">公众号管理</h4>
    </div>
    <div class="padding mb30">
        <div class="breadcrumbs">
            <a href="<?=$this->context->createUrl('/wechat/account/index')?>" class="icon-merge icon-goback" title="返回上一层">返回上一层</a>
            <a href="<?=$this->context->createUrl('/wechat/account/index')?>" class="parent">公众号管理</a> /
            <span>添加绑定</span>
        </div>
    </div>
    <div class="padding">
        <form class="form-align form form-base form-horizontal" id="form">
            <input type="hidden" id="id" name="id" value="<?=$id?>">
            <input type="hidden" id="is_group" value="<?=$is_group?1:0?>">
            <div class="form-group<?=$is_group?'':' hide'?>">
                <label for="corp_id" class="col-md-2 form-label">所属组织</label>
                <div class="col-md-10">
                    <select id="corp_id" class="form-control invisible" style="width: 250px;" name="corp_id">
                        <?php
                        foreach ($orgs as $org) { ?>
                            <option value="<?=$org['id']?>"<?=($corp_id && $corp_id==$org['id']) || (!$corp_id && $data['corp_id']==$org['id'])?' selected="selected"':''?>><?=$org['name']?></option>
                            <?php
                        }?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <?php
                $is_bind = !empty($data);
                ?>
                <label for="name" class="col-md-2 form-label"><?=$is_bind?'基础信息':'一键绑定'?></label>
                <div class="col-md-10">
                    <div class="clearfix">
                        <?php
                        if ($is_bind) {?>
                            <div class="clearfix" id="bind_info">
                                <div class="form-group">
                                    <label class="col-md-2 form-label">头像</label>
                                    <div class="col-md-10 gray"><img src="<?=$data['headimg_url']?>" width="80" height="80"></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 form-label">公众号名称</label>
                                    <div class="col-md-10 gray"><?=$data['name']?></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 form-label">公众号原始ID</label>
                                    <div class="col-md-10 gray"><?=$data['original_id']?></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 form-label">微信号</label>
                                    <div class="col-md-10 gray"><?=$data['wechat_number']?></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 form-label">公众号类型</label>
                                    <div class="col-md-10 gray"><?=$data['type']?></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 form-label">AppID</label>
                                    <div class="col-md-10 gray"><?=$data['app_id']?></div>
                                </div>
                            </div>
                        <?php
                        }?>
                        <input type="hidden" id="is_bind" name="is_bind" value="<?=$is_bind?'1':'0'?>">
                        <a href="javascript:;" class="icon-login" id="bind"></a>
                    </div>
                    <div class="clearfix padT10">
                        <label class="form-checkbox<?=$is_bind?' selected':''?>">
                            <i class="icon-checkbox"></i>
                            <span class="align-m">已阅提示 温馨提示：请使用服务号，订阅号无法使用云服务完整的功能</span>
                            <input type="checkbox" class="form-checkbox-input" id="is_read" name="is_read">
                        </label>
                    </div>
                    <p class="gray padT10">
                        您将进入腾讯的微信公众号登录授权页面，您需要输入微信公众号账号密码，并需要管理员以扫码的方式授权登录，将微信公众号授权给明源云服务平台，完成一键配置。
                    </p>
                    <p class="gray padT10">
                        <span class="notice-color">*</span> 只有将全部接口和功能授权给明源云服务平台，才能完整使用平台功能。
                    </p>
                    <p class="gray padT10">
                        <span class="notice-color">*</span> 如果要取消授权，请到<a href="https://mp.weixin.qq.com/" target="_blank">微信后台开发者中心</a>进行取消
                    </p>
                </div>
            </div>
            <div class="form-bottom<?=!$is_auth || empty($data) ? ' hide' : ''?>">
                <button type="button" class="btn btn-primary" id="submit_btn">确认授权</button>
            </div>
        </form>
    </div>
</div>
<?php $this->beginBlock('js') ?>
    <script type="text/javascript">
    
        __REQUIRE('/modules/js/wechat/account/wechat-auth.js?v=759fe1fce5');
    </script>
<?php $this->endBlock() ?>
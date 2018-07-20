<?php 
    $this->title = Yii::$app->params['system_name'];
?>

<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/wechat/manage/wechat.min.css?v=2c80139ac6" />
<?php $this->endBlock() ?>

<div class="manage-content">
    <div class="padding mb40 border-bottom">
        <h4>公众号配置</h4>
    </div>
    <div class="padding">
        <div class="breadcrumbs">
            <a href="<?= $this->context->createUrl("wechat/manage/list") ?>" class="icon-merge icon-goback" title="返回上一层">返回上一层</a>
            <a href="<?= $this->context->createUrl("wechat/manage/list") ?>" class="parent">公众号列表</a> /
            <span>公众号配置</span>
        </div>
    </div>
    <div class="padding form" id="config_form">
        <div class="panel panel-default panel-reset">
            <div class="panel-heading">基础信息</div>
            <table class="table">
                <tr>
                    <td width="100">所属组织</td>
                    <td>
                        <span class="text"><?=$data['corp_name']?></span>
                        <div class="edit hide">
                            <select class="form-control form-select-control" data-column="corp_id">
                                <?php foreach ($data["orgs"] as $org) { ?>
                                    <option value="<?=$org['id']?>" <?php if($org['name']==$data['corp_name']){ ?>selected<?php } ?>><?=$org['name']?></option>
                                <?php }?>
                            </select>
                        </div>
                    </td>
                    <td width="60"><?=$data['corp_id']==''?'<a href="javascript:;" class="edit-btn">修改</a>':''?></td>
                </tr>
                <tr>
                    <td>公众号名称</td>
                    <td>
                        <span class="text"><?=$data['name']?></span>
                        <div class="edit hide">
                            <input type="text" class="form-control form-text-control" value="<?=$data['name']?>" data-value="<?=$data['name']?>" data-column="name">
                        </div>
                    </td>
                    <td><a href="javascript:;" class="edit-btn">修改</a></td>
                </tr>
                <tr>
                    <td>原始ID</td>
                    <td>
                        <span class="text"><?=$data['original_id']?></span>
                        <div class="edit hide">
                            <input type="text" class="form-control form-text-control" value="<?=$data['original_id']?>" data-value="<?=$data['original_id']?>" data-column="original_id">
                        </div>
                    </td>
                    <td><a href="javascript:;" class="edit-btn">修改</a></td>
                </tr>
                <tr>
                    <td>微信号</td>
                    <td>
                        <span class="text"><?=$data['wechat_number']?></span>
                        <div class="edit hide">
                            <input type="text" class="form-control form-text-control" value="<?=$data['wechat_number']?>" data-value="<?=$data['wechat_number']?>" data-column="wechat_number">
                        </div>
                    </td>
                    <td><a href="javascript:;" class="edit-btn">修改</a></td>
                </tr>
                <tr>
                    <td>微信号类型</td>
                    <td>
                        <span class="text"><?=$data['type']?></span>
                        <div class="edit hide">
                            <select class="form-control form-select-control" data-column="type">
                                <option value="服务号" <?php if($data['type']=='服务号'){ ?>selected<?php } ?>>服务号</option>
                            </select>
                        </div>
                    </td>
                    <td><a href="javascript:;" class="edit-btn">修改</a></td>
                </tr>
                <tr>
                    <td>AppID</td>
                    <td>
                        <span class="text"><?=$data['app_id']?></span>
                        <div class="edit hide">
                            <input type="text" class="form-control form-text-control" value="<?=$data['app_id']?>" data-value="<?=$data['app_id']?>" data-column="app_id">
                        </div>
                    </td>
                    <td><a href="javascript:;" class="edit-btn">修改</a></td>
                </tr>
                <tr>
                    <td>AppSecret</td>
                    <td>
                        <span class="text"><?=$data['app_secret']?></span>
                        <div class="edit hide">
                            <input type="text" class="form-control form-text-control" value="<?=$data['app_secret']?>" data-value="<?=$data['app_secret']?>" data-column="app_secret">
                        </div>
                    </td>
                    <td><a href="javascript:;" class="edit-btn">修改</a></td>
                </tr>
                <tr>
                    <td>商户号</td>
                    <td>
                        <span class="text"><?=$data['mch_id']?></span>
                        <div class="edit hide">
                            <input type="text" class="form-control form-text-control" value="<?=$data['mch_id']?>" data-value="<?=$data['mch_id']?>" data-column="mch_id">
                        </div>
                    </td>
                    <td><a href="javascript:;" class="edit-btn">修改</a></td>
                </tr>
                <tr>
                    <td>商户密钥</td>
                    <td>
                        <span class="text"><?=$data['mch_key']?></span>
                        <div class="edit hide">
                            <input type="text" class="form-control form-text-control" value="<?=$data['mch_key']?>" data-value="<?=$data['mch_key']?>" data-column="mch_key">
                        </div>
                    </td>
                    <td><a href="javascript:;" class="edit-btn">修改</a></td>
                </tr>
                <tr>
                    <td>Api证书</td>
                    <td>
                        <span class="text1"><?=$data['mch_ssl_cert']?'已导入':'未导入'?></span>
                    </td>
                    <td><a href="javascript:;" class="edit-btn" id="import_cert">导入</a></td>
                </tr>
                <tr>
                    <td>Api密钥</td>
                    <td>
                        <span class="text1"><?=$data['mch_ssl_key']?'已导入':'未导入'?></span>
                    </td>
                    <td><a href="javascript:;" class="edit-btn" id="import_key">导入</a></td>
                </tr>
            </table>
        </div>
        <!--
        <div class="panel panel-default panel-reset">
            <div class="panel-heading">已绑定应用</div>
            <table class="table">
                <tr>
                    <td>
                        <span class="text">
                            <?php if(count($data["selected_apps"])>0){ ?>
                                <?php 
                                    $select_apps ='';
                                    foreach ($data["selected_apps"] as $app) { $select_apps.=$app['app_name'].',';
                                ?>
                                    <?=$app['app_name']?>&nbsp;&nbsp;
                                <?php }?>
                            <?php }else{ ?>
                                    <p class="color-gray">暂无应用</p>
                            <?php }?>
                            <input type="hidden" value="<?=$select_apps?>" id="select_apps" />
                        </span>
                        <div class="edit apps-list hide">
                            <?php  foreach ($data["apps"] as $app) { ?>
                            <label class="form-checkbox">
                                    <i class="icon-checkbox"></i>
                                    <span class="align-m"><?=$app['app_name']?></span>
                                    <input type="checkbox" class="form-checkbox-input" value="<?=$app['id']?>" code="<?=$app['app_code']?>"  name="apps">
                                </label>
                            <?php }?>
                            <input type="hidden" name="app_list" id="app_list" value="[]"/>
                        </div>
                    </td>
                    <td width="60"><a href="javascript:;" class="edit-btn appset-btn" isEdit="false">设置</a></td>
                </tr>
            </table>
        </div>
        -->
        <div class="panel panel-default panel-reset">
            <div class="panel-heading">接口配置<a class="wechat-link float-right" href="https://mp.weixin.qq.com/" target="_blank">进入微信公众平台配置</a></div>
            <table class="table">
                <tr>
                    <td width="100">接口API</td>
                    <td class="copytext"><?=$data['wechat_api_url']?></td>
                    <td width="60"><a href="javascript:;" class="copy-button" id="copy_api">复制</a></td>
                </tr>
                <tr>
                    <td>Token</td>
                    <td class="copytext"><?=$data['token']?></td>
                    <td><a href="javascript:;" class="copy-button" id="copy_token">复制</a></td>
                </tr>
                <tr>
                    <td>授权域名</td>
                    <td class="copytext"><?=$data['wechat_domain']?></td>
                    <td><a href="javascript:;" class="copy-button" id="copy_domain">复制</a></td>
                </tr>
                <?php
                $wechat_js_domain=$data['wechat_js_domain'];
                $wechat_js_domain_count=count($wechat_js_domain);
                for($i=0;$i<$wechat_js_domain_count;$i++){
                    if(!isset($wechat_js_domain[$i]) || $wechat_js_domain[$i]=='')continue;
                    ?>
                    <tr>
                        <td><?=$i==0?'JS域名':''?></td>
                        <td class="copytext"><?=$wechat_js_domain[$i]?></td>
                        <td><a href="javascript:;" class="copy-button" id="copy_domain">复制</a></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>

<?php $this->beginBlock('js') ?>
<script type="text/javascript" src="/frontend/3rd/plupload/plupload.full.min.js"></script>
<script type="text/javascript" src="/modules/js/wechat/manage/index.js?v=20150910"></script>
<script type="text/javascript" src="/modules/js/wechat/manage/index-key.js?v=20150910"></script>
<script type="text/javascript" src="/modules/js/wechat/manage/config.js" flag="build"></script>
<!--<script type="text/javascript" src="/frontend/3rd/zeroclipboard/2/ZeroClipboard.js"></script>-->
<script type="text/javascript">
        $(function(){
            /*ZeroClipboard.setMoviePath('/frontend/3rd/zeroclipboard/2/ZeroClipboard.swf');
            $('.copy-button').each(function(){
                var $this = $(this);
                var clip = new ZeroClipboard.Client();
                clip.setHandCursor(true);
                clip.addEventListener("mouseOver", function(){
                    clip.setText($this.closest('tr').find('.copytext').html());
                });
                clip.addEventListener("mouseUp", function(){
                    $.topTips({tip_text:'复制成功'})
                });
                clip.glue($(this).attr('id'));
            });*/

//            ZeroClipboard.config({ swfPath: "/frontend/3rd/zeroclipboard/2/ZeroClipboard.swf" });

//            var client = new ZeroClipboard($(".copy-button"));
//
//            client.on( 'ready', function() {
//                client.on( 'copy', function(event) {
//                    event.clipboardData.setData('text/plain', $(event.target).closest('tr').find('.copytext').html());
//                } );
//                client.on( 'aftercopy', function() {
//                    $.topTips({tip_text:'复制成功'})
//                } );
//            } );
//
//            client.on( 'error', function() {
//                ZeroClipboard.destroy();
//            } );
        })
    </script>
<?php $this->endBlock() ?>
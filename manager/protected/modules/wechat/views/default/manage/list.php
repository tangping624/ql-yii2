<?php
$this->title = Yii::$app->params['system_name'];
?>
<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/wechat/manage/wechat.min.css?v=2c80139ac6"/>
<?php $this->endBlock() ?>
<?php
$corp_list = $data['corp_list'];
?>

<div class="manage-content">
    <div class="padding mb40 border-bottom">
        <h4>公众号列表</h4>
    </div>
    <div class="padding">
        <div class="row">
            <div class="btn-add-area">
                <a href="<?=$this->context->createUrl('/wechat/manage/add')?>" class="btn-add">添加公众号</a>
            </div>
        </div>
        <div class="list-wrap">
            <?php for ($i = 0; $i < count($corp_list); $i++) {
                $wechat_data = $corp_list[$i]['wechat_data'];
                $wechat_count = count($wechat_data);
                if ($wechat_count === 0) continue;
                ?>
                <dl class="<?= $i === 0 ? 'group' : 'corp' ?>">
                    <dt class="title"><?= $corp_list[$i]['name'] ?></dt>
                    <?php foreach ($wechat_data as $data_item) {
                        $app_data = $data_item['selected_apps'];
                        $app_count = count($app_data);
                        ?>
                        <dd>
                            <div class="row mp-num">
                                <div class="mp-name col-md-3">
                                    <div class="name-wrap">
                                        <!-- 临时固定图标 -->
                                        <div class="avatar"></div>
                                        <div><?= $data_item['name'] ?></div>
                                        <div class="num-type"><?= $data_item['type'] ?></div>
                                    </div>
                                </div>
                                <div class="mp-app col-md-9">
                                    <div class="app-wrap clearfix">
                                        <div class="split-line">
                                            <div class="line-bg"></div>
                                        </div>
                                        <?php if ($app_count === 0) { ?>
                                            <div class="empty-app"></div>
                                        <?php } else {
                                            foreach ($app_data as $app_item) {
                                                ?>
                                                <div class="col-md-3 app-area">
                                                    <!-- 临时固定图标 -->
                                                    <?php if($app_item['icon_url']!=null&&$app_item['icon_url']!=''){?>
                                                        <img src="<?=$app_item['icon_url']?>" width="80" height="80">
                                                    <?php }else{?>
                                                        <!--<div class="app-icon"></div>-->
                                                        <img class="app-icon" src="/modules/images/global/<?=$app_item['app_name']=='会员中心'?'vip':'serv'?>.png" width="80" height="80">
                                                    <?php }?>
                                                    <a class="app-name" href="javascript:;"><?=$app_item['app_name']?></a>
                                                </div>
                                            <?php }
                                        } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row option-area">
                                <div class="button-area">
                                    <a class="cfg_wechat" href="<?=$this->context->createUrl('/wechat/manage/config?id='.$data_item['id'])?>">公众号配置</a>
                                    <a class="del_wechat" data-id="<?=$data_item['id']?>" href="javascript:;">删除</a>
                                </div>
                            </div>
                        </dd>
                    <?php } ?>
                </dl>
            <?php } ?>
        </div>
    </div>
</div>
<script type="text/template" id="del_wechat_tpl">
    <div class="tips-wrap">
        <div class="content">确定删除？</div>
        <div class="clearfix">
            <button type="button" class="btn-pr ok-btn pull-left">确定</button>
            <button type="button" class="btn-pr cancel-btn pull-right">取消</button>
        </div>
    </div>
</script>
<?php $this->beginBlock('js') ?>
    <script type="text/javascript" src="/modules/js/wechat/manage/list.js" flag="build"></script>
<?php $this->endBlock() ?>
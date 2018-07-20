<?php
$this->title = Yii::$app->params['system_name']; 

?>
<?php $this->beginBlock('css') ?>
<link rel="stylesheet" href="/modules/css/basic/parameter/index.min.css?v=5b9997cef9"/>
<?php $this->endBlock() ?>
<div class="manage-content">
    <h4 class="padding manage-title border-bottom mb30">参数设置</h4>
    <div class="padding form parameter-form">
        <?php
        if (isset($paramGroup) === false || count($paramGroup) === 0) {
            ?>
            <div class="gray">未找到相关参数</div>
            <?php
        } else {
            $isOutPutAutoCodeParam = false;//特殊控制自动编码设置类型只输出一个参数设置项
            foreach ($paramGroup as $groupName => $paramArray) {
                ?>
                <div class="panel panel-default panel-reset">
                    <div class="panel-heading"><?= $groupName ?></div>
                    <table class="table">
                        <?php
                        foreach ($paramArray as $param) {
                            //创建设置参数的URL
                            $url = 'javascript:;';
                            if (!empty($param['customized_url'])) {
                                if ($param['type'] == '单值') {
                                    $url = $this->context->createUrl($param['customized_url'], ['name'=>$param['name']], true, false, true);
                                } else {
                                    $url = $this->context->createUrl($param['customized_url'], [], true, false, true);
                                } 
                            }
                            if ($param['type'] === '自动编码') {
                                if ($isOutPutAutoCodeParam) {
                                    continue;
                                } else {
                                    //输出固定参数
                                    $isOutPutAutoCodeParam = true;
                                    ?>
                                    <tr>
                                        <td>自动编码设置</td>
                                        <td class="param-option"><a href="<?= $url ?>">设置</a></td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                if ($param['name']=='积分规则'&&!\Yii::$app->context->hasMemberCenter) {
                                    continue;
                                }
                                ?>
                                <tr>
                                    <td><?= $param['name'] ?></td>
                                    <td class="param-option"><a href="<?= $url ?>">设置</a></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </table>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>
<?php $this->beginBlock('js') ?>
<script type="text/javascript">
    __REQUIRE('/modules/js/basic/parameter/index.js?v=1c4796acb2');
</script>
<?php $this->endBlock() ?>

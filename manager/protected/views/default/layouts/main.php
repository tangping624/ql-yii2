<?php 
use app\framework\biz\Common;
use app\framework\biz\cache\UserGroupRightsCacheManager;  
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="prefix" content="<?= Yii::$app->response->getHeaders()->get('prefix') ?>">
    <title><?= Yii::$app->params['system_name'] ?></title>
    <link rel="icon" href="http://carlife.oss-cn-beijing.aliyuncs.com/root/icon/clogo.ico"
          otype="image/x-icon"/>
    <link rel="shortcut icon" href="http://carlife.oss-cn-beijing.aliyuncs.com/root/icon/clogo.ico"
          type="image/x-icon"/>
    <link href="/frontend/css/bootstrap/dist/css/bootstrap.min.css?v=b547efe9b3" rel="stylesheet">
    <style>
        .manage-menu .only-item{cursor: pointer;}
        .manage-header h3.logo{background:url(/modules/images/cy963.png) no-repeat left center !important;background-size:150px !important;}
        .only-item{margin-left: 10px;}
    </style>
    <?php
    if (isset($this->blocks['css'])) {
        ?>
        <?= $this->blocks['css'] ?>
    <?php
    }?>
    <?= $this->head() ?>
    <script type="text/javascript">window.ENV = '<?= YII_ENV ?>';</script>
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/frontend/js/lib/compatible.js"></script>
    <![endif]-->
</head>
<body>
<?php $this->beginBody() ?>

<!-- 顶部 -->
<div class="manage-header">
    <div class="container-fixed clearfix">
        <h3 class="logo pull-left" style=""><?= Yii::$app->params['system_name'] ?></h3>
        <div class="user pull-right header_user">
            <?php
             $loginInfo = Common::getLoginInfo();
            ?> 
            <div class="user_right">  
                <span class="mr14"><?= $loginInfo['userName'] ?></span>
                <span class="mr14">|</span>
                <a href="/auth/logout">退出</a>
            </div>
        </div>
    </div>
</div>

<!-- 容器 [[-->
<div class="container-fixed">
    <div class="manage-container">
 <!-- 菜单 [[-->
        <div class="manage-menu account">
            <dl>
                <dt class="only-item">
                    <span class="module-icon module-align" aria-hidden="true"></span>
                    <span class="align-m">系统设置</span>
                </dt>
                <div class="menu_box" style="display: none;">
                    <dd class="js-menu-item">
                        <a href="/system/user/index">用户管理</a>
                    </dd>
                    <dd class="js-menu-item" style="display:none;">
                        <a href="/system/account/config">公众号配置</a>
                    </dd>
                    <dd class="js-menu-item">
                        <a href="/city/city/index">区域城市管理</a>
                    </dd>
                    <dd class="js-menu-item">
                        <a href="/type/type/index">分类设置</a>
                    </dd>
                    <dd class="js-menu-item">
                        <a href="/merchant/merchant/index">商家管理</a>
                    </dd>
                    <dd class="js-menu-item">
                        <a href="/advertise/advert/index">广告管理</a>
                    </dd>
                </div>

                <dt class="only-item">
                    <span class="module-icon align-m" aria-hidden="true"></span>
                    <span class="align-m">会员</span>
                </dt>
                <div class="menu_box" style="display: none;">
                    <dd class="js-menu-item">
                        <a href="/member/member/index">会员管理</a>
                    </dd>
                </div>

                <!---->
                <dt class="only-item">
                    <span class="module-icon align-m" aria-hidden="true"></span>
                    <span class="align-m">新鲜事</span>
                </dt>
                <div class="menu_box" style="display: none;">
                    <dd class="js-menu-item">
                        <a href="/news/news/index">新鲜事管理</a>
                    </dd>
                </div>
                <dt class="only-item">
                    <span class="module-icon align-m" aria-hidden="true"></span>
                    <span class="align-m">百科</span>
                </dt>
                <div class="menu_box" style="display: none;">
                    <dd class="js-menu-item">
                        <a href="/baike/bai-ke/index">分类设置</a>
                    </dd>
                    <dd class="js-menu-item">
                        <a href="/baike/manage/index">百科管理</a>
                    </dd>
                </div>
                <dt class="only-item">
                    <span class="module-icon align-m" aria-hidden="true"></span>
                    <span class="align-m">紧急</span>
                </dt>
                <div class="menu_box" style="display: none;">
                    <dd class="js-menu-item">
                        <a href="/baike/emergency/shop-type?app_code=urgent">产品分类</a>
                    </dd>
                    <dd class="js-menu-item">
                        <a href="/baike/emergency/index?app_code=urgent">产品管理</a>
                    </dd>
                </div>
                <!---->
                <dt class="only-item">
                    <span class="module-icon align-m" aria-hidden="true"></span>
                    <span class="align-m">游说</span>
                </dt>
                <div class="menu_box" style="display: none;">
                    <dd class="js-menu-item">
                        <a href="/lobby/lobby/index">游说管理</a>
                    </dd>
                </div>
                
                <!---->
                
                <dt class="only-item">
                    <span class="module-icon align-m" aria-hidden="true"></span>
                    <span class="align-m">房产</span>
                </dt>
                <div class="menu_box" style="display: none;">
                    <dd class="js-menu-item">
                        <a href="/house/house/shop-type?app_code=house">产品分类</a>
                    </dd>
                    <dd class="js-menu-item">
                        <a href="/house/house/index?app_code=house">产品管理</a>
                    </dd>
                </div>
                <dt class="only-item">
                    <span class="module-icon align-m" aria-hidden="true"></span>
                    <span class="align-m">购物惠</span>
                </dt>
                <div class="menu_box" style="display: none;">
                    <dd class="js-menu-item">
                        <a href="/shop/type/index?app_code=shop">商品分类</a>
                    </dd>
                    <dd class="js-menu-item">
                        <a href="/shop/shop/index?app_code=shop">商品管理</a>
                    </dd>
                </div>
                <dt class="only-item">
                    <span class="module-icon align-m" aria-hidden="true"></span>
                    <span class="align-m">旅游</span>
                </dt>
                <div class="menu_box" style="display: none;">
                    <dd class="js-menu-item">
                        <a href="/tour/tour/shop-type?app_code=tour">产品分类</a>
                    </dd>
                    <dd class="js-menu-item">
                        <a href="/tour/tour/tour-shop?app_code=tour">产品管理</a>
                    </dd>
                </div>
                <dt class="only-item">
                    <span class="module-icon align-m" aria-hidden="true"></span>
                    <span class="align-m">投资项目</span>
                </dt>
                <div class="menu_box" style="display: none;">
                    <dd class="js-menu-item">
                        <a href="/invest/invest/shop-type?app_code=invest">产品分类</a>
                    </dd>
                    <dd class="js-menu-item">
                        <a href="/invest/invest/invest-shop?app_code=invest">产品管理</a>
                    </dd>
                </div>
                <dt class="only-item">
                    <span class="module-icon align-m" aria-hidden="true"></span>
                    <span class="align-m">合作交流</span>
                </dt>
                <div class="menu_box" style="display: none;">
                    <dd class="js-menu-item">
                        <a href="/cooperation/cooperation/shop-type?app_code=cooperation">产品分类</a>
                    </dd>
                    <dd class="js-menu-item">
                        <a href="/cooperation/cooperation/cooperation-shop?app_code=cooperation">产品管理</a>
                    </dd>
                </div>
                <dt class="only-item">
                    <span class="module-icon align-m" aria-hidden="true"></span>
                    <span class="align-m">移民</span>
                </dt>
                <div class="menu_box" style="display: none;">
                    <dd class="js-menu-item">
                        <a href="/migrate/migrate/shop-type?app_code=migrate">产品分类</a>
                    </dd>
                    <dd class="js-menu-item">
                        <a href="/migrate/migrate/migrate-shop?app_code=migrate">产品管理</a>
                    </dd>
                </div>
                <dt class="only-item">
                    <span class="module-icon align-m" aria-hidden="true"></span>
                    <span class="align-m">专业服务</span>
                </dt>
                <div class="menu_box" style="display: none;">
                    <dd class="js-menu-item">
                        <a href="/serve/serve/shop-type?app_code=serve">产品分类</a>
                    </dd>
                    <dd class="js-menu-item">
                        <a href="/serve/serve/serve-shop?app_code=serve">产品管理</a>
                    </dd>
                </div>
                <dt class="only-item">
                    <span class="module-icon align-m" aria-hidden="true"></span>
                    <span class="align-m">外汇</span>
                </dt>
                <div class="menu_box" style="display: none;">
                    <dd class="js-menu-item">
                        <a href="/repast/repast/shop-type?app_code=repast">产品分类</a>
                    </dd>
                    <dd class="js-menu-item">
                        <a href="/repast/repast/repast-shop?app_code=repast">产品管理</a>
                    </dd>
                </div>
                <dt class="only-item">
                    <span class="module-icon align-m" aria-hidden="true"></span>
                    <span class="align-m">休闲娱乐</span>
                </dt>
                <div class="menu_box" style="display: none;">
                    <dd class="js-menu-item">
                        <a href="/sports/sports/shop-type?app_code=sports">产品分类</a>
                    </dd>
                    <dd class="js-menu-item">
                        <a href="/sports/sports/sports-shop?app_code=sports">产品管理</a>
                    </dd>
                </div>
                <dt class="only-item">
                    <span class="module-icon align-m" aria-hidden="true"></span>
                    <span class="align-m">VIP服务</span>
                </dt>
                <div class="menu_box" style="display: none;">
                    <dd class="js-menu-item">
                        <a href="/vip/vip/shop-type?app_code=vip">产品分类</a>
                    </dd>
                    <dd class="js-menu-item">
                        <a href="/vip/vip/vip-shop?app_code=vip">产品管理</a>
                    </dd>
                </div>

            </dl>
          
        </div>
        <!-- 菜单 [[-->
       <!-- <div class="manage-menu">
            <dl>
                <dt>
                    <span class="module-icon module-member align-m" aria-hidden="true"></span>
                    <span class="align-m">系统设置</span>
                 </dt> 
                  <dd class="js-menu-item on"><a href="<?= $this->context->createUrl('system/user/index')?>">用户管理</a></dd> 
                  <dd class="js-menu-item"><a href="<?= $this->context->createUrl('system/account/config')?>">公众号管理</a></dd>  
            </dl>  
        </div> ]]-->
        <!-- 菜单 ]]-->

        <!-- 内容 [[-->
        <?= $content ?>
        <!-- 内容 ]]--> 
    </div>
</div>



<!-- 容器 ]]-->
<!-- 底部 [[-->
 
<!-- 底部 ]]-->
<script type="text/javascript" src="/frontend/js/lib/global.js?v=81a7f2cfd5"></script> 
<script type="text/javascript" src="/frontend/js/lib/menu.js"></script> 
<?php $this->endPage() ?>
<script type="text/javascript">
    seajs.use('/frontend/js/plugin/dataTitle.js');
</script>
<script type="text/javascript">
    $('.on').parent().show();
    $('.only-item').click(function(){
        $(this).next().slideToggle();
    });
</script>
<?php if (isset($this->blocks['js'])) { ?>
    <?= $this->blocks['js'] ?>
<?php } ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

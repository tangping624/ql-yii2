<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');//dev, auto_test, test, auto_beta, beta, prod

require(__DIR__ . '/../protected/vendor/autoload.php');
require(__DIR__ . '/../protected/vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../protected/boot/inject.php');
require(__DIR__ . '/../protected/framework/bootstrap.php');

$config = require(__DIR__ . '/../protected/config/web.php');
(new yii\web\Application($config))->run();

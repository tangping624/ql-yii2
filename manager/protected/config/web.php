<?php //

define('APP_NAME', 'QiLinManager');
require(__DIR__ . '/inc/constants.php');

date_default_timezone_set('PRC');
$params = require(__DIR__ . '/inc/params.php');

$config = [
    'id' => APP_NAME,
    'name' => APP_NAME,
    'basePath' => dirname(__DIR__),
    'defaultRoute' => 'home/index',
    'bootstrap' => ['log'],
    'modules' => require(__DIR__ . '/inc/module.php'),
    'components' => [
        'request' => [
            'cookieValidationKey' => 'qilin',
        ],
        'session' => [
            'name' => 'PUBLICACCOUNTSID',
        ],
        'user' => [
            'identityClass' => 'app\models\User', 
            'loginUrl' => 'auth/login',
        ],
        'view' => [
            'theme' => require(__DIR__ . '/inc/theme.php'),
        ],
        'cache' => [
            'keyPrefix' => APP_NAME,
            'class' => 'yii\caching\FileCache',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ], 
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=mytest',
            'username' => 'test',
            'password' => 'test',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
        ],
        'context' => [
            'class' => '\app\framework\web\extension\ManagerContext',
        ],
        'dbTenant' => require(__DIR__ . '/inc/dbTenant.php'), 
    ],
    'params' => $params
];

$customConfigs = [];

if (YII_ENV == 'dev') {
    $customConfigs = require(__DIR__ . '/env/dev.php');
} elseif (YII_ENV == 'prod') {
    $customConfigs = require(__DIR__ . '/env/release.php');
} elseif (YII_ENV == 'test') {
    $customConfigs = require(__DIR__ . '/env/test.php');
} elseif (YII_ENV == 'beta') {
    $customConfigs = require(__DIR__ . '/env/beta.php');
} elseif (YII_ENV == 'auto_test') {
    $customConfigs = require(__DIR__ . '/env/auto_test.php');
} elseif (YII_ENV == 'auto_beta') {
    $customConfigs = require(__DIR__ . '/env/auto_beta.php');
}

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

$components = isset($customConfigs['configs']['components']) ? $customConfigs['configs']['components'] : [];
$config['bootstrap'] = isset($customConfigs['configs']['bootstrap']) ? $customConfigs['configs']['bootstrap'] : $config['bootstrap'];
$params = isset($customConfigs['params']) ? $customConfigs['params'] : [];
foreach($components as $k=>$v){
    $config['components'][$k] = $v;
}
foreach($params as $k=>$v){
    $config['params'][$k] = $v;
}

return $config;

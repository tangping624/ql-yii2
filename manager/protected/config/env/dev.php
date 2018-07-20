<?php
defined('WEIXIN_AGENT') or define('WEIXIN_AGENT', false);//是否限制在微信外访问
$yiiConfigs = [
    'components' => [
        'cache' => [
            'keyPrefix' => 'ysq',
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'flushInterval' => 1,
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'app\framework\logging\MyFileTarget',
                    'exportInterval' => 1,
                    'levels' => ['error', 'warning' , 'info', 'trace'],
                ], 
            ],
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=192.168.0.200;dbname=qilin',
            'username' => 'root',
            'password' => 'good123',
            'charset' => 'utf8',
             'enableSchemaCache' => true,
        ], 
    ],
];

$params = [
    'wx_sending_massMessage_count_per' => 2 //微信群发消息，每次发送数量
];

return ['configs' => $yiiConfigs, 'params' => $params];

<?php

defined('WEIXIN_AGENT') or define('WEIXIN_AGENT', false);//是否限制在微信外访问
defined('AUTO_TEST') or define('AUTO_TEST', true);

$yiiConfigs = [
    'components' => [
        'session' => [
            'name' => 'VMEMBERSIDAT',
        ],
        'cache' => [
            'keyPrefix' => 'ysq',
            'cachePath' => '/webser/www/autotest-cache',
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'flushInterval' => 1,
            'targets' => [
                [
                    'class' => 'app\framework\logging\MyFileTarget',
                    'exportInterval' => 1,
                    'levels' => ['error', 'warning'],
                ],
            ],

        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=ykqadatabasein.mysql.rds.aliyuncs.com;dbname=mycommunity_config_auto',
            'username' => 'yunshequ',
            'password' => 'yunshequ123',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
        ],
        'db_game' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=ykqadatabasein.mysql.rds.aliyuncs.com;dbname=game_auto_test',
            'username' => 'yunshequ',
            'password' => 'yunshequ123',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
        ]
    ],
];

$params = [
];

return ['configs'=>$yiiConfigs, 'params'=>$params];

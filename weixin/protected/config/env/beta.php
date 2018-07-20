<?php

defined('WEIXIN_AGENT') or define('WEIXIN_AGENT', true);//是否限制在微信外访问

$yiiConfigs = [
    'components' => [
        'cache' => [
            'keyPrefix' => 'ysq',
            'class' => 'app\framework\cache\AliyunCache',
        ],
        'log' => [
            'traceLevel' => 3,
            'flushInterval' => 1,
            'targets' => [
                #[
                #    'class' => 'app\framework\logging\MyFileTarget',
                #    'exportInterval' => 1,
                #    'levels' => ['error', 'warning'],
                #],
                [
                    'class' => 'app\framework\logging\SentryTarget',
                    'exportInterval' => 1,
                    'levels' => ['error', 'warning'],
                    'dsn' => 'http://0a428fe9df3f45189f3352a59e065951:027c7582a10e4242a1f696c8f049d439@shequ-beta.myscrm.cn:9876/3',
                ],
            ],
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=ykqadatabasein.mysql.rds.aliyuncs.com;dbname=mycommunity_config_beta',
            'username' => 'yunshequ',
            'password' => 'yunshequ123',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
        ],
        'db_game' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=ykqadatabasein.mysql.rds.aliyuncs.com;dbname=game_beta',
            'username' => 'yunshequ',
            'password' => 'yunshequ123',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
        ]
    ],
];

$params = [
];

return ['configs' => $yiiConfigs, 'params' => $params];
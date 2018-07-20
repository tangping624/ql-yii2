<?php

defined('WEIXIN_AGENT') or define('WEIXIN_AGENT', false);//是否限制在微信外访问

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
                    'dsn' => 'http://19c3f1b08bc6453f95fb79a38226b5d5:4147ef175e1d46438f9703373feede03@shequ-test.myscrm.cn:9876/2',
                ],
            ],

        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=ykqadatabasein.mysql.rds.aliyuncs.com;dbname=mycommunity_config_test',
            'username' => 'yunshequ',
            'password' => 'yunshequ123',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
        ],
        'db_game' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=ykqadatabasein.mysql.rds.aliyuncs.com;dbname=game_test',
            'username' => 'yunshequ',
            'password' => 'yunshequ123',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
        ],
        'queue' => [
            'class' => 'app\framework\queue\Client',
            'host' => 'e99d439173404737.m.cnsza.kvstore.aliyuncs.com',
            'uid' => 'e99d439173404737',
            'pwd' => 'qsedtq5zibzXiHb1hgxx',
            'port' => 6379,
            'database' => 1
        ]
    ],
];

$params = [
];

return ['configs'=>$yiiConfigs, 'params'=>$params];
<?php

$yiiConfigs = [
    'components' => [
        'session' => [
            'name' => 'PUBLICACCOUNTSIDAB',
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
                    'context' => ['session' => 1],
                    'levels' => ['error', 'warning'],
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

return ['configs'=>$yiiConfigs, 'params'=>$params];
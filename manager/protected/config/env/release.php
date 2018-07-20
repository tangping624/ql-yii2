<?php

$yiiConfigs= [
    'components' => [
        'cache' => [
            'keyPrefix' => 'ysq',
            'class' => 'app\framework\cache\AliyunCache',//生产环境切换到阿里云
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
    'wx_sending_massMessage_count_per' => 5000 //微信群发消息，每次发送数量
];

return ['configs'=>$yiiConfigs, 'params'=>$params];

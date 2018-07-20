<?php

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
                    'context' => ['session' => 1],
                    'dsn' => 'http://9a65229c48cd4b45bfedcbefb063d9f8:1cb2c06c4cad4f5a953523b2470d98a4@shequ-beta.myscrm.cn:9876/8',
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

return ['configs' => $yiiConfigs, 'params' => $params];
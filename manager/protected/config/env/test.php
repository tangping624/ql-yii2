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
                    'context' => ['session' => 1],
                    'levels' => ['error', 'warning'],
                    'dsn' => 'http://e0deadf559ff4f2db7eee2d353cc06b4:aff3dee94a224c4599de3069e48644d9@shequ-test.myscrm.cn:9876/4',
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
    'wx_sending_massMessage_count_per' => 3//微信群发消息，每次发送数量
];

return ['configs'=>$yiiConfigs, 'params'=>$params];
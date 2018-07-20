<?php

defined('WEIXIN_AGENT') or define('WEIXIN_AGENT', false);//是否限制在微信外访问

$yiiConfigs =
    [
        'bootstrap' => ['log'],
        'components' => [
            'cache' => [
                'keyPrefix' => 'ysq',
                'class' => 'yii\caching\FileCache',
            ],
            'log' => [
                'flushInterval' => 1,
                'traceLevel' => YII_DEBUG ? 3 : 0,
                'targets' => [
//                    [
//                        'class' => 'app\framework\logging\SentryTarget',
//                        'exportInterval' => 1,
//                        'levels' => ['error', 'warning', 'info', 'trace'],
//                        'dsn' => 'http://6701d81299a04cd090fd60506a7b58b7:edf568958d03442ca820bfaf83d788d7@10.5.7.133:9876/2',
//                    ],
                    [
                        'class' => 'app\framework\logging\MyFileTarget',
                        'exportInterval' => 1,
                        'levels' => ['error', 'warning', 'info', 'trace'],
                    ]
                ],
            ],
            'db' => [
                'class' => 'yii\db\Connection',
                'dsn' => 'mysql:host=192.168.0.200;dbname=qilin',
                'username' => 'root',
                'password' => 'good123',
                'charset' => 'utf8',
                'enableSchemaCache' => true,

            ] , 
        ],
    ];

    $params = [
    ];

    return ['configs' => $yiiConfigs, 'params' => $params];

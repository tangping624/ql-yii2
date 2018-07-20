<?php

defined('WEIXIN_AGENT') or define('WEIXIN_AGENT', true);//是否限制在微信外访问

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
                    'levels' => ['error', 'warning'],
                    'except' => ['yii\web\HttpException:404', 'yii\web\HttpException:403']
                ],
            ],
        ],
         'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=rm-2ze269no04f44gsq6.mysql.rds.aliyuncs.com;dbname=publicaccount_test',
            'username' => 'carlife_test',
            'password' => 'Carlifetest',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
        ] 
    ],
];

$params = [
];

return ['configs'=>$yiiConfigs, 'params'=>$params];

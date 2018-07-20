<?php

return [
    'id' => 'testingAppId',
    'name' => 'testingAppId',
    'basePath' => dirname(__DIR__),
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=10.5.7.132;dbname=test',
            'username' => 'sa',
            'password' => 'Mysoft123',
            'charset' => 'utf8',
        ],
    ],
    'params' => [],
];
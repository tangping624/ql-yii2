<?php

require(__DIR__ . '/../helpers/ApplicationMocker.php');
use \Mockery as m;

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');//dev, test, prod

$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['SERVER_ADDR'] = '127.0.0.1';
define('APP_ID', 'unit_test');

$appConfig = require(__DIR__ . '/config.php');
$httpMocker = new ApplicationMocker($appConfig);
$app = $httpMocker->app;


/** @var app\framework\biz\tenant\TenantReaderInterface | \Mockery\MockInterface $tenantReader */
$tenantReader = m::mock('app\framework\biz\tenant\TenantReaderInterface');
$tenantReader->shouldReceive('getCurrentTenantCode')->andReturn('mysoft');
 \Yii::$container->set('app\framework\biz\tenant\TenantReaderInterface', $tenantReader);

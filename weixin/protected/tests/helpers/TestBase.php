<?php

use yii\base\InvalidRouteException;

require_once __DIR__ . '/../autoload_register.php';
require_once __DIR__ . '/init.php';

abstract class TestBase extends PHPUnit_Framework_TestCase
{
    public function handle_args($args)
    {
        return array_merge($args, ['o' => Yii::$app->params['o'], 'token' => Yii::$app->params['token']]);
    }

    protected function assertException($callback)
    {
        $except = false;
        try {
            $callback();
        } catch (Exception $e) {
            $except = true;
        }
        $this->assertEquals(true, $except);
    }

    protected function runAction($route, $params)
    {
        //execute
        $parts = Yii::$app->createController($route);

        if (is_array($parts)) {

            /* @var $controller Controller */
            list($controller, $actionID) = $parts;
            $controller->unit_test = true;
            list($view_name, $model) = $controller->runAction($actionID, $params);

            return (object)['view' => $view_name, 'data' => $model];

        } else {

            throw new InvalidRouteException('Unable to resolve the request "');
        }
    }

    public function tearDown()
    {
        \Mockery::close();
    }
}

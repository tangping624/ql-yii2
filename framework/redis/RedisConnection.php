<?php

namespace app\framework\redis;


use app\framework\redis\interfaces\RedisConnectionInterface;

class RedisConnection implements RedisConnectionInterface
{

    /**
     * @return array
     */
    public function getConnectOption()
    {
        $option =['host'=> '127.0.0.1', 'port'=>6379];

        $paramHost = \Yii::$app->params['redis_host'];
        $paramPort = \Yii::$app->params['redis_port'];
        $paramConnectTimeout = \Yii::$app->param['redis_connect_timeout'];
        if(isset($paramHost)){
            $option['host'] = $paramHost;
        }

        if(isset($paramPort)){
            $option['port'] = $paramPort;
        }
        if(isset($paramConnectTimeout)){
            $option['timeout'] = $paramConnectTimeout;
        }

        return $option;
    }
}
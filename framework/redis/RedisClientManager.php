<?php

namespace app\framework\redis;

use yii\base\Component;

class RedisClientManager extends Component
{

    /**
     * @return RedisConnection
     * @throws \yii\base\InvalidConfigException
     */
    private static function _connection()
    {
        $conn = \Yii::$container->get('app\framework\redis\interfaces\RedisConnectionInterface');
        if (!isset($conn)) {
            $conn = new RedisConnection();
            \Yii::$container->setSingleton('app\framework\redis\interfaces\RedisConnectionInterface', $conn);
        }

        return $conn;
    }

    /**
     * 创建Redis client并连接server
     * @return \Redis
     * @throws ConnectException
     */
    public static function create()
    {
        $client = new \Redis();
        $conn = static::_connection();
        $options = $conn->getConnectOption();
        $connected = $client->connect($options['host'], $options['port'], $options['timeout']);
        if ($connected == false) {
            throw new ConnectException('连接redis-server失败');
        } else {
            return $client;
        }
    }
}
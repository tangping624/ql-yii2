<?php

namespace app\framework\cache;

use app\framework\cache\interfaces\CacheProviderInterface;

/**
 * cache using yii
 */
class YiiCacheProvider implements  CacheProviderInterface
{

    /**
     * @inheritdoc
     */
    public function set($key, $value, $duration = 0)
    {
        if(empty($key)){
            throw new \InvalidArgumentException('$key');
        }

        if(!isset($value)){
            throw new \InvalidArgumentException('$value');
        }

        \Yii::$app->cache->set($key, $value, $duration);
    }

    /**
     * @inheritdoc
     */
    public function get($key, $default = null)
    {
        if (empty($key)) {
            return $default;
        }

        $obj = \Yii::$app->cache->get($key);
        return $obj == false ? $default : $obj;
    }


    /**
     * @inheritdoc
     */
    public function add($key, $value, $duration = 0)
    {
        if (empty($key)) {
            throw new \InvalidArgumentException('$key');
        }

        if (!isset($value)) {
            throw new \InvalidArgumentException('$value');
        }
        return \Yii::$app->cache->add($key, $value, $duration);
    }

    /**
     * @inheritdoc
     */
    public function mset($keyValue_arr, $duration = 0)
    {
        if(!is_array($keyValue_arr) || count($keyValue_arr) < 1) {
            throw new \InvalidArgumentException('$keyValue_arr should be array and not allow empty');
        }

        \Yii::$app->cache->mset($keyValue_arr, $duration);
    }

    /**
     * @inheritdoc
     */
    public function mget($key_arr)
    {
        if (!is_array($key_arr)) {
            throw new \InvalidArgumentException('$key_arr should be array');
        }

        if (count($key_arr) < 1) {
            return null;
        }

        return \Yii::$app->cache->mget($key_arr);
    }

    public function remove($key)
    {
        \Yii::$app->cache->delete($key);
    }

    public function flush()
    {
        \Yii::$app->cache->flush();
    }
}
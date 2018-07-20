<?php

namespace app\framework\cache\interfaces;

interface CacheProviderInterface
{
    /**
     * @param string|int
     * @param mixed
     * @param int $duration after seconds, 0 mean never expire
     * @return void
     */
    public function set($key, $value, $duration = 0);

    /**
     * @param string|int $key
     * @param mixed $default return default value if nothing
     * @return mixed 没有缓存或缓存过期则返回false
     */
    public function get($key, $default = null);

    /**
     * 当$key存在时返回false
     * @param $key
     * @param $value
     * @param int $duration
     * @return bool
     */
    public function add($key, $value, $duration = 0);

    /**
     * set multiple objects once
     * @param array $keyValue_arr key value pair
     * @param int $duration expire after seconds, 0 mean never expire
     * @return void
     */
    public function mset($keyValue_arr, $duration = 0);

    /**
     * get multiple objects once
     * @param array string|array int
     * @return array|null
     */
    public function mget($key_arr);


    public function remove($key);

    public function flush();

}
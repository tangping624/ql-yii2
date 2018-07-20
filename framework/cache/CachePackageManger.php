<?php

namespace app\framework\cache;

use app\framework\cache\interfaces\KeyPrefixGeneratorInterface;

/**
 * 数据包缓存
 * $scope 缓存参数范围说明:
 *  全局缓存,key只由$id决定 
 */
class CachePackageManger
{

    private static $_instance;
    private $key;
    private static $_appId;
    protected  $dns;

    /**
     * @param $key
     * @param int $scope 缓存参数范围
     * @param string $scopeId 对应范围的标识
     */
    private function __construct($key, $scope = 1, $scopeId = '')
    {
        if (empty($key)) {
            throw new \InvalidArgumentException('$key');
        }

        $this->key = $key;
        $this->scope = $scope;
        $this->scopeId = $scopeId;
    }

    /**构造实例
     * @param mixed $key
     * @param int $scope 缓存参数范围
     * @param string $scopeId 对应范围的标识
     * @return CachePackageManger
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     */
    public static function instance($key, $scope = 1, $scopeId = '')
    {
        static::$_instance = new CachePackageManger($key, $scope, $scopeId);
        return static::$_instance;
    }


    /**
     * @param mixed $default 默认值
     * @return object|null
     */
    public function get($default = null)
    {
        $key = $this->get_cacheId();
        $cacheProvider = \Yii::$container->get('app\framework\cache\interfaces\CacheProviderInterface');

        $obj = $cacheProvider->get($key);
        return $obj ? $obj : $default;
    }

    /**
     * @param mixed $package
     * @param int $duration 过期时间秒, 0为永不过期
     */
    public function set($package, $duration = 0)
    {
        if (!isset($package)) {
            throw new \InvalidArgumentException('$package没有值');
        }

        $key = $this->get_cacheId();
        $cacheProvider = \Yii::$container->get('app\framework\cache\interfaces\CacheProviderInterface');
        $cacheProvider->set($key, $package, $duration);
    }

    public function remove()
    {
        $key = $this->get_cacheId();
        $cacheProvider = \Yii::$container->get('app\framework\cache\interfaces\CacheProviderInterface');
        $cacheProvider->remove($key);
    }

    /**
     * 删除所有缓存
     * @throws \yii\base\InvalidConfigException
     */
    public function removeAll()
    {
        $cacheProvider = \Yii::$container->get('app\framework\cache\interfaces\CacheProviderInterface');
        $cacheProvider->flush();
    }


    /**
     * 获取缓存键
     * token:appId:packageId
     * @return string
     * @throws \Exception
     */
    public function get_cacheId()
    {

        /** @var KeyPrefixGeneratorInterface $KeyPrefixGenerator */
        $KeyPrefixGenerator = \Yii::$container->get('app\framework\cache\interfaces\KeyPrefixGeneratorInterface');
        $prefixKey = $KeyPrefixGenerator->createKeyPrefix($this->scope, $this->scopeId);
        \Yii::trace('测试key: ' .$prefixKey . KeyPrefixGeneratorInterface::KEY_SEPARATOR . $this->key);
        if(empty($prefixKey)){
            return $this->key;
        }
        else{
            return $prefixKey . KeyPrefixGeneratorInterface::KEY_SEPARATOR . $this->key;
        }
    }


    /**
     * prevent the instance from being cloned
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * prevent from being unserialized
     *
     * @return void
     */
    private function __wakeup()
    {

    }
}
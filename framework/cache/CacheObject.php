<?php
namespace app\framework\cache;

use app\framework\cache\interfaces\KeyPrefixGeneratorInterface;

abstract class CacheObject
{

    public $id;
    protected $scope;
    protected $scopeId;

    /**
     * @param $id
     * @param int $scope 缓存参数范围
     *  全局缓存,key只由$id决定 
     * @param string $scopeId 对应范围的标识
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function __construct($id, $scope=1, $scopeId='')
    {
        $this->id = $id;
        $this->scope = $scope;
        $this->scopeId = $scopeId;

    }


    /**
     * 缓存当前实例对象
     * @param int $duration 秒后过期, 0 表示永不过期
     * @throws CacheException
     */
    public function cache($duration = 0)
    {
        if (empty($this->id)) {
            throw new CacheException('id 必须设置');
        }

        $cacheProvider = \Yii::$container->get('app\framework\cache\interfaces\CacheProviderInterface');
        $key = static::get_cacheId($this->id, $this->scope, $this->scopeId);
        $cacheProvider->set($key, $this, $duration);
    }


    /**一次设置多个缓存
     * @param $obj_arr
     * @param int $duration 秒后过期, 0 表示永不过期
     * @param int $scope
     * @param string $scopeId
     */
    public static function set_many($obj_arr, $duration = 0, $scope = 1, $scopeId = '')
    {
        if (!is_array($obj_arr)) {
            throw new \InvalidArgumentException('$obj_arr 必须是数组');
        }

        if (!isset($obj_arr) || count($obj_arr) < 1) {
            throw new \InvalidArgumentException('$obj_arr 不能为空数组');
        }

        /** @var KeyPrefixGeneratorInterface $KeyPrefixGenerator */
        $KeyPrefixGenerator = \Yii::$container->get('app\framework\cache\interfaces\KeyPrefixGeneratorInterface');

        $keyPrefix = $KeyPrefixGenerator->createKeyPrefix($scope, $scopeId);

        $source = array_map(function ($item) use ($scope, $scopeId, $keyPrefix) {

            return [static::create_cacheId(get_class($item), $item->id, $keyPrefix), $item];
        }, $obj_arr);

        $arr = [];
        foreach ($source as $kv) {
            $arr[$kv[0]] = $kv[1];
        }

        $cacheProvider = \Yii::$container->get('app\framework\cache\interfaces\CacheProviderInterface');
        $cacheProvider->mset($arr, $duration);
    }


    /**
     * 批量获取
     * @param $id_arr array string|array int
     * @param int $scope
     * @param string $scopeId
     * @return array
     */
    public static function get_many($id_arr, $scope = 1, $scopeId = '')
    {
        if (!is_array($id_arr)) {
            throw new \InvalidArgumentException('$id_arr 不是数组');
        }

        if (count($id_arr) < 1)
            return [];

        $key_arr = [];

        foreach ($id_arr as $id) {
            $key_arr[] = static::get_cacheId($id, $scope, $scopeId);
        }

        $cacheProvider = \Yii::$container->get('app\framework\cache\interfaces\CacheProviderInterface');
        $kvs = $cacheProvider->mget($key_arr);
        $results = [];
        foreach ($kvs as $kv) {
            $results[] = $kv;
        }

        return $results;
    }


    /**
     * 获取当前缓存实例
     * @param $id
     * @param int $scope
     * @param string $scopeId
     * @return mixed|null 返回当前缓存实例
     */
    public static function getCache($id, $scope = 1, $scopeId = '')
    {
        if (empty($id)) {
            return null;
        }

        $cacheProvider = \Yii::$container->get('app\framework\cache\interfaces\CacheProviderInterface');

        $key = static::get_cacheId($id, $scope, $scopeId);

        return $cacheProvider->get($key, null);
    }

    /**
     * @param $id
     * @param int $scope
     * @param string $scopeId
     */
    public static function remove($id, $scope = 1, $scopeId = '')
    {
        $key = static::get_cacheId($id, $scope, $scopeId);

        $cacheProvider = \Yii::$container->get('app\framework\cache\interfaces\CacheProviderInterface');

        $cacheProvider->remove($key);
    }

    /**
     * 删除所有的缓存
     * @throws \yii\base\InvalidConfigException
     */
    public static function removeAll()
    {
        $cacheProvider = \Yii::$container->get('app\framework\cache\interfaces\CacheProviderInterface');
        $cacheProvider->flush();
    }


    /**
     * @param $id
     * @param int $scope
     * @param string $scopeId
     * @return string
     * @throws \Exception
     */
    public static function get_cacheId($id, $scope = 1, $scopeId = '')
    {
        if (empty($id)) {
            throw new \Invalidargumentexception('无效参数');
        }

        $classname = get_called_class();
        /** @var KeyPrefixGeneratorInterface $KeyPrefixGenerator */
        $KeyPrefixGenerator = \Yii::$container->get('app\framework\cache\interfaces\KeyPrefixGeneratorInterface');
        $prefixKey = $KeyPrefixGenerator->createKeyPrefix($scope, $scopeId);
        $cache_id = static::create_cacheid($classname, $id, $prefixKey);

        return $cache_id;
    }


    /**
     * @param $className
     * @param $id
     * @param string $prefix
     * @return string
     * @throws \Exception
     */
    protected static function create_cacheId($className, $id, $prefix)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }

        if (empty($prefix)) {
            return $className . KeyPrefixGeneratorInterface::KEY_SEPARATOR . $id;
        } else {
            return $prefix . KeyPrefixGeneratorInterface::KEY_SEPARATOR . $className . KeyPrefixGeneratorInterface::KEY_SEPARATOR . $id;
        }

    }


}

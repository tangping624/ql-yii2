<?php
/**
 * 楼栋缓存管理
 * User: robert
 * Date: 2015/5/26
 * Time: 18:05
 */
namespace app\cache;

use app\cache\models\BuildingCache;
use app\models\Building;

class BuildingCacheManager
{
    //用户数据缓存时长(s)
    const CACHE_USER_TIME_SECONDS = 86400;

    /**
     * @var BuildingCacheManager 单例
     */
    private static $_instance;

    /**
     * 构造器
     */
    public function __construct()
    {
    }

    /**
     * BuildingCacheManager 实例
     * @return BuildingCacheManager
     * @throws \yii\base\InvalidConfigException
     */
    public static function instance()
    {
        if (!isset(static::$_instance)) {
            static::$_instance = new BuildingCacheManager();
        }
        return static::$_instance;
    }

    public function getBuildingCache($corpId)
    {
        if (isset($corpId) == false || empty($corpId)) {
            return null;
        }
        $cacheData = BuildingCache::getCache($corpId);
        if (isset($cacheData) && empty($cacheData) == false) {
            return $cacheData;
        }

        $buildings = (new Building())->getTreeBuildings($corpId);

        $buildingCache = new BuildingCache($corpId);
        $buildingCache->id = $corpId;
        $buildingCache->buildingTree = $buildings;
        $buildingCache->cache($this::CACHE_USER_TIME_SECONDS);
        unset($buildings);
        return $buildingCache;
    }

    public function removeBuildingCache($corpId)
    {
        BuildingCache::remove($corpId);
    }
}

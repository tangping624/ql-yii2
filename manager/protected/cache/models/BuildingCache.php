<?php
/**
 * 楼栋缓存
 * User: robert
 * Date: 2015/5/26
 * Time: 18:00
 */
namespace app\cache\models;

use app\framework\cache\CacheObject;

/**
 * Class BuildingCache
 * @package app\cache\models
 */
class BuildingCache extends CacheObject
{
    /**
     * @var array 项目楼栋树
     */
    public $buildingTree;
}

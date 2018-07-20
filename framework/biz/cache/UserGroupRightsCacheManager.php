<?php
namespace app\framework\biz\cache;

use app\framework\biz\cache\models\UserGroupRightsCacheObject;
use app\framework\utils\WebUtility;
use \Yii;

/**
 * 用户组权限管理器(依赖用户组的登录状态)
 * Class UserGroupRightsCacheObject
 * @package app\framework\biz\cache
 */
class UserGroupRightsCacheManager
{
    /**
     * 获得用户组的缓存对象
     * @param string $groupId
     * @return UserGroupRightsCacheObject 用户组缓存对象
     */
    private static function getCache()
    {  
        $cache = UserGroupRightsCacheObject::getCache(SUPER_USER_ID);
        //当前用户组未缓存则创建
        if (empty($cache)) {
            $cache = new UserGroupRightsCacheObject();
            $cache->cache();
        } 
        return $cache;
    }
 
 

    /**
     * 获得用户所在组有权限的模块
     * @param $appCode string 应用编码（默认当前应用）
     * @return array 功能模块列表
     */
    public static function getFunctions( )
    {  
           $functions = self::getCache()->getFuncListForNav(); 
            if (count($functions) > 0) {
                return self::arrayUnique($functions);
            }
         

        return $functions;
    }
 

    /**
     * 获取用户所在组默认模块Url
     * @return string
     */
    public static function getDefaultFunctionUrl()
    { 
        return "/system/user/index";
    }

    /**
     * 获取默认模块
     * @return mixed
     */
    public static function getDefaultFunction()
    {
        $functions = static::getFunctions();
        return current($functions);
    }

  
    /**
     * 清空缓存
     */
    public static function clearCache()
    { 
       UserGroupRightsCacheObject::remove();
            
    }

    /**
     * @param $groupId
     */
    public static function removeCache()
    {
        //需要维护缓存的地方：TUGroupAppRole
        //TODO TAppRolePermission TAppFunction
        UserGroupRightsCacheObject::remove( );
    }


    /**
     * @param $array
     * @return array
     */
    public static function arrayUnique($array)
    {
        $temp = $res = [];
        foreach ($array as $v) {
            $v = json_encode($v);  //降维,将一维数组转换字符串
            $temp[] = $v;
        }

        $temp = array_unique($temp);    //去掉重复的字符串,也就是重复的一维数组
        foreach ($temp as $item) {
            $res[] = json_decode($item, true);   //再将拆开的数组重新组装
        }

        return $res;
    } 
}

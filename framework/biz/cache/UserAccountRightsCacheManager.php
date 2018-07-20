<?php
 

namespace app\framework\biz\cache;

use app\framework\biz\cache\models\UserAccountRightsCacheObject;
use app\framework\utils\WebUtility;
use \Yii;

/**
 * 用户组权限管理器(依赖用户组的登录状态)
 * Class UserGroupRightsCacheObject
 * @package app\framework\biz\cache
 */
class UserAccountRightsCacheManager
{
    /**
     * 获得用户组的缓存对象
     * @param string $accountId
     * @return UserGroupRightsCacheObject 用户组缓存对象
     */
    private static function getCache($accountId,$level,$package_type)
    {
        if (empty($accountId)) {
            throw new \InvalidArgumentException('$accountId');
        }
       if (empty($level)) {
            throw new \InvalidArgumentException('$level');
        }
        $cache = UserAccountRightsCacheObject::getCache($accountId.'_'.$level);
        //当前用户组未缓存则创建
        if (empty($cache)) {
            $cache = new UserAccountRightsCacheObject($accountId,$level,$package_type);
            $cache->cache();
        } 
        return $cache;
    }

    /**
     * 获得appCode
     * @return string appCode
     */
    private static function getAppCode()
    {
        return isset($_REQUEST['_ac']) ? $_REQUEST['_ac'] :'member';
    }

    /**
     * 获取用户组的应用列表（导航使用）
     * @return array
     */
    public static function getAppsForNav()
    {
        $level = AccountSessionManager::getUserSession()->getLevel();
        $account_id =  AccountSessionManager::getAccountSession()->account_id;
        $package_type =  AccountSessionManager::getAccountSession()->package_type;
        $apps = [];
        if (!empty($level) && !empty($account_id)) { 
            $tempApps = self::getCache($account_id,$level,$package_type)->getAppsForNav();
            foreach ($tempApps as $key => &$app) {
                if (AccountSessionManager::checkAccountApp($app['app_code']) === false) {
                    unset($tempApps[$key]);
                }
            }
            $apps = array_merge_recursive($apps, $tempApps); 

            if (count($apps) > 0) {
                return self::arrayUnique($apps);
            }
        } 
        return $apps;
    }

    /**
     * 获得用户所在组有权限的模块
     * @param $appCode string 应用编码（默认当前应用）
     * @return array 功能模块列表
     */
    public static function getFunctions($appCode = null)
    {
        if (isset($appCode) == false) {
            $appCode = self::getAppCode();
        }
        $level = AccountSessionManager::getUserSession()->getLevel();
        $account_id = AccountSessionManager::getAccountSession()->account_id;
         $package_type =  AccountSessionManager::getAccountSession()->package_type;
        $functions = [];
        if (!empty($level) && !empty($account_id) && AccountSessionManager::checkAccountApp($appCode)) { 
             $functions =   self::getCache($account_id,$level,$package_type)->getFunctions($appCode); 
            if (count($functions) > 0) {
                return self::arrayUnique($functions);
            }
        } 
        return $functions;
    }

    /**
     * 获得用户所在组有权限的模块（导航使用）
     * @param $appCode string 应用编码（默认当前应用）
     * @return array 功能模块列表
     */
    public static function getFunctionsForNav($appCode = null)
    {
        if (isset($appCode) == false) {
            $appCode = self::getAppCode();
        }
         $level = AccountSessionManager::getUserSession()->getLevel();
        $account_id = AccountSessionManager::getAccountSession()->account_id;
         $package_type =  AccountSessionManager::getAccountSession()->package_type;
        $functions = [];
        if (!empty($level) && !empty($account_id)&& AccountSessionManager::checkAccountApp($appCode)) {
             $functions = self::getCache($account_id,$level,$package_type)->getFunctionsForNav($appCode); 
            if (count($functions) > 0) {
                return self::arrayUnique($functions);
            }
        }

        return $functions;
    }

    /**
     * 获取用户所在组默认模块Url
     * @return string
     */
    public static function getDefaultFunctionUrl()
    {
        $firstFunction = self::getDefaultFunction();
        if (isset($firstFunction)) {
            $url = $firstFunction['func_url'];
            if (!empty($firstFunction['provideby_site_code'])) {
                $url .= ((strstr($url, '?') === false) ? '?' : '&') . '_ac=' . $firstFunction['app_code'];
            }
        }
        if (empty($url)) {
            $url = '';
        }
        return $url;
    }

    /**
     * 获取默认模块
     * @return mixed
     */
    public static function getDefaultFunction()
    {
        $functions = static::getFunctionsForNav();
        return current($functions);
    }


//    /**
//     * 获得用户所在组有权限的功能点
//     * @param string $funcCode 功能模块Code
//     * @param null $appCode 应用编码（默认当前应用）
//     * @return array 功能点集合
//     */
//    public static function getActions($funcCode, $appCode = null)
//    {
//        if (isset($appCode) === false) {
//            $appCode = self::getAppCode();
//        }
//
//        $groupIds = UserSessionManager::getUserSession()->ugroup_id;
//        $actions = [];
//        if (!empty($groupIds) && count($groupIds) > 0 && UserSessionManager::checkTenantApp($appCode)) {
//            foreach ($groupIds as $groupId) {
//                $actions = array_merge_recursive($actions, self::getCache($groupId)->getActions($appCode, $funcCode));
//            }
//
//            if (count($actions) > 0) {
//                return self::arrayUnique($actions);
//            }
//        }
//
//        return $actions;
//    }

    /**
     * 检查当前用户所在组在应用下的功能模块的功能点
     * @param string $funcCode 功能模块id
     * @param string $actionCode 功能id
     * @param null $appCode 应用编码（默认当前应用）
     * @return bool 是否具备功能点权限
     */
    public static function checkRight($funcCode,  $appCode = null)
    {
        if (isset($appCode) === false) {
            $appCode = self::getAppCode();
        }
        $level = AccountSessionManager::getUserSession()->getLevel();
        $account_id = AccountSessionManager::getAccountSession()->account_id;
         $package_type =  AccountSessionManager::getAccountSession()->package_type;
        
        if (!empty($level) &&!empty($account_id)&& AccountSessionManager::checkAccountApp($appCode)) { 
            $result = self::getCache($account_id,$level,$package_type)->checkRight($appCode, $funcCode);
            if ($result) {
                return true;
            } 
        }

        return false;
    }

    /**
     * 清空缓存
     */
    public static function clearCache()
    {
        $level = AccountSessionManager::getUserSession()->getLevel();
        $account_id = AccountSessionManager::getAccountSession()->account_id;
        if (!empty($level) &&!empty($account_id) ) { 
            UserAccountRightsCacheObject::remove($account_id.'_'.$level); 
        }
    }

    /**
     * @param $account_id
     */
    public static function removeCache($account_id,$level)
    {   
        //需要维护缓存的地方：TUGroupAppRole
        //TODO TAppRolePermission TAppFunction
       UserAccountRightsCacheObject::remove($account_id.'_'.$level); 
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

    public static function getDefaultAppFunctionUrl()
    {
        $appList = self::getAppsForNav();
        if (!empty($appList)) {
            $functions = static::getFunctionsForNav(current($appList)['app_code']);;
            $firstFunction = current($functions);
//            if ($firstFunction != false && isset($firstFunction)) {
//                $url = $firstFunction['func_url'];
//                if (!empty($firstFunction['provideby_site_code'])) {
//                    $url = SiteCacheManager::getSiteUrl($firstFunction['provideby_site_code']) . $url . ((strstr($url, '?') === false) ? '?' : '&') . '_ac=' . $firstFunction['app_code'];
//                } else {
//                    $url = WebUtility::createUrl($url);
//
//                    $url = ApplicationCacheManager::getApplicationUrl($firstFunction['app_code']) . $url;
//                }
//
//            }
             $url = WebUtility::createUrl(ApplicationCacheManager::getApplicationUrl($firstFunction['app_code']) );
        }
        if (empty($url)) {
            $url = '';
        }
        return $url;
    }
}
 
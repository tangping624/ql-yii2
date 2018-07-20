<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\api\services;
use app\framework\db\EntityBase;
/**
 * Description of BizCacheManager
 *
 * @author Chenxy
 */
class BizCacheManager
{
    /**
     * 清除第三方托管缓存
     * @param string $appId 微信第三方平台ID
     */
    public static function clearComponentCache($appId)
    {
        \app\framework\weixin\component\ComponentAccessTokenRepository::clearCache($appId);
    }
    
    /**
     * 清除公众号缓存
     * @param string $id 支持公众号id,微信原始ID，AppId
     */
    public static function clearAccountCache($id)
    {
            
        $dbConn =EntityBase::getDb();
        $sql = "select id,original_id,app_id from p_account "
                . "where (original_id='{$id}' or id='{$id}' or app_id='{$id}') and is_deleted=0";
        $config = $dbConn->createCommand($sql)->queryOne();
        $appId = $config["app_id"];
        $accountId = $config["id"];
        $wechat_id = $config["original_id"];
        \app\framework\weixin\DbAccessTokenRepository::clearCache($accountId);
        \app\framework\weixin\helper\BizTenantCodeHelper::clearTenantAppIdMappingCache($appId);
        static::clearAccountMsgForwardSettingCache($accountId);
    }
    
    /**
     * 清除公众号消息第三方转发设置缓存
     * @param string $accountId 公众号id
     */
    public static function clearAccountMsgForwardSettingCache($accountId)
    {
        \app\modules\api\services\WechatMsgForwardModule::clearWechatMsgForwardSettingCache($accountId);
    }
    
    /**
     * 清除业务上微信相关缓存设置
     * @param string $wechat 微信号
     * @param string $cacheType 缓存内容类型
     * @param string $key 清除指定项的缓存（暂使用范围为菜单）
     */
    public static function clearReplayCache($wechat, $cacheType, $key = '')
    {
        $cache = \Yii::$app->cache;
        switch ($cacheType) {
            // 帐号设置缓存 AccessTokenRepository
            case 'account':
                static::clearAccountCache($wechat);
                break;
            // 修改关注回复
            case 'subscribe':
                // 清除关注回复缓存
                $cacheKey = WeixinEventHandler::getCacheKey($wechat, 'subscribe', $key);
                !empty($cacheKey) && $cache->exists($cacheKey) && $cache->delete($cacheKey);
                break;
            // 修改自动回复
            case 'autoreply':
                // 清除关注回复缓存
                $cacheKey = BizService::getAutoReplaySettingCacheKey($wechat);
                !empty($cacheKey) && $cache->exists($cacheKey) && $cache->delete($cacheKey);
                break;
            // 修改关键字,自动回复和关注回复依赖
            case 'keyword':
                // 清除关注回复缓存
                $cacheKey = WeixinEventHandler::getCacheKey($wechat, 'subscribe', $key);
                !empty($cacheKey) && $cache->exists($cacheKey) && $cache->delete($cacheKey);
                $cacheKey = BizService::getAutoReplaySettingCacheKey($wechat);
                !empty($cacheKey) && $cache->exists($cacheKey) && $cache->delete($cacheKey);
                break;
            case 'menu':
                // 清除click回复内容
                $cacheKey = WeixinEventHandler::getCacheKey($wechat, 'click', $key);
                !empty($cacheKey) && $cache->exists($cacheKey) && $cache->delete($cacheKey);
                break;
            default:
                break;
        }
    }
}

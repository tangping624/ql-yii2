<?php

namespace app\framework\biz\cache;

use app\framework\biz\cache\models\FanCacheObject;

class FanCacheManager
{
    /**
     * @return Repository
     * @throws \yii\base\InvalidConfigException
     */
    private static function getRepositoryInstance()
    {
        $instance = \Yii::$container->get('app\framework\biz\cache\Repository');

        if (is_null($instance)) {
            $instance = new Repository();
            \Yii::$container->setSingleton('app\framework\biz\cache\Repository', $instance);
        }

        return $instance;
    }

    /**
     * @param $openId
     * @param $tenantCode
     * @return FanCacheObject|null
     * @throws \app\framework\cache\CacheException
     */
    public static function getFan($openId)
    {
        if (empty($openId)) {
            return null;
        }
       

        $cachedFan = FanCacheObject::getCache($openId);

        if (is_null($cachedFan)) {
            $fanRow = static::getRepositoryInstance()->getFanByOpenId($openId);
            if ($fanRow != false) {
                $cachedFan = new FanCacheObject($openId);
                $cachedFan->fanId = $fanRow['id'];
                $cachedFan->nickName = isset($fanRow['nick_name']) ? $fanRow['nick_name'] : '';
                $cachedFan->sex = isset($fanRow['sex']) ? $fanRow['sex'] : '';
                $cachedFan->memberId = isset($fanRow['member_id']) ? $fanRow['member_id'] : '';
                $cachedFan->headimgUrl = isset($fanRow['headimg_url']) ? $fanRow['headimg_url'] : '';
                $cachedFan->name = isset($fanRow['name']) ? $fanRow['name'] : '';  
                $cachedFan->isFollowed = $fanRow['is_followed'];
                $cachedFan->cache(3600);
            } else {
                return null;
            }

        }
        return $cachedFan;
    }

    /**
     * @param $openId
     */
    public static function removeCache($openId)
    {
        FanCacheObject::remove($openId);
    }


}
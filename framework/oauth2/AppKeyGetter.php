<?php
/**
 * Created by hwl on 15-9-15.
 */


namespace app\framework\oauth2;
use app\framework\db\EntityBase;

class AppKeyGetter
{
    /**
     * @var Repository
     */
    private $_repository;

    public function __construct($repository=null)
    {
        if($repository == null){
            $repository = new Repository();
        }

        $this->_repository = $repository;
    }

    /**
     * @param $tenantCode
     * @param bool|true $cache
     * @return array|mixed
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function getAppIdAndSecret(  $cache=true)
    {
        $appId = APP_NAME;
        if (empty($appId)) {
            throw new \Exception('appid 不能为空');
        }
 

        $cacheKey = 'oauth2:secret:  ' . strtolower($appId);

        if ($cache == false) { 
            $db = EntityBase::getDb();
            $appSecret = $this->_repository->getSecretByAppId($db, $appId);
            if ($appSecret == false) {
                throw new \Exception('未能找到appid, ' . $appId . '的记录');
            }

            return [$appId, $appSecret];

        } else {
            $cachedItem = \Yii::$app->cache->get($cacheKey);
            if ($cachedItem == false) { 
                 $db = EntityBase::getDb();
                $appSecret = $this->_repository->getSecretByAppId($db, $appId);
                if ($appSecret == false) {
                    throw new \Exception('未能找到appid, ' . $appId . '的记录');
                }
                \Yii::$app->cache->set($cacheKey, [$appId, $appSecret], 3600);
                return [$appId, $appSecret];
            } else {
                return $cachedItem;
            }
        }

    }
}
<?php

namespace app\framework\biz\cache;

use yii\db\Query;
use app\framework\cache\CachePackageManger;
use app\framework\db\EntityBase;
class OAuth2CacheManager
{
    /**
     * 获取client_secret
     * @param $appId
     * @return bool|null|object|string
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     */
    public static function getSecretByAppId($appId)
    {
        if (empty($appId)) {
            throw new \InvalidArgumentException('$appId');
        }

        $cm = CachePackageManger::instance('oauth2:secret:' . $appId);
        $secret = $cm->get();
        if (!is_null($secret)) {
            return $secret;
        } 
        $db = EntityBase::getDb(); 
        $query = new Query();
        $cmd = $query->from('oauth_clients')
            ->where('client_id=:client_id', [':client_id' => $appId])
            ->select('client_secret')
            ->createCommand($db); 
        $result = $cmd->queryScalar();
        if ($result == false) {
            throw new \Exception('client_secret获取失败, client_id: ' . $appId);
        } else {
            $secret = $result;
            $cm->set($secret, 3600);
            return $secret;
        }
    }

}
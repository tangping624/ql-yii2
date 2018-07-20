<?php
/**
 * Created by hwl on 15-9-15.
 */


namespace app\framework\oauth2;


use yii\db\Query;

class Repository
{
    /**
     * @param \yii\db\Connection $dbconn
     * @param string $appId
     * @return false|string
     */
    public function getSecretByAppId($dbconn, $appId)
    {
        $query = new Query();
        $cmd = $query->from('oauth_clients')
            ->where('client_id=:client_id', [':client_id' => $appId])
            ->select('client_secret')
            ->createCommand($dbconn);

        return $cmd->queryScalar();

    }
}
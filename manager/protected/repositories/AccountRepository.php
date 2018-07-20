<?php

namespace app\repositories;
 
use yii\db\Query;

class AccountRepository
{
    public function validateUser($uid, $pwd, $db)
    {
        $query = new \yii\db\Query();
        $cmd = $query->from('t_user')
            ->where('id=:id and is_deleted=0')
            ->select('pwd')
            ->createCommand($db)
            ->bindValue(':id', $uid);

        $password = $cmd->queryScalar();
        if ($password == false) {
            return false;
        }

        $match = \app\framework\utils\Security::validatePassword($pwd, $password);
        return $match;
    }

    public function updatePassword($uid, $npwd, $db)
    {
        $password = \app\framework\utils\Security::encryptByPassword($npwd);

        return \app\framework\db\SqlHelper::update('t_user', $db, ['pwd' => $password], ['id' => $uid]);
    }

    /**
     * @param $account
     * @param $tenantDb
     * @return array|false
     */
    public function getUserInfoByWxUserId($account, $tenantDb)
    {
        if (empty($account)) {
            return null;
        }

        $query = new \yii\db\Query();
        $cmd = $query->from('t_user')
            ->where(['account' => $account, 'is_deleted' => 0])
            ->createCommand($tenantDb);
        return $cmd->queryOne();
    }
 
 
 
     
    public function getWechatUserByOpenid($openid)
    {
        $query = new Query();
        $cmd = $query->where('openid=:openid', [':openid' => $openid])
            ->from('wechat_user')
            ->createCommand();
        return $cmd->queryOne();
    }
}

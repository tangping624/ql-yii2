<?php

namespace app\framework\auth;

use yii\db\Query;
use app\framework\db\EntityBase;
use app\framework\entities\TAccount;

class Store
{
    /**
     * 获取指定的分库用户信息
     * @param string $account
     * @param array $db_dsn
     * @return array|null
     */
    public static function getUser($account)
    {
        if(empty($account)){
            return null;
        }

        $query = new \yii\db\Query();
        $cmd = $query->from('t_user')
            ->where(['account'=>$account, 'is_deleted'=>0])
            ->createCommand(EntityBase::toConnection());
        $row = $cmd->queryOne();
        return $row == false ? null : $row;
    }

    public static function getMember($mobile)
    {
        if(empty($mobile)){
            return null;
        }

        $query = new \yii\db\Query();
        $cmd = $query->from('h_member')
            ->where(['mobile'=>$mobile, 'is_deleted'=>0])
            ->createCommand(EntityBase::toConnection());
        $row = $cmd->queryOne();
        return $row == false ? null : $row;
    }
    /**
     * 获取指定的分库用户信息
     * @param string $account
     * @param array $db_dsn
     * @return array|null
     */
    public static function getUserById($userid)
    {
        if(empty($userid)){
            return null;
        }
        $query = new \yii\db\Query();
        $cmd = $query->from('t_user')
            ->where(['id'=>$userid, 'is_deleted'=>0])
            ->createCommand(EntityBase::toConnection());
        $row = $cmd->queryOne();
        return $row == false ? null : $row;
    }

    public static function getMemberById($userid)
    {
        if(empty($userid)){
            return null;
        }
        $query = new \yii\db\Query();
        $cmd = $query->from('h_member')
            ->where(['id'=>$userid, 'is_deleted'=>0])
            ->createCommand(EntityBase::toConnection());
        $row = $cmd->queryOne();
        return $row == false ? null : $row;
    }
    /**
     * 获取指定的用户绑定信息
     * @param string $openid
     * @param array $type
     * @return array|null
     */
    public static function getAccount($openid,$type)
    {
        if(empty($openid)){
            return null;
        }
        if(empty($type)){
            return null;
        }
        return TAccount::findOne(['openid'=>$openid,'login_type'=>$type,'is_deleted'=>0]);
    }
    /**
     * 更新三方登陆数据
     * @param TAccount $account
     * @return type
     */
    public static  function updateAccount(TAccount $account){
        return   $account->save();
    }

    public static function saveAccount(){

    }

}

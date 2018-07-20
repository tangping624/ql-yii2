<?php

namespace app\services;
 
use app\repositories\AccountRepository;
use app\framework\db\EntityBase;

class AccountService
{
    private $_accountRepo; 

    public function __construct(AccountRepository $accountRepo)
    {
        $this->_accountRepo = $accountRepo; 
    }

    public function validateUser($uid, $pwd)
    {
        $db = EntityBase::getDb();

        return $this->_accountRepo->validateUser($uid, $pwd, $db);
    }

    public function updatePassword($uid, $npwd)
    {
        $db = EntityBase::getDb();
        return $this->_accountRepo->updatePassword($uid, $npwd, $db);
    }

    /**
     * @param string $wxUserId
     * @param string $tenantCode 租户代码
     * @return array|false
     */
    public function getUserInfoByWxUserId($wxUserId)
    {
        $db =  EntityBase::getDb();
        $userRow = $this->_accountRepo->getUserInfoByWxUserId($wxUserId, $db);
        return $userRow;
    }

    /**
     * 更新企业号user id
     * @param $qyhUserId
     * @param $account
     * @param $tenantCode
     */
    public function updateQyhAccount($qyhUserId, $account)
    {
        $db =  EntityBase::getDb();
        $this->_accountRepo->updateQyhAccount($qyhUserId, $account, $db);
    }

    /**
     * @param string $tenantCode 租户代码
     * @return array 第一个元素为CorpID,第二个为管理组secret
     */
    public function getCorpApp( $appCdoe)
    {
        $db =  EntityBase::getDb();
        return $this->_accountRepo->getCorpApp($appCdoe, $db);
    }

    /**
     * @param $userId
     * @param $tenantCode
     * @return array
     */
    public function getAppCodeListOfUser($userId)
    {
        $db =  EntityBase::getDb();
        return $this->_accountRepo->getAppCodeListOfUser($userId, $db);
    }



}

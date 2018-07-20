<?php

namespace app\modules\api\services;

use app\modules\api\repositories\PublicAccountRepository;
use app\framework\db\EntityBase;
class PublicAccountService
{
    private $_publicAccountRepository;
    
    public function __construct(PublicAccountRepository $publicAccountRepository)
    {
        $this->_publicAccountRepository = $publicAccountRepository;
    }
    
    /**
     * 根据appId获取租户代码和租户数据库连接
     * @param string $appId
     * @return array
     * @throws \Exception
     */
    public function getTenantInfoByAppId( )
    { 
        $dbConn =EntityBase::getDb();
        return ['code' => '', 'dbConn' => $dbConn];
    }
    
    /**
     * @param string $accountId
     * @return array|false
     */
    public function getMch($accountId)
    {
        if (empty($accountId)) {
            throw new \InvalidArgumentException('$accountId');
        }

        $accountRow = $this->_publicAccountRepository->getMch($accountId, ['mch_key']);
        if ($accountRow == false) {
            return false;
        }

        $mchKey = $accountRow['mch_key'];
        if (!empty($mchKey)) {
            if (strlen($mchKey) == 32) {
                $accountRow['mch_half_key'] = substr($mchKey, 0, 16);
            } else {
                throw new \InvalidArgumentException('mch_key长度不等于32!');
            }

        }
        return $accountRow;
    }

    /**
     * @param $corpId
     * @return false|array
     * @throws \Exception
     */
    public function getAccountIdByCorpId($corpId)
    {
        if (empty($corpId)) {
            throw new \InvalidArgumentException('$corpId');
        }

        $accountId = $this->_publicAccountRepository->getAccountIdByCorpId($corpId);
//        if ($accountId == false) {
//            throw new \Exception("找不到公司{$corpId}的公众号!");
//        }
        return $accountId;
    }
}

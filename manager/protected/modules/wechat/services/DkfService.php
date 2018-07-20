<?php

namespace app\modules\wechat\services;

use app\framework\biz\cache\OrganizationCacheManager;
use app\models\Organization;
use app\modules\ServiceBase;
use app\modules\wechat\repositories\DkfRepository;

/**
 * Description of DkfService
 *
 * @author weizs
 */
class DkfService extends ServiceBase
{
    private $_dkfRepository;

    public function __construct(DkfRepository $dkfRepository)
    {
        $this->_dkfRepository=$dkfRepository;
    }

    /**
     * 通过OpenId&AccountId获取会员信息
     * @param $openId
     * @param $accountId
     * @return array|bool
     */
    public function getMember($openId,$accountId)
    {
        $memberInfo=$this->_dkfRepository->getMember($openId,$accountId);  
        return $memberInfo;
    }

    /**
     * 获取所有公司信息
     * @param $kfAccount
     * @param $accountId
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getAllCompany($kfAccount,$accountId)
    {
        $tReader = \Yii::$container->get('app\framework\biz\tenant\TenantReaderInterface');
        $tenantCode = $tReader->getCurrentTenantCode();
        $selected=$this->_dkfRepository->getWorkerCorpList($kfAccount,$accountId);
        $company=OrganizationCacheManager::getAllCompany($tenantCode);
        $companyList=array_merge([['id'=>'','name'=>'无会籍']],$company);
        return [
            'company'=>$companyList,
            'selected'=>$selected
        ];
    }

    /**
     * 通过CorpId&AccountId获取工号列表
     * @param $corpId
     * @param $accountId
     * @return array
     */
    public function getWorkers($corpId,$accountId)
    {
        $workerList=$this->_dkfRepository->getWorkers($corpId,$accountId);
        $workerAccountList=[];
        if($workerList){
            foreach($workerList as $worker){
                $workerAccountList[]=$worker['kf_account'];
            }
        }
        return $workerAccountList;
    }

    /**
     * 设置工号对应区域
     * @param $accountId
     * @param $workerAccount
     * @param $corp_ids
     * @return mixed
     */
    public function setWorkerCorpIds($accountId,$workerAccount,$corp_ids)
    {
        $kfSetting=['account_id'=>$accountId,'kf_account'=>$workerAccount];

        $weixinHelper = new WeixinHelperService();
        $kfList = [];
        try {
            $kfList = $weixinHelper->getKfList($accountId);
        } catch (\Exception $ex) {
        }

        return $this->_dkfRepository->setWorkerCorpIds($kfSetting,$corp_ids,$kfList);
    }

    /**
     * 保存关键字规则
     * @param array $keyword
     * @throws \Exception
     * @return bool
     */
    public function insertKeyword($keyword)
    {
        return $this->_dkfRepository->insertKeyword($keyword);
    }

    /**
     * 通过AccountId获取关键字列表
     * @param $accountId
     * @return array
     */
    public function getKeywordByAccountId($accountId)
    {
        return $this->_dkfRepository->getKeywordByAccountId($accountId);
    }

}

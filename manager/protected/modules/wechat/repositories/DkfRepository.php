<?php

namespace app\modules\wechat\repositories;

use app\entities\EntityBase;
use app\framework\db\SqlHelper;
use app\framework\utils\DateTimeHelper;
use app\framework\utils\StringHelper;
use app\modules\RepositoryBase;
use yii\db\Query;

/**
 * Description of DkfRepository
 *
 * @author weizs
 */
class DkfRepository extends RepositoryBase
{
    private $_dbConnection;
    public function __construct($dbConnetion = null)
    {
        $this->_dbConnection = $dbConnetion ?:EntityBase::getDb();
    }
    
    /**
     * 通过OpenId&AccountId获取会员信息
     * @param $openId
     * @param $accountId
     * @return array|bool
     */
    public function getMember($openId,$accountId)
    {
        $query = new Query();
        $rows = $query->from('p_fan f')
            ->leftJoin('h_member m','f.member_id=m.id') 
            ->leftJoin('h_member_level l','m.level_id=l.id')
            ->select('m.id member_id,f.nick_name,m.name,m.type,m.sex,m.id_code,m.mobile,l.name member_level')
            ->where(['f.openid' => $openId,'f.account_id'=>$accountId, 'f.is_deleted'=>0])
            ->createCommand($this->_dbConnection)
            ->queryOne();

        return $rows;
    }
 

    /**
     * 通过CorpId&AccountId获取账号列表
     * @param $corpId
     * @param $accountId
     * @return array
     */
    public function getWorkers($corpId,$accountId)
    {
        $corpId=$corpId==null?'':$corpId;
        $query = new Query();
        $rows = $query->from('p_kf_setting k')
            ->select('k.kf_account')
            ->distinct('k.kf_account')
            ->where(['k.corp_id' => $corpId,'k.account_id' => $accountId, 'k.is_deleted'=>0])
            ->createCommand($this->_dbConnection)
            ->queryAll();
        return $rows;
    }

    /**
     * 通过AccountId获取关键字列表
     * @param $accountId
     * @return array
     */
    public function getKeywords($accountId)
    {
        $query = new Query();
        $rows = $query->from('p_kf_keyword k')
            ->select('k.keyword')
            ->where(['k.account_id' => $accountId, 'k.is_deleted'=>0])
            ->createCommand($this->_dbConnection)
            ->queryAll();
        return $rows;
    }

    /**
     * 设置工号对应区域
     * @param $kfSetting
     * @param $corpIds
     * @return mixed
     * @throws \yii\db\Exception
     */
    public function setWorkerCorpIds($kfSetting,$corpIds,$kfList)
    {
        $kfAccount=$kfSetting['kf_account'];
        $accountId=$kfSetting['account_id'];
        $conn = $this->_dbConnection;

        $corpArray=$this->getWorkerCorpList($kfAccount,$accountId);
        $currentDateTime = date('Y-m-d H:i:s', time());
        //删除已存在
        $delArray=[];
        $compareArray=[];
        if($corpArray&&count($corpArray)){
            foreach($corpArray as $corp){
                $corpId=$corp['corp_id'];
                $compareArray[]=$corpId;
                if(!in_array($corpId,$corpIds)){
                    $delArray[]=$corpId;
                }
            }

            if(count($delArray)){
                $conn->createCommand()->update("p_kf_setting", ['is_deleted' => 1, 'modified_on' => $currentDateTime],['kf_account'=>$kfAccount,'account_id'=>$accountId,'corp_id'=>$delArray])->execute();
            }
        }

        if (count($kfList)>0) {
            /*$kfstr = "";
            foreach ($kfList as $kf) {
                if ($kfstr == "")  {
                    $kfstr = "'".$kf."'";
                } else {
                    $kfstr .= ",'".$kf."'";
                }
            }*/
            //$conn->createCommand()->update("p_kf_setting", ['is_deleted' => 1, 'modified_on' => $currentDateTime],"kf_account not in (".$kfstr.") and account_id='".$accountId."'")->execute();
            $conn->createCommand()->update("p_kf_setting", ['is_deleted' => 1, 'modified_on' => $currentDateTime], ['AND', ['NOT IN', 'kf_account', $kfList], ['=', 'account_id', $accountId]])->execute();
        }

        $batchKfSetting=[];
        foreach($corpIds as $corpId){
            if(!in_array($corpId,$compareArray)){
                $tmpKfSetting=[];
                $tmpKfSetting['id'] = StringHelper::uuid();
                $tmpKfSetting['kf_account'] = $kfAccount;
                $tmpKfSetting['corp_id'] = $corpId;
                $tmpKfSetting['account_id'] = $accountId;
                $tmpKfSetting['created_on'] = DateTimeHelper::now();
                $tmpKfSetting['modified_on'] = $tmpKfSetting['created_on'];
                $tmpKfSetting['is_deleted'] = 0;
                $batchKfSetting[]=$tmpKfSetting;
            }
        }

        $insertSize=count($batchKfSetting);

        if($insertSize){
            $conn->createCommand()->batchInsert('p_kf_setting', ['id','kf_account','corp_id','account_id','created_on','modified_on','is_deleted'],$batchKfSetting)->execute();
        }
        return $insertSize;
    }

    /**
     * 通过KfAccount&AccountId获取账号列表
     * @param $kfAccount
     * @param $accountId
     * @return array
     */
    public function getWorkerCorpList($kfAccount,$accountId)
    {
        $query = new Query();
        $rows = $query->from('p_kf_setting k')
            ->select('k.corp_id')
            ->distinct('k.corp_id')
            ->where(['k.kf_account' => $kfAccount,'k.account_id' => $accountId, 'k.is_deleted'=>0])
            ->createCommand($this->_dbConnection)
            ->queryAll();
        return $rows;
    }

    /**
     * 通过AccountId获取关键字列表
     * @param $accountId
     * @return array
     */
    public function getKeywordByAccountId($accountId)
    {
        $query = new Query();
        $rows = $query->from('p_kf_keyword k')
            ->select('k.*')
            ->where(['k.account_id' => $accountId, 'k.is_deleted'=>0])
            ->createCommand($this->_dbConnection)
            ->queryOne();
        return $rows;
    }

    /**
     * 保存关键字规则
     * @param array $keyword
     * @throws \Exception
     * @return bool
     */
    public function insertKeyword($keyword)
    {
        $conn = $this->_dbConnection;
        $transaction = $conn->beginTransaction();

        try {
            $keywords= $this->getKeywords($keyword["account_id"]);
            if (count($keywords)<=0) {
                $conn->createCommand()->insert('p_kf_keyword', $keyword)->execute();
            } else {
                $data = ['keyword' => $keyword["keyword"]];
                SqlHelper::update('p_kf_keyword', $conn, $data, ['account_id' => $keyword["account_id"]]);
            }

            $transaction->commit();
            return true;

        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
    }

}

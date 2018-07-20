<?php
namespace app\modules\wechat\repositories;
/**
 * @todo Description
 * @author fanwq
 */
use app\framework\db\SqlHelper;
use app\modules\RepositoryBase;
use app\entities\PMassMsgApprover;
use yii\db\Query;
use app\framework\utils\StringHelper;

class MassMsgAuthRepository extends RepositoryBase
{
    private $db;
    private $table;
    
    public function __construct() {
        $this->db = PMassMsgApprover::getDb();
        $this->table = PMassMsgApprover::tableName();
    }


    public function updateAuth($mass_msg_id, $status)
    {
        try {
            SqlHelper::update('p_mass_msg_auth', $this->db, ['status'=>$status], ['mass_msg_id'=>$mass_msg_id]);
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    
    public function deleteAuth($mass_msg_id)
    {
        try {
            SqlHelper::update('p_mass_msg_auth', $this->db, ['is_deleted'=>1], ['mass_msg_id'=>$mass_msg_id]);
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function getByMassMsgId($mass_msg_id)
    {
        $query = new Query();
        $query = $query->select('id')
                ->from('p_mass_msg_auth')
                ->where('is_deleted=0')
                ->andWhere(['=', 'mass_msg_id', $mass_msg_id])
                ->orderBy('created_on desc')
                ->limit(1);
        
        return $query->createCommand($this->db)->queryOne();
    }

    
    public function getAccount($public_id)
    {
        $query = new Query();
        $query = $query->select('*')
                ->from('p_account')
                ->where('is_deleted=0')
                ->andWhere(['=', 'id', $public_id]);
        
        return $query->createCommand($this->db)->queryOne();
    }
    
    public function getMassTitle($mass_msg_id)
    {
        $query = new Query();
        $query = $query->select('mpnews_title')
                ->from('p_mass_msg')
                ->where('is_deleted=0')
                ->andWhere(['=', 'id', $mass_msg_id]);
        
        return $query->createCommand($this->db)->queryScalar();      
    }


    public function getMassMsgAuthById($id)
    {
        $query = new Query();
        $query = $query->select('*')
            ->from('p_mass_msg_auth')
            ->where('is_deleted=0')
            ->andWhere(['=', 'id', $id]);

        return $query->createCommand($this->db)->queryOne();
    }

    public function getMassMsgAuthByFanId($msgId, $fanId)
    {
        $query = new Query();
        $query = $query->select('*')
            ->from('p_mass_msg_auth')
            ->where('is_deleted=0')
            ->andWhere(['=', 'mass_msg_id', $msgId])
            ->andWhere(['=', 'applicant_fan_id', $fanId]);

        return $query->createCommand($this->db)->queryOne();
    }

    public function getMassMsgAuthByMsgId($msgId)
    {
        $query = new Query();
        $query = $query->select('*')
            ->from('p_mass_msg_auth')
            ->where('is_deleted=0')
            ->andWhere(['=', 'mass_msg_id', $msgId]);

        return $query->createCommand($this->db)->queryAll();
    }

    /**
     * 获取账号原始ID
     * @param $id
     * @return null
     */
    public function getWeChatOriginalId($id)
    {
        $query = new Query();
        $query = $query
            ->select('original_id')
            ->from('p_account')
            ->where('p_account.is_deleted=0')
            ->andWhere(['=', 'p_account.id', $id]);

        $connection = $this->db;
        $command = $query->createCommand($connection);
        $rows = $command->queryOne();
        $we_chat_number = null;
        if (isset($rows)) {
            $we_chat_number = $rows["original_id"];
        }
        return $we_chat_number;
    }  
    
    /**
     * 查找关联指定会员id的已关注粉丝数据
     * @param guid $memberId
     * @param array|string $fanCols 要查找的粉丝表字段
     * @return array
     */
    public function getBindFollowedFans($memberId, $public_id, $fanCols)
    {
        $query = new Query();
        $rows = $query->from('p_fan')
            ->where(['member_id' => $memberId, 'is_followed' => 1, 'is_deleted' => 0])
            ->andWhere(['=', 'account_id', $public_id])
            ->select($fanCols)
            ->createCommand($this->db)
            ->queryAll();

        return $rows;
    }    
    
    public function insertTemplateMsgLog($templateMsgLogRowData)
    {
        if (!array_key_exists('id', $templateMsgLogRowData)
            || empty($templateMsgLogRowData['id'])) {
            $templateMsgLogRowData['id'] = StringHelper::uuid();
        }
        
        $this->db->createCommand()->insert('p_template_msg', $templateMsgLogRowData)->execute();
        return $templateMsgLogRowData;
    }    
    
    public function getWechat($accountId)
    {
        $query = new Query();
        $wechat= $query->from('p_account')
            ->where(['id' => $accountId, 'is_deleted' => 0])
            ->select("original_id")
            ->createCommand($this->db)
            ->queryScalar();

        return $wechat;
    }    
    
    public function getAuthorizer($mass_msg_id)
    {
        $query = new Query();
        $query = $query->select('authorizer_member_id')
                ->from('p_mass_msg_auth')
                ->where('is_deleted=0')
                ->andWhere(['=', 'mass_msg_id', $mass_msg_id]);
        
        return $query->createCommand($this->db)->queryAll();
    }    
    
    /**
     * 粉丝是否登陆
     * @param type $memberId
     * @return type
     */
    public function isFanLogged($memberId)
    {
        $query = (new Query())
            ->select('count(1) as total')
            ->from('p_fan')
           // ->where("is_deleted=0 and member_id='{$memberId}'");
            ->where(['is_deleted'=>0, 'member_id'=>$memberId]);
            
        $count = $query->createCommand($this->db)->queryScalar();
        return $count['total'] > 0 ? true : false;
    }
    
    public function getUserCorpId($userId)
    {
        $query = (new Query())
            ->select('org_id')
            ->from('t_user')
            //->where("is_deleted=0 and id='{$userId}'");
            ->where(['is_deleted'=>0, 'id'=>$userId]);
            
        return $query->createCommand($this->db)->queryScalar();   
    }
}



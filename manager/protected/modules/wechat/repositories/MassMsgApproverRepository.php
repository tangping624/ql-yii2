<?php
namespace app\modules\wechat\repositories;
/**
 * @todo Description
 * @author fanwq
 */
use app\modules\RepositoryBase;
use app\entities\PMassMsgApprover;
use yii\db\Query;

class MassMsgApproverRepository extends RepositoryBase
{
    private $db;
    private $table;
    // 集团id
    const GROUP_ID_STRING = '11b11db4-e907-4f1f-8835-b9daab6e1f23';
    
    public function __construct() {
        $this->db = PMassMsgApprover::getDb();
        $this->table = PMassMsgApprover::tableName();
    }
    
    /**
     * 按照条件获取 审核管理员
     * @param type $condition
     * @return type
     */
    public function getApproverByCondition($condition)
    {
        $query = new Query();
        $query = $query   
            ->select('id, member_id')
            ->where('is_deleted=0')
            ->from($this->table); 
       
        if( isset($condition['limit']) ) {
            $query = $query->limit($condition['limit']);
        }
        
        return $query->createCommand($this->db)->queryAll();
    }
    
    /**
     * 更新
     * @param type $param
     * @param type $id
     * @return boolean
     * @throws \app\modules\wechat\repositories\Exception
     */
    public function updateEntity($param, $id)
    {
        try {
            SqlHelper::update($this->table, $this->db, $param, ['id' => $id]);
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }        
    }
    
    /**
     * 通过openid获取粉丝信息
     * @param type $openid
     * @return type
     */
    public function getFanInfo($condition)
    {
        $query = new Query();
        $query = $query 
            ->select('*')
            ->where('is_deleted=0')
            ->from('p_fan');
        if( isset($condition['openid']) ) {
            $query = $query->andWhere(['=', 'openid', $condition['openid']]);
            $query = $query->andWhere(['=', 'account_id', $condition['account_id']]);
        }
         if( isset($condition['id']) ) {
            $query = $query->andWhere(['=', 'id', $condition['id']]);
        }       
        return $query->createCommand($this->db)->queryOne();    
    }
 
    
    public function getApproverByAccountID($accountId)
    {
        $query = ( new Query() )
            ->select('a.member_id, f.id')
            ->from($this->table.' as a')
            ->innerJoin('p_fan as f', "a.member_id=f.member_id")
            ->where('a.is_deleted=0')
            ->andWhere(['=', 'a.account_id', $accountId])
            ->andWhere(['=', 'f.is_followed', 1])
            ->groupBy('a.member_id');
      
        $rows = $query->createCommand($this->db)->queryAll(); 
        return $rows;
    }    
    
    
    public function isAdmin($memberId, $accountId)
    {
        $query = ( new Query() )
            ->select('id')
            ->where('is_deleted=0')
            ->andWhere(['=', 'member_id', $memberId])
            ->andWhere(['=', 'account_id', $accountId])
            ->from($this->table);   
        $id = $query->createCommand($this->db)->queryScalar();  
        return $id;
    }
    
}

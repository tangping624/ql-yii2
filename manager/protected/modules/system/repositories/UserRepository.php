<?php
namespace app\modules\system\repositories; 
use app\modules\RepositoryBase; 
use app\entities\TUser;
use app\framework\db\EntityBase;
use app\framework\db\PageResult; 
class UserRepository extends RepositoryBase {
     
     /**
     * 插入实体
     * @param EntityBase $entity 实体
     * @return bool 是否插入成功
     */
    public function insert(EntityBase $entity)
    {
        return $entity->save();
    }

    /**
     * 更新实体
     * @param EntityBase $entity 实体
     * @return bool 是否更新成功
     */
    public function update(EntityBase $entity)
    {
        return $entity->save();
    }
    /**
     * 保存公众号用户
     * @param type $useraccount
     * @return type
     */
    public function  saveUserAccount($useraccount) {
        return $useraccount->save();
    }
    /**
     * 删除实体
     * @param EntityBase $entity 实体
     * @return bool 是否删除成功
     */
    public function delete(EntityBase $entity)
    {
        if (empty($entity) || empty($entity->id)) {
            return false;
        }
        $entity->is_deleted = 1;
        return $this->update($entity);
    }

    /**
     * 获取有效实体（非删除的实体）
     * @param EntityBase $entity 实体
     * @param $id 主键
     * @return array|null|\yii\db\ActiveRecord 返回实体对象
     */
    public function getOne(EntityBase $entity, $id)
    {
        return $entity->find()->where(['id' => $id])
            ->andWhere(['is_deleted' => 0])
            ->one();
    }
    
    public function getUserList($skip, $limit,$isenable,$userinfo=''){
        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }  
        $where = " where a.is_deleted=0 ";
        $params = [];
        if (strlen($isenable)>0)
        {
            $where .= " and ( a.enabled = :enabled)";
            $params[':enabled'] = $isenable; 
        }
        if(!empty($userinfo)){
              $where .= " and (a.mobile like CONCAT('%', :userinfo, '%') or a.name like CONCAT('%', :userinfo, '%'))";
              $params[':userinfo'] = $userinfo;
        } 
        $sql = "select SQL_CALC_FOUND_ROWS a.id,a.name,a.account,a.mobile,a.email,a.enabled   
             from t_user  a  " .$where . " limit :skip,:limit";
        $params[':skip']=$skip;
        $params[':limit'] = intval($limit);
        $pageResult = new PageResult();
        $db = TUser::getDb();
        $cmd = $db->createCommand($sql,$params) ; 
        $pageResult->items = $cmd->queryAll();
        
        $sql = "SELECT FOUND_ROWS() AS count;";
        $rows = $db->createCommand($sql)->queryAll();
        $pageResult->total = $rows[0]["count"];   
        return $pageResult; 
    }  
     
    /**
     * 根据userId获取用户信息
     * @param $userId
     * @return array
     */
    public function getUserInfo($userId)
    { 
        $db = TUser::getDb();
        $query = new \yii\db\Query();
        return $query->from('t_user')
            ->where(['t_user.id' => $userId, 't_user.is_deleted' => 0])
            ->select('t_user.name,t_user.account,t_user.mobile,t_user.email')
            ->createCommand($db)
            ->queryOne();
    }
}

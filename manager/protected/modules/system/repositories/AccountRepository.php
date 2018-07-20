<?php
namespace app\modules\system\repositories; 
use app\modules\RepositoryBase; 
use app\entities\PAccount;
use app\entities\TUserAccount; 
use app\framework\db\PageResult; 
use app\framework\db\SqlHelper;
class AccountRepository extends RepositoryBase { 
    /**
     * 查询当前用户所有有权限的公众号
     * @param type $userid
     * @return type
     */
    public function getAccountList($skip, $limit,$userid,$level,$name=''){ 
        if (empty($userid)) {
            throw new \InvalidArgumentException('$userid');
        }
        $pageResult = new PageResult();
        $params = [];
        $where = " where a.is_deleted=0 ";
          if (strlen($name)>0)
        {
            $where .= " and ( a.name like CONCAT('%', :name, '%') )";
            $params[':name'] = $name; 
        }
        if($level >=2){
             $where .= " and ( b.user_id = :user_id) and b.is_deleted=0 ";
            $params[':user_id'] = $userid; 
        }
        //权限过虑？？？
        $sql = "select SQL_CALC_FOUND_ROWS a.id, a.`name`,a.headimg_url,a.qrcode_url,a.package_type  
                from p_account a 
                inner join t_user_account b on a.id = b.account_id
                " . $where . " limit :skip,:limit";
        if($level==1){
            $sql = "select SQL_CALC_FOUND_ROWS a.id, a.`name`,a.headimg_url,a.qrcode_url ,a.package_type  
                    from p_account a 
                    " . $where . " limit :skip,:limit";
        }
        $db=PAccount::getDb();
        $params[':skip']=$skip;
        $params[':limit'] = intval($limit);
        $pageResult->items=  $db->createCommand($sql,$params)->queryAll(); 
        $sql = "SELECT FOUND_ROWS() AS count;";
        $pageResult->total = $db->createCommand($sql)->queryScalar(); 
        return $pageResult; 
    }
    
    
    /**
     * 获取公众号数据，数组
     * @param type $id
     * @return type
     */
    public function getAccount()
    {
        $query = (new \yii\db\Query())
            ->select('p_account.id,p_account.name,p_account.original_id,p_account.mch_ssl_cert,p_account.mch_ssl_key,p_account.wechat_number,p_account.type,p_account.app_id,p_account.app_secret,p_account.mch_id,p_account.mch_key,p_account.token,p_account.headimg_url,p_account.qrcode_url,p_account.attention_url')
            ->from('p_account') 
            ->where('p_account.is_deleted=0') ; 
        $connection = PAccount::getDb();
        $command = $query->createCommand($connection);
        $rows = $command->queryOne();
        return $rows;
    } 
    /**
     * 修改公众号信息
     * @param string $id
     * @param string $column
     * @param string $value
     * @throws \Exception
     * @return bool
     */
    public function updateAccountInfo($id, $column, $value)
    {
        $conn = PAccount::getDb();

        $data = [$column => $value];
        try {
            SqlHelper::update('p_account', $conn, $data, ['id' => $id]);
            return true;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }
        /**
     * 删除公众号信息
     * @param string $id
     * @throws \Exception
     * @return bool
     */
    public function removeAccountInfo($id)
    {
        $conn = PAccount::getDb(); 
        $data = ['is_deleted' => 1];
        try {
            SqlHelper::update('p_account', $conn, $data, ['id' => $id]); 
            return true;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }  
}

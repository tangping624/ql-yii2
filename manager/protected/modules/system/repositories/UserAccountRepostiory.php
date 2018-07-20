<?php
namespace app\modules\system\repositories; 
use app\modules\RepositoryBase; 
use app\entities\TUserAccount; 
class UserAccountRepostiory extends RepositoryBase {  
    public function getUserAccount($userid,$level){
        if (empty($userid)||empty($level)) {
            return [];
        } 
        $sql = "select a.account_id from t_user_account a 
                inner join p_account b on a.account_id=b.id 
                where a.user_id=:userid and a.is_deleted=0 and b.is_deleted=0;";
        //系统管理员有所有权限
        if($level==1){
            $sql = "select id as account_id from p_account where is_deleted=0;";
        }
        return TUserAccount::getDb()->createCommand($sql,[':userid'=>$userid])->queryAll();  
    }
    
    /**
     * 获取当前公众号所有有权限的用户
     * @param type $accountId
     * @return type
     */
    public function getAccountUser($accountId){
        if (empty($accountId)) {
            return [];
        } 
        $sql = "select user_id from t_user_account where is_deleted=0 and account_id=:account_id"; 
        return TUserAccount::getDb()->createCommand($sql,[':account_id'=>$accountId])->queryAll();  
    }
     
    
    
    
}

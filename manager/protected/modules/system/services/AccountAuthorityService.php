<?php
 namespace app\modules\system\services;
 use app\modules\ServiceBase;
 use app\modules\system\repositories\UserAccountRepostiory;
 
class AccountAuthorityService extends ServiceBase{  
     private $_userAccountRepostiory;

    /**
     * 构造函数
     * @param UserRepository $userAccountRepostiory 
     * */
    public function __construct(UserAccountRepostiory $userAccountRepostiory)
    {
        $this->_userAccountRepostiory = $userAccountRepostiory;
    }
    /**
     * 检测用户是否有某个公众号权限
     * @param type $userid
     * @param type $accountid
     * @param type $level
     * @return boolean
     */
    public function checkHaveAuthority($userid,$accountid,$level){
        if (empty($userid)||empty($level)||empty($accountid)) {
            return false;
        }
        $arrAccounts = $this->getUserAccount($userid, $level);
        if(in_array($accountid,$arrAccounts)){
            return true;
        }
        return false;
    }
    /**
     * 获取用户所有公众号权限
     * @param type $userid
     * @param type $level
     * @return type
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function getUserAccount($userid,$level){
        if (empty($userid)) {
            throw new \InvalidArgumentException('$userid');
        }
        if (empty($level)) {
            throw new \InvalidArgumentException('$level');
        }
         try {
            // 优先从缓存中取
            $cacheKey = "userid_account_$userid";
            $cache = \Yii::$app->cache;
            if ($cache->exists($cacheKey)) {
                if (!empty($cache[$cacheKey])) {
                    return $cache[$cacheKey];
                }
            }
           $arrAccounts = $this->_userAccountRepostiory->getUserAccount($userid, $level); 
            if (!empty($arrAccounts)) {
                $cache->set($cacheKey, $arrAccounts, 3600*24);
            }
            return $arrAccounts;
        } catch (\Exception $ex) {
            \Yii::error($ex->getMessage());
            throw $ex;
        } 
    }
    /**
     * 清除用户公众号缓存
     * @param type $userid
     * @return boolean
     */
    public function removeUserAccount($userid){
         // 优先从缓存中取
        $cacheKey = "userid_account_$userid";
        $cache = \Yii::$app->cache;
        if ($cache->exists($cacheKey)) {
            $cache->delete($cacheKey);
        }
        return true;
    }
    /**
     * 清除公众号中所有权限用户缓存
     * @param type $account_id
     * @return boolean
     * @throws \InvalidArgumentException
     */
    public function removeAccountUserCache($account_id){
         if (empty($account_id)) {
            throw new \InvalidArgumentException('$account_id');
        }
        $arrUser = $this->_userAccountRepostiory->getAccountUser($account_id);
        foreach($arrUser as $u){
            $this->removeUserAccount($u['user_id']);
        }
        return true;
    }
    
    
}

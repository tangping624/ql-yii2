<?php
 namespace app\modules\system\services;
 use app\modules\ServiceBase;
 use app\modules\system\repositories\AccountRepository; 
 use app\framework\utils\PagingHelper;
 use app\entities\PAccount;
 use app\framework\utils\DateTimeHelper;
class AccountService extends ServiceBase{ 
     private $_accountRepository; 

    /**
     * 构造函数
     * @param UserRepository $accountRepository 
     * */
    public function __construct(AccountRepository $accountRepository )
    {
        $this->_accountRepository = $accountRepository; 
    }  
    
    /**
     * 获取公众号实体
     * @param type $id
     * @return type
     * @throws \InvalidArgumentException
     */
    public function getAccount( ){ 
        return $this->_accountRepository->getAccount( );
    } 
    /**
     * 根据Id修改公众号信息
     * @param string $id
     * @param string $column
     * @param string $value
     * @return bool
     */
    public function updateAccountInfo($id, $column, $value)
    {
        return $this->_accountRepository->updateAccountInfo($id, $column, $value);
    }
  
    
}

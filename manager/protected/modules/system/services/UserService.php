<?php
 namespace app\modules\system\services;
 use app\modules\ServiceBase;
 use app\modules\system\repositories\UserRepository; 
 use app\framework\utils\PagingHelper;
 use app\framework\utils\Security;

 use app\entities\TUser;
  use app\entities\TUserAccount;
class UserService extends ServiceBase{
   
    private $_userRepository; 
    /**
     * 构造函数
     * @param UserRepository $userRepository 用户数据库对象
     * */
    public function __construct(UserRepository $userRepository)
    {
        $this->_userRepository = $userRepository; 
    }
    
    
    
    /**
     * 功能：新增用户信息
     * @param TUser $user 用户数据模型
     * @return boolean
     * */
    public function insert(TUser $user)
    {
        return $this->_userRepository->insert($user);
    }

    /**
     * 功能：更新用户信息
     * @param TUser $user 用户数据模型
     * @return boolean
     * */
    public function update(TUser $user)
    {
        return $this->_userRepository->update($user);
    }
  /**
     * 功能：更新用户信息
     * @param TUser $useraccount 用户数据模型
     * @return boolean
     * */
    public function saveUserAccount(TUserAccount $useraccount)
    {
        return $this->_userRepository->saveUserAccount($useraccount);
    }
    /**
     * 获取用户
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getUser($id)
    {
        if (empty($id)) {
            return null;
        }
        return $this->_userRepository->getOne(new TUser(), $id);
    }
     /**
     * 重置用户密码
     * @param $userId string 用户ID
     * @return bool 是否重置成功
     */
    public function chgPassword($userId,$password)
    {
        $entity = $this->getUser($userId);
        if (isset($entity) === false) {
            throw new InvalidParamException('未获取到该用户');
        } else { 
            $entity->pwd = Security::encryptByPassword($password);
            $result = $this->update($entity);
            return ['result' => $result, 'password' => $password, 'user' => $entity];
        }
    }
    
    public function getUserList($page , $pagesize,$isenable='',$userinfo=''){
         if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        } 
        $skip = PagingHelper::getSkip($page, $pagesize); 
        return  $this->_userRepository->getUserList($skip, $pagesize,$isenable,$userinfo,$userinfo); 
    } 
      
    public function getUserInfo($userid){
        return $this->_userRepository->getUserInfo($userid);
    }
    
     
}

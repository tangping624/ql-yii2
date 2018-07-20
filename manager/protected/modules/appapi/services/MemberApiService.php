<?php 
namespace app\modules\appapi\services; 
use app\framework\sms\SmsService; 
use app\modules\ServiceBase;
use app\modules\appapi\repositories\MemberApiRepository;
use app\modules\appapi\repositories\CollectionRepository;
use app\modules\appapi\repositories\LobbyRepository;
use app\entities\member\HMember;
use app\framework\utils\Security;
use app\framework\utils\DateTimeHelper;
use app\framework\utils\PagingHelper;
class MemberApiService extends  ServiceBase {
    
    private $_memberApiRepository;
    private $_smsService;
    private $_collectionRepository;
    private $_lobbyRepository;
    
    
    public function __construct(MemberApiRepository $memberApiRepository,SmsService $smsService,CollectionRepository $collectionRepository, LobbyRepository $lobbyRepository)
    {
        $this->_memberApiRepository = $memberApiRepository;
        $this->_smsService = $smsService;
        $this->_collectionRepository=$collectionRepository;
        $this->_lobbyRepository=$lobbyRepository;
    }
    /*
     * 获取发送短信验证码
     */
    public  function sendVerifyCode($phone){ 
//        return  [
//                'result' => true,
//                'verifycode'=>'1234',
//                'code' => "200",
//                'msg' => "发送成功"
//            ]; 
        return $this->_smsService->sendVerifyCode($phone);
    }
    /**
     * 验检短信验证码
     * @param type $phone
     * @param type $inputcode
     * @return type
     */
    public  function VerifyCode($phone,$inputcode){ 
//          return [
//                    'result' => true,
//                    'code' => "200",
//                    'msg' => "验证成功",
//                    'phone' => $phone
//                ];
        return $this->_smsService->verifyCode($phone,$inputcode);
    }
    
     /**
      * 检验会员是否存在
      * @param type $pdo
      * @param type $phone
      * @return boolean
      */
     public  function checkUserIsExist($phone){
          return  $this->_memberApiRepository->checkUserIsExist($phone);
            
     }
      
     /**
      * 保存用户数据
      * @param TUser $user
      */
     public function save(HMember $user){
         return $this->_memberApiRepository->save($user);
     }

    //验证登入密码
    public function findPwd($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return $this->_memberApiRepository->findPwd($id);
    }

    //根据用户id更新密码
    public function stePwd($id, $password){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return $this->_memberApiRepository->stePwd( $id, $password);
    }

    //根据电话号码更新密码
    public function findUser($mobile,$password){
        if (empty($mobile)) {
            throw new \InvalidArgumentException('$mobile');
        }
        return $this->_memberApiRepository->findUser($mobile, $password);
    }
    
    //验证用户绑定手机号
    public function findPhone($mobile,$id ){
        if (empty($mobile)) {
            throw new \InvalidArgumentException('$mobile');
        }
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return $this->_memberApiRepository->findPhone($mobile,$id );
    }

    //注册保存
    public function SaveUser($mobile, $pwd){
        if (empty($mobile)) {
            throw new \InvalidArgumentException('$mobile');
        }
        if (empty($pwd)) {
            throw new \InvalidArgumentException('$pwd');
        }
        return  $this->_memberApiRepository->SaveUser($mobile, $pwd);
    }

    //验证原始密码
    public function checkUser($mobile, $pwd){
        if (empty($mobile)) {
            throw new \InvalidArgumentException('$mobile');
        }
        if (empty($pwd)) {
            throw new \InvalidArgumentException('$pwd');
        }
        return  $this->_memberApiRepository->checkUser($mobile, $pwd);
    }

    //更新密码
    public function updatePwd($id, $npwd){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_memberApiRepository->updatePwd($id, $npwd);
    }

    public function getTrack( $pagesize,$page, $memberId,$type){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);

        return  $this->_collectionRepository->getTrack( $skip,$pagesize,$memberId,$type);

    }

    public function getCollection( $pagesize,$page, $memberId,$type){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);

        return  $this->_collectionRepository->getCollection( $skip,$pagesize,$memberId,$type);

    }

    public function deleteTrack($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_collectionRepository->deleteTrack($id);
    }

    public function getPraise( $pagesize,$page, $memberId,$type){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);

        return  $this->_collectionRepository->getPraise( $skip,$pagesize,$memberId,$type);

    }

    public function getLobbyList($pagesize,$page, $memberId){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);
        return  $this->_lobbyRepository->getLobbyList($skip,$pagesize,$memberId);

    }
    public  function saveBlog($blog, $memberId){
        if (empty($blog)) {
            throw new \InvalidArgumentException('$blog');
        }
        $blog->source=2;
        $blog->created_on=date('Y-m-d H:i:s');
        $blog->created_by= $memberId;
        $blog->modified_on=date('Y-m-d H:i:s');
        $blog->modified_by= $memberId;
        $blog->ll_sum=0;
        $blog->dz_num=0;
        $blog->sc_num=0;
        return  $this->_lobbyRepository->saveBlog($blog);

    }
    
    public function getLobby($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }

        return  $this->_lobbyRepository->getLobby($id);
    }

    public function deleteLobby($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_lobbyRepository->deleteLobby($id);
    }

    //修改图像
    public function updatePhoto($id,$headimg_url){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }

        return  $this->_memberApiRepository->updatePhoto($id, $headimg_url);
    }

    //修改昵称
    public function updateName($id,$name){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_memberApiRepository->updateName($id,$name);
    }

    public function getUserInfo($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_memberApiRepository->getUserInfo($id);
    }
    
}

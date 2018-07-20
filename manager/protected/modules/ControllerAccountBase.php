<?php 
namespace app\modules;

use app\framework\web\extension\ManagerController;
use app\framework\auth\interfaces\UserSessionAccessorInterface;
use app\framework\auth\PublicAccountSession; 
 use yii\web\ForbiddenHttpException; 

class ControllerAccountBase extends ManagerController
{
    /**
     * @var 当前用户缓存信息
     */
    public $userSession;
    public $publicSession;
    public $userLevel;
    private $_publicAccountSessionAccessor;
    private $_publicAccountRepository;
    private $_accountAuthorityService;
    /**
     * @var UserSessionAccessorInterface
     */
    protected $userSessionAccessor;
    private $_popupLayout = '../../../../views/layouts/account.php';
     
     public function beforeAction($action)
    {
        if (parent::beforeAction($action)) { 
            if(!$this->checkPublicID()){
                throw  new ForbiddenHttpException('没有权限，禁止访问');  
            }
            return true;
        } 
        return false;
    }
    
    public function checkPublicID(){
        $public_id = \Yii::$app->request->get('public_id');
        if(empty($public_id)){
            $public_id= \Yii::$app->request->get('accountid');
        }
        $publicSession = $this->_publicAccountSessionAccessor->getAccountSession();
        if(is_null($publicSession)&&empty($public_id)){
            return false;
        }
        //检验公众号权限
        if($this->_accountAuthorityService-> checkHaveAuthority($this->userSession->user_id,$public_id,$this->userLevel)){
            return false;
        }
        if(!empty($public_id)  && (is_null($publicSession)||$publicSession->account_id != $public_id )){
            if($publicSession){
               $this->_publicAccountSessionAccessor->removeUserSession();
            }
            //写公众号会话信息 
            $this->setAccountSession($public_id);
        }else{
            $this->_publicAccountSessionAccessor->updateSession($publicSession);  
        } 
         $this->publicSession =  $this->_publicAccountSessionAccessor->getAccountSession();
        return true;
    }
    
 
      /**
     * 构造器
     * @param string $id actionID
     * @param \yii\base\Module $module 模块
     * @param array $config 配置信息
     * @throws \Exception 未知异常
     * @throws \yii\base\InvalidConfigException 抛出参数异常
     */
    public function __construct($id, $module,  $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->userSessionAccessor = \Yii::$container->get('app\framework\auth\interfaces\UserSessionAccessorInterface'); 
        $this->userSession = $this->userSessionAccessor->getUserSession(); 
        if(!is_null($this->userSession)){
          //  $this->userLevel = $this->getLevel($this->userSession->ugroup_id);
            $this->_publicAccountSessionAccessor = \Yii::$container->get('app\framework\auth\PublicAccountSessionAccessor');
            $this->_publicAccountRepository = \Yii::$container->get('app\repositories\PublicAccountRepository'); 
            $this->_accountAuthorityService = \Yii::$container->get('app\modules\system\services\AccountAuthorityService'); 
            $this->publicSession =  $this->_publicAccountSessionAccessor->getAccountSession();  
        } 
       // $this->layout=$this->_popupLayout;
    }
    
    private function getLevel($groupid){
        if($groupid==USERGUOUP_ADMIN){
            return 1;
        }else if($groupid==USERGROUP_PUBLIC){
            return 2;
        }else{
            return 3;
        }
    }
     
  

    /**
     * 返回JSON数据
     * @param $status bool 自定义状态
     * @param $message string 自定义消息
     * @param null $data object\array 自定义数据，不设置则不输出该属性
     * @return array JSON Data
     */
    public function jsonData($status, $message, $data = null)
    {
        $jsonObject = ['status' => $status, 'message' => $message];
        if (isset($data)) {
            $jsonObject['data'] = $data;
        }
        return $this->json($jsonObject);
    }
    
    private function setAccountSession($public_id){
        $data = $this->_publicAccountRepository->getAccount($public_id);
        $accountSession = new PublicAccountSession();
        $accountSession->key =  sha1($public_id . time() . mt_rand(1, 9000));
        $accountSession->account_id = $data['id'];
        $accountSession->name = $data['name'];
        $accountSession->originalId = $data['original_id'];
        $accountSession->wechatNumber = $data['wechat_number'];
        $accountSession->appId = $data['app_id'];
        $accountSession->appSecret = $data['app_secret'];
        $accountSession->token = $data['token'];
        $accountSession->package_type = $data['package_type'];
        $this->_publicAccountSessionAccessor->updateSession($accountSession);   
    }
//    public function behaviors()
//    {
//        return [
//            [
//                'class' => 'app\framework\web\filters\AuthorizationFilter',
//            ] ,
//            [
//                'class' => 'app\framework\web\filters\FunctionActionFilter',
//            ]
//        ];
//    }
}

 

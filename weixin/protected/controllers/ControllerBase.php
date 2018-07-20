<?php 
namespace app\controllers;
use app\framework\db\EntityBase;
use app\framework\web\extension\MobileController; 
use app\framework\utils\WebUtility; 

/**
 * @property \app\framework\web\extension\MobileContext $context This property is read-only.
 * @package app\controllers
 */
abstract class ControllerBase extends MobileController
{
    public $enableCsrfValidation = false;    
    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    public function beforeAction($action)
    {  
        if (parent::beforeAction($action)) {  
            $this->addBehaviorLog($action);
            return true;
        } 
        return false;
    }
    
    public function getContext()
    {
        $context = \Yii::$app->context;
        return $context;
    }
        
    private function addBehaviorLog($action)
    {
        try {
            $headers = $this->getRequestHeaders();
            //$context = $this->context;
            /*$logData = [
                    'id' => \app\framework\utils\StringHelper::uuid(),
                    'openid' => $context->openid,
                    'fan_id' => $context->fanId,
                    'member_id' => $context->memberId ,
                    'account_id' => $context->publicId, 
                    'current_url' => "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",
                     'refer_url' =>isset($headers['Referer'])?$referer=$headers['Referer']:"",
                    'sharer_openid_id' => $this->getRequestParamter('shareropenid'),
                    'sharer_fan_id' => $this->getRequestParamter('sharerfanid'),
                    'sharer_member_id' => $this->getRequestParamter('sharermemberid'),
                    'controller' => $action->controller->id,
                    'action' => $action->id, 
                    'is_ajax' => (isset($headers['X-Requested-With'])&&$headers['X-Requested-With']=="XMLHttpRequest")?true:false,
                    'client_ip' => $_SERVER['REMOTE_ADDR']
            ];
          
            $dbConnection =EntityBase::getDb();
            $dbConnection->createCommand()->insert('p_behavior', $logData)->execute();*/
        } catch (\Exception $ex) {
            // 采用跟踪日志避免错误日志膨胀
            \Yii::trace($ex);
        }
    }
    
    private function getRequestHeaders() {
        $headers = array();
        foreach($_SERVER as $key => $value) {
            if(strpos($key, 'HTTP_') === 0) {
                $headers[str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))))] = $value;
            }
        }
        return $headers;
    }
    
    private function getRequestParamter($paramName)
    {
        if (array_key_exists($paramName, $_REQUEST)) {
            return $_REQUEST[$paramName];
        }
        
        return null;
    } 
    /**
     * 检验是否绑定
     * @return boolean
     */
    public function checkBind(){
        $memberid = $this->context->memberId;
        if(empty($memberid)){
            $url= WebUtility::createBeautifiedUrl("me/me/login-index");
            $this->redirect($url);
        }
        return true;
    }

    /**
     * 获取导航信息
     * @param type $appcode
     * @return type
     * @throws \InvalidArgumentException
     */
    public function  getNavigation($appcode){
        if(empty($appcode)){
            throw new \InvalidArgumentException('$appcode对象不存在');
        }
        $brannerService =  \Yii::$container->get('app\modules\pub\services\BrannerService');
        return $brannerService->getNavigation($this->context->publicId,$appcode );
    }
}

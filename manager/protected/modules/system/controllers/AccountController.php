<?php 
namespace app\modules\system\controllers; 
use app\modules\ControllerBase;
use app\modules\system\services\AccountService;  
use yii;
use yii\base\ErrorException;
use yii\base\InvalidParamException;
use yii\web\Response;
use \yii\db\ActiveRecord; 
use app\framework\utils\Security;
use app\framework\utils\StringHelper;
use app\modules\system\models\AccountForm;
use app\modules\system\services\CertService;
use app\entities\TUserAccount;
use app\framework\utils\DateTimeHelper;  

class AccountController extends ControllerBase{
    private $_accountService;/**
     * @var string 弹出页面使用的layout
     */
    private $_popupLayout = '../../../../views/layouts/popup.php';
    private $_certService; 
    
    public function __construct($id, $module, AccountService $accountService,CertService $certService,  $config = [])
    {
        $this->_accountService = $accountService;
        $this->_certService=$certService; 
        parent::__construct($id, $module, $config);
    } 
    
     /**
     * 公众号指引
     * @param $id
     * @return string
     */
    public function actionGuide()
    {
        $data = $this->_accountService->getAccount();
        $data["wechat_api_url"] = "http://".$_SERVER['HTTP_HOST']."/api?public_id=".$data['id'];
        $data["wechat_domain"] = $_SERVER['HTTP_HOST'];
        $data["wechat_js_domain"] = $this->_accountService->getWeiXinSite(); 
        return $this->render('guide', ['data' => $data]);
    } 
    /**
     * 公众号配置
     * @param $id
     * @return string
     */
    public function actionConfig()
    {

        $data = $this->_accountService->getAccount(); 
        $data["wechat_api_url"] = "http://".$_SERVER['HTTP_HOST']."/api?public_id=".$data['id'];
        $data["wechat_domain"] = $_SERVER['HTTP_HOST'];//$_SERVER['HTTP_HOST'];
        $data["wechat_js_domain"] =$this->_accountService->getWeiXinSite(); 
        return $this->render('config', ['data' => $data]);
    }
    
    
    
     /**
     * 修改公众号基本信息
     * @param string $id
     * @param string $column
     * @param string $value
     * @return mixed
     */
    public function actionUpdateAccount($id, $column, $value)
    {
        try { 
            /*if ($column == "original_id") {
                $oldAccount = $this->_accountService->getAccountByOriginalId($value);
                if ($oldAccount->id<>$id) {
                    return $this->json(['result' => false, 'code' => 500, 'msg' => "该公众号原始ID已经存在"]);
                }

                $account = $this->_accountService->getAccountById($id); 
            }*/
            $result = $this->_accountService->updateAccountInfo($id='39d87f3e-141f-9e5c-7ab2-6848a8953b5e', $column, $value);
            if ($result) { 
                return $this->json(['result' => true, 'data' => $result]);
            } else {
                return $this->json(['result' => false, 'code' => 500, 'msg' => "修改公众号信息失败"]);
            }
        } catch (\Exception $ex) {
            return $this->json(['result' => false, 'code' => 500, 'msg' => $ex->getMessage()]);
        }
    }
  
    /**
     * 收集表单数据
     * @param type $isValid
     * @return \app\modules\system\controllers\AccountForm
     * @throws \yii\base\InvalidValueException
     */
    private function getAccountFormByPost($isValid = true) {
        $accountForm = new AccountForm();
        // 取post过来的产品数据, 键值对
        $accountForm->setAttributes($this->request->post(), false);

        if ($isValid && !$accountForm->validate()) {
            throw new \yii\base\InvalidValueException('AccountForm必填项校验未通过');
        } 
        return $accountForm;
    } 

    /**
     * 导入支付证书
     * @return type
     */
    public function actionImport()
    {
        $type = $this->request->get('type');
        if (empty($type)) {
            return $this->json(['result'=>false, 'msg'=>'导入类型为空']);
        }
        $accountId = $this->request->get('accountId');
        if (empty($accountId)) {
            return $this->json(['result'=>false, 'msg'=>'公众号为空']);
        }

        try {
            $fileName = $_FILES["file"]['tmp_name'];
            $content = file_get_contents($fileName);
        } catch (\Exception $exc) {
            return $this->json(['result'=>false, 'msg'=>$exc->getMessage()]);
        }

        if (empty($content)) {
            return $this->json(['result'=>false, 'msg'=>'文件内容为空']);
        }

        try {
            $this->_certService->insertContent($type, $content, $accountId); 
        } catch (\Exception $exc) {
            return $this->json(['result'=>false, 'msg'=>$exc->getMessage()]);
        }

        return $this->json(['result'=>true, 'msg'=>'导入成功']);
    } 
    
}

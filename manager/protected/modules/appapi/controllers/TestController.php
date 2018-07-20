<?php 
namespace app\modules\appapi\controllers;
use app\modules\appapi\services\TestService;
use app\modules\appapi\utils\WebUtils;
use app\framework\sms\SmsService;
class TestController extends AppControllerBase{  
    private  $_testService;
    private $_smsService;
    public function __construct($id, $module,TestService $testService, $config = [])
    {
        $this->_testService = $testService;
        $this->_smsService = new SmsService();//$smsService;
        parent::__construct($id, $module, $config);
    }
   
    public function actionTest(){
         if (!WebUtils::IsRequestParam('user')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提用户ID[user]']);
        }
        $data = $this->_testService->getAllUser();
        return  $this->json(['result'=>true,'code' => 200, 'data' => $data]);
    }

    /*
     * 获取发送短信验证码
     */
    public  function actionIndex($phone='18826808110')
    {
//        return  [
//                'result' => true,
//                'verifycode'=>'1234',
//                'code' => "200",
//                'msg' => "发送成功"
//            ];
        return $this->_smsService->sendVerifyCode($phone);
    }
}

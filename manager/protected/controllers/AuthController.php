<?php

namespace app\controllers; 

use Yii;

use app\framework\utils\StringHelper;
use app\framework\auth\UserSession; 
use app\framework\cache\OnlineAppCache;
use app\framework\auth\NonceService;
use app\framework\utils\WebUtility;
use app\models\LoginForm;
 use app\framework\auth\Authorization; 
use app\framework\utils\FormHelper;
use app\framework\web\extension\Controller;
use app\services\LoginLockerService;
use app\services\LoginService;
use app\services\AccountService;
use app\framework\auth\UserSessionAccessor;
 
use yii\web\Response;  

class AuthController  extends Controller
{ 
    private $_accountService;
    private $_authorization;
    private $_loginService;
    private $_userSessionAccessor;
    public function __construct($id, $module, AccountService $accountService, Authorization $authorization,LoginService $loginService,UserSessionAccessor $userSessionAccessor, $config = [])
    {
        $this->_accountService = $accountService;
        $this->_authorization = $authorization;
        $this->_loginService = $loginService;
        $this->_userSessionAccessor=$userSessionAccessor; 
        parent::__construct($id, $module, $config);
    }

    //释放密码错误锁
    public function actionReleaseLock()
    { 
        $userName = \Yii::$app->request->post('username', ''); 
        if (empty($userName)) {
            return $this->json(['errcode' => 40013, 'errmsg' => '用户名不能为空']);
        }
        $oauth2Filter = new  \app\framework\web\filters\OAuth2VerifyApiActionFilter();
        $result = $oauth2Filter->beforeAction($this->action);
        if ($result) {
            $lockerService = new LoginLockerService( $userName);
            $lockerService->releaseLocker();
            return $this->json(['errcocde' => 0, 'errmsg' => '']);
        } else {
            Yii::$app->response->format = Response::FORMAT_JSON;
            \Yii::$app->end();
        }
    }

    public function actionLogin($returnUrl = '')
    {   
        $model = new LoginForm(); 
         if (Yii::$app->request->isGet) { 
             $session = $this->_userSessionAccessor->getUserSession();
             if(isset($session)){
                  $sessionId = $session->key;
                   if (empty($returnUrl)) {
                       $defaultUrl =$this->getDefaultUrl();
                        if ($defaultUrl == false) { 
                            return $this->renderPartial('login', ['model' => $model, 'error' => '登录失败, 用户没有任何权限']);
                        }
                        $this->redirect($defaultUrl);
                   }
                   else {
                        $nc = NonceService::createNonceAndStore($session); 
                        $returnUrl = WebUtility::buildQueryUrl($returnUrl, 'sid=' . $sessionId . ' &ticket=' . urlencode($nc->id) );
                        $this->redirect($returnUrl);
                   }
             }else{
                  $showCaptchaCode = LoginLockerService::needCaptchaCode();
                  return $this->renderPartial('login', ['model' => $model, 'captcha_code_show' => $showCaptchaCode]);
             }
         } 

        $model->setAttributes($this->request->post(), false); 
        if (!$model->validate()) { 
            $showCaptchaCode = LoginLockerService::needCaptchaCode();
            return $this->renderPartial('login', ['model' => $model, 'error' => $model->errors,  'captcha_code_show' => $showCaptchaCode]);
        }

        $lockerService = new LoginLockerService( $model->userName);
        /************** 密码连续错误加锁 **********/
        $lockerTimes = $lockerService->checkLoginLocker();
        $result = $this->_authorization->login($model->userName, $model->password, 0);
        $status = $result['status'];
        $errorMsg = $result['msg']; 
       if ($status == LOGIN_STATUS_SUCCESS) {
            /** @var UserSession $session */
            $lockerService->releaseLocker();
            $session = $result['session'];
            $sessionId = $session->key;  
            if (!empty($returnUrl)) {
                $nc = NonceService::createNonceAndStore($session);
                \Yii::trace('创建并传送sid: ' . $sessionId); 
                 
                 $urlTo = WebUtility::buildQueryUrl($returnUrl, 'sid=' . $sessionId . '&ticket=' . $nc->id);
                $this->redirect($urlTo);
            } else { 
                $defaultUrl =$this->getDefaultUrl();
                if ($defaultUrl == false) { 
                    return $this->renderPartial('login', ['model' => $model, 'error' => '登录失败, 用户没有任何权限']);
                }
                $this->redirect($defaultUrl);
            }

        } else {
            $lockerService->lock($status);
            list(, $showCaptchaCode) = $lockerService->valid($model->captchaCode); 
            if ($lockerTimes > 0 && in_array($status, [LOGIN_STATUS_INVALID_USER, LOGIN_STATUS_INVALID_PASSWORD, LOGIN_STATUS_INVALID_TENANT])) {
                $lockMsg = $lockerService->lockerTimeSec > 60 ? ($lockerService->lockerTimeSec / 60) . '分钟!' :  $lockerService->lockerTimeSec . '秒!';
                $errorMsg = '帐号或密码不正确,已失败' . $lockerTimes .'次,失败'. $lockerService->maxErrorToLock.'次帐号将被锁定' . $lockMsg;
            }
            return $this->renderPartial('login', ['model' => $model, 'error' => $errorMsg,  'captcha_code_show' => $showCaptchaCode]);
        }
    }
    /**
     * 获取当前用户默认Url
     * @param type $groupid
     * @return string
     */
    private function getDefaultUrl(){ 
        $url= '/system/user/index'; 
        return $url;
    }
    /**
     * 跳转至微信获取code
     * @param $corpId
     */
    protected function redirectToWxGetCode($corpId)
    {
        $currentUrl = Yii::$app->request->absoluteUrl;
        $currentUrl = WebUtility::unsetParam('code', $currentUrl);
        $currentUrl = WebUtility::unsetParam('state', $currentUrl);
        $state = StringHelper::random();
        \Yii::$app->session->set('qyh:login:token', $state);

        $wxUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='
            . $corpId . '&redirect_uri='
            . urlencode($currentUrl) . '&response_type=code&scope=snsapi_base&state='
            . $state
            . '#wechat_redirect';
        $this->redirect($wxUrl);
    }

    /**
     * @param $ticket
     * @param string $logoutUrl
     * @param string $sid
     * @return json
     */
    public function actionValidateservice($ticket, $logoutUrl = '', $sid = '')
    {
        if (empty($ticket)) {
            return $this->json(['status' => 0, 'msg' => 'ticket为空']);
        }
        if (NonceService::validate($ticket)) {
            \Yii::trace('set logoutstatus:' . $sid);
            $cache = NonceService::get($ticket);
            $sessionId = sha1($cache->account . time() . mt_rand(1, 1000)); 
            NonceService::remove($ticket);
            \Yii::trace('sendsessionid:' . $sessionId);

            return $this->json(['status' => 1, 'data' => $cache, 'sessionId' => $sessionId]);

        } else {
            return $this->json(['status' => 0, 'msg' => 'ticke not found']);
        }
    }

    public function actionGetSession()
    {
        $token = $this->tokenAccessor->getToken();
        if (empty($token)) {
            return $this->json(['status' => 0]);//没有登录
        }

        $session = $this->userSessionAccessor->getUserSession($token);
        if (!isset($session)) {
            $this->tokenAccessor->removeToken();
            return $this->json(['status' => 0]);//没有登录
        } 
        return $this->json(['status' => 1, 'data' => $session]); 
    }

    public function actionLogout($returnUrl = '')
    {
        if (!\Yii::$app->session->isActive) {
            \Yii::$app->session->open();
        }
        $sid = \Yii::$app->session->id;
        if (!empty($sid)) {
            $data = OnlineAppCache::get($sid);
            \Yii::trace('read1: ' . json_encode($data));
            //退出应用APP
            if (isset($data)) {
                foreach ($data as $item) {
                    $logoutUrl = $item['logoutUrl'];
                    $sessionId = $item['sessionId'];
                    if (!empty($logoutUrl)) {
                        $logoutUrl = WebUtility::buildQueryUrl($logoutUrl, 'sid=' . $sessionId);
                        \Yii::trace('remotelogout ' . $logoutUrl);
                        $rq = new \app\framework\webService\RestClientHelper();
                        try {
                            $rq->invoke($logoutUrl, null, 'GET', true);
                        } catch (\Exception $ex) {
                            \Yii::error($ex);
                        }
                    }
                }

                OnlineAppCache::removeLogoutData($sid);
            }
        }
        session_destroy();
        if (empty($returnUrl)) {
            $returnUrl = '/auth/login';
        }
        $this->_loginService->rememberPreAccessUrl();
        $this->redirect($returnUrl);
    }
 
  
  
    public function actionLogout1()
    { 
        $logoutUrl =  'auth/logout';
        Yii::$app->response->redirect($logoutUrl);
    } 
    
    public function actionRemoveSession($sid)
    {
        return $this->logOutApp($sid);
    }

    public function logOutApp($token)
    {
        if (empty($token)) {
            return $this->json(['result' => false, 'msg' => 'sessionId is empty']);
        }
        try
        {
            $sessionAccessor = new UserSessionAccessor();
            session_id($sessionAccessor->sessionId($token));
            session_start();
            session_destroy();
        }
        catch(\Exception $ex) {
            \Yii::warning($ex->getMessage());
        }
        return $this->json(['result' => true, 'msg' => 'success']);
    }
}

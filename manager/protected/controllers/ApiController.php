<?php 
namespace app\controllers; 
use Yii;
use yii\web\BadRequestHttpException;
use app\framework\web\extension\Controller;
use app\framework\auth\NonceService;
use app\framework\cache\OnlineAppCache; 
use app\framework\auth\UserSession;
use app\models\LoginForm;
use app\models\LoginFormOther;

class ApiController extends Controller
{ 
    /**
     * @var UserSessionAccessorInterface
     */
    protected $_userSessionAccessor;
    private $_loginService;
    /**
     * @var TokenAccessorInterface
     */
    protected $tokenAccessor;
    public function __construct($id, $module, 
                                $config = [])
    { 
        $this->tokenAccessor=\Yii::$container->get('app\framework\auth\SSOCookieTokenAccessor');
        $this->_userSessionAccessor=\Yii::$container->get('app\framework\auth\UserSessionAccessor');
        $this->_loginService=\Yii::$container->get('app\framework\auth\Authorization');  
        parent::__construct($id, $module, $config);
    }

  
    public function actionLogin() 
    {
        if ($this->is_get) {
            throw new BadRequestHttpException('禁止get请求');
        }

        $model = new LoginForm(); 
        $model->setAttributes($this->request->post(), false);
        $jsonResult = [];

        if (!$model->validate()) {
            $jsonResult['code'] = LOGIN_STATUS_INVALID_INPUT;
            $jsonResult['message'] = $model->errors;
            return $this->json($jsonResult);
        }

        $result = $this->_loginService->login($model->userName, $model->password, $model->from);
        $status = $result['status'];
        $session = $result['session']; 
        $msg = $result['msg']; 
        $jsonResult = ['code' => $status, 'message' => $msg];

        if ($status == LOGIN_STATUS_SUCCESS) {
            $nc = NonceService::createNonceAndStore($session); 
            $jsonResult['ticket'] = $nc->id; 
        }
        return $this->json($jsonResult);

    }
    
    public function actionSigned($access_token='')
    {
        return $this->json(['signed'=> $this->_loginService->isAuthorized()]);
    }

    public function actionSession($ticket)
    {
        $jsonResult = [];

        if (empty($ticket)) {
            $jsonResult['code'] = 0;
            $jsonResult['message'] = 'ticket不能为空';
            return $this->json($jsonResult);
        } 
        $nc = NonceService::get($ticket);
        if (!is_null($nc)){
            //登录成功 
            $sessionId = sha1( $nc->code . time() . mt_rand(1, 1000));
               //登录成功  
            $session = new UserSession(); 
            $session->account = $nc->account;
            $session->db_dsn = $nc->db_dsn;
            $session->displayName = $nc->displayName;
            $session->org_id = $nc->org_id;
            $session->ugroup_id = $nc->ugroup_id;
            $session->user_id = $nc->user_id;
            $session->mobile = $nc->mobile;
            $session->db_master = $session->db_dsn;
            $session->key = $sessionId;

            $this->_userSessionAccessor->updateSession($session);

            $jsonResult['code'] = 1;
            $jsonResult['token'] = $sessionId;
            $jsonResult['message'] = '获取数据成功';
            $jsonResult['data'] = ['mobile'=>$session->mobile,'username'=>$session->displayName ,'account'=>$session->user_id];
            return $this->json($jsonResult);

        } else {
            $jsonResult['code'] = 0;
            $jsonResult['message'] = 'ticket无效';
            return $this->json($jsonResult);
        }
    } 


    public function actionChangePwd()
    {
        if($this->is_get){
            throw new BadRequestHttpException('无效请求');
        }

        $uid = $_POST['uid'];
        $pwd = $_POST['pwd'];
        $npwd = $_POST['npwd']; 

        if (empty($uid) || empty($pwd) || empty($npwd)) {
            return $this->json(['status' => 0, 'msg' => '参数不能为空!']);
        }

        $match = $this->_memberService->validateUser($uid, $pwd);
        if ($match == false) {
            return $this->json(['status' => 0, 'msg' => '密码不正确!']);
        } else {
            $result = $this->_memberService->updatePassword($uid, $npwd);
            if ($result > 0) {
                return $this->json(['status' => 1, 'msg' => '修改成功!']);
            } else {
                return $this->json(['status' => 0, 'msg' => '更新数据记录失败!']);
            }
        }
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
        if (AppLoginService::validate($ticket)) {
            \Yii::trace('set logoutstatus:' . $sid);
            $cache = AppLoginService::get($ticket);
            $sessionId = sha1($cache->code . time() . mt_rand(1, 1000));
            //为登录APP创建session_id
            if (!empty($sid)) {
                $sid = trim($sid);
                $onlineSign = ['sessionId' => $sessionId, 'logoutUrl' => $logoutUrl];
                \Yii::trace('保存sid: ' . $sid . ', remote_sessionid:' . $sessionId);

                OnlineAppCache::cacheLogoutData($sid, $onlineSign);
            }
            AppLoginService::remove($ticket);
            \Yii::trace('sendsessionid:' . $sessionId); 
            return $this->json(['status' => 1, 'data' => $cache, 'sessionId' => $sessionId]);

        } else {
            return $this->json(['status' => 0, 'msg' => 'ticke not found']);
        }
    }

}

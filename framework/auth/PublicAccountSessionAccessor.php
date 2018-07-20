<?php 
namespace app\framework\auth;  

class PublicAccountSessionAccessor  
{
    const SESSION_KEY = 'public_account_session_key';
    public function __construct()
    { 
    }
    
   public function getPublicAccountToken(){
       $token = isset($_GET[Configs::PUBLID_ACCOUNT]) ? $_GET[Configs::PUBLID_ACCOUNT] : '';
        //api在同一个接口设置token，并获取token需要以下逻辑
        return $token;
   }
    /**
     * @param string $accountSessionId
     * @return UserSession
     */
    public function getAccountSession()
    {
//        if ($accountSessionId == '') {
//            $token = $this->getPublicAccountToken();
//            if (!empty($token)) {
//                $accountSessionId = $this->sessionId($token);
//            }
//        }
//        if (!empty($accountSessionId)) {
//            session_id($accountSessionId);
//        }

        /** @var UserSession $userSession */
        $accountSession = \Yii::$app->session->get(self::SESSION_KEY, false);
        if ($accountSession == false) {
            return null;
        } 
//        $accountSession->key = \Yii::$app->session->id;
        return $accountSession;
    }

    public function removeUserSession()
    {
//        if ($sessionId == '') {
//            $token = $this->getPublicAccountToken();
//            if (!empty($token)) {
//                $sessionId = $this->sessionId($token);
//            }
//        }
//
//        if (!empty($sessionId)) {
//            session_id($sessionId);
//        }
        \Yii::$app->session->remove(self::SESSION_KEY);
    }

    /**
     * @param UserSession $userSession
     * @throws \InvalidArgumentException
     */
    public function updateSession(PublicAccountSession $accountSession)
    {
//        if (empty($accountSession->key)) {
//            throw new \InvalidArgumentException('session缺少id');
//        }

        if (!isset($accountSession)) {
            throw new \InvalidArgumentException('$userSession 不能为空!');
        } 
        
//        if(\Yii::$app->session->isActive){
//            session_destroy();
//        }
//        $sessionId = $this->sessionId($accountSession->key);
//        session_id($sessionId);
        \Yii::trace('public_account ssid:');
        \Yii::$app->session->set(self::SESSION_KEY, $accountSession);
        session_write_close();
    }

    /**
     * @param string $token
     * @return string
     */
    public function sessionId($token)
    {
        if (empty($token)) {
            throw new \InvalidArgumentException('$token');
        } 
        return sha1($token . \Yii::$app->id);
    }
}
 
<?php

namespace app\framework\auth;

use app\framework\auth\interfaces\UserSessionAccessorInterface;
use app\framework\auth\interfaces\TokenAccessorInterface;

class SSOUserSessionAccessor implements UserSessionAccessorInterface
{
    /**
     * @var TokenAccessorInterface
     */
    private $tokenAccessor;

    public function __construct()
    {
        $this->tokenAccessor = \Yii::$container->get('app\framework\auth\interfaces\TokenAccessorInterface');

        if(!isset($this->tokenAccessor)){
            throw new \Exception('没有注入 app\framework\auth\interfaces\TokenAccessorInterface 实例');
        }
    }

    /**
     * @return UserSession
     */
    public function getUserSession($sessionId='')
    {
        if($sessionId == ''){
            $sessionId = $this->tokenAccessor->getToken();
        }
        if(!empty($sessionId)){
            session_id($sessionId);
        }
        \Yii::trace('getsessionid:' . $sessionId);
        $userSession = \Yii::$app->session->get(self::SESSION_KEY, false);
        if($userSession == false){
            return null;
        }

        $userSession->key = \Yii::$app->session->id;
        return $userSession;
    }

    public function removeUserSession($sessionId='')
    {
        if($sessionId == ''){
            $sessionId = $this->tokenAccessor->getToken();
        }
        if(!empty($sessionId)){
            session_id($sessionId);
        }
        \Yii::$app->session->remove(self::SESSION_KEY);
    }

    /**
     * @param UserSession $userSession
     * @throws \InvalidArgumentException
     */
    public function updateSession(UserSession $userSession)
    {
        if (!isset($userSession)) {
            throw new \InvalidArgumentException('$userSession 不能为空!');
        }

         
        session_id($userSession->key);
        \Yii::$app->session->set(self::SESSION_KEY, $userSession);
    }

    /**
     * @param string $token
     * @return string
     */
    public function sessionId($token)
    {
        return $token;
    }
}

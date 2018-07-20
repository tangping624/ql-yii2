<?php

namespace app\framework\auth;

use app\framework\auth\interfaces\UserSessionAccessorInterface;
use app\framework\auth\interfaces\TokenAccessorInterface;

class UserSessionAccessor implements UserSessionAccessorInterface
{
    /**
     * @var TokenAccessorInterface
     */
    private $tokenAccessor;

    public function __construct()
    {
        $this->tokenAccessor = \Yii::$container->get('app\framework\auth\interfaces\TokenAccessorInterface');

        if (!isset($this->tokenAccessor)) {
            throw new \Exception('没有注入 app\framework\auth\interfaces\TokenAccessorInterface 实例');
        }
    }

    /**
     * @param string $sessionId
     * @return UserSession
     */
    public function getUserSession($sessionId = '')
    {
        if ($sessionId == '') {
            $token = $this->tokenAccessor->getToken();
            if (!empty($token)) {
                $sessionId = $this->sessionId($token);
            }
        }
        if (!empty($sessionId)) {
            session_id($sessionId);
        }

        /** @var UserSession $userSession */
        $userSession = \Yii::$app->session->get(self::SESSION_KEY, false);
        if ($userSession == false) {
            return null;
        }

        $userSession->key = \Yii::$app->session->id;
        return $userSession;
    }

    public function removeUserSession($sessionId = '')
    {
        if ($sessionId == '') {
            $token = $this->tokenAccessor->getToken();
            if (!empty($token)) {
                $sessionId = $this->sessionId($token);
            }
        }

        if (!empty($sessionId)) {
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
        if (empty($userSession->key)) {
            throw new \InvalidArgumentException('session缺少id');
        }

        if (!isset($userSession)) {
            throw new \InvalidArgumentException('$userSession 不能为空!');
        } 
        if(\Yii::$app->session->isActive){
            session_destroy();
        }
        $sessionId = $this->sessionId($userSession->key);
        session_id($sessionId);
        \Yii::trace('login ssid:' . $sessionId);
        \Yii::$app->session->set(self::SESSION_KEY, $userSession);
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

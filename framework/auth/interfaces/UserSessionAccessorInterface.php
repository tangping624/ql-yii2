<?php

namespace app\framework\auth\interfaces;
use app\framework\auth\UserSession;

/**
 * 处理用户登录状态session
 */
interface UserSessionAccessorInterface
{
    const SESSION_KEY = 'user_sign_token_session_key';

    /**
     * 获取登录session
     * @return UserSession
     */
    public function getUserSession($sessionId='');


    /**
     * 移除当前登录session
     * @return void
     */
    public function removeUserSession($sessionId='');

    /**
     * @param UserSession $userSession
     * @throws \InvalidArgumentException
     */
    public function updateSession(UserSession $userSession);

    /**
     * @param string $token
     * @return string
     */
    public function sessionId($token);
}
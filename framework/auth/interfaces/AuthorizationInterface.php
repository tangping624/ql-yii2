<?php

namespace app\framework\auth\interfaces;

/**
 * 验证登录
 */
define('LOGIN_STATUS_SUCCESS', 0x001);//验证成功
define('LOGIN_STATUS_INVALID_USER' , 0x002);//账号不正确
define('LOGIN_STATUS_INVALID_PASSWORD' , 0x003);//密码不匹配
define('LOGIN_STATUS_INVALID_TENANT', 0x004);//无效的租户
define('LOGIN_STATUS_INVALID_INPUT', 0x005);//无效的输入
define('LOGIN_STATUS_DISABLE_TENANT', 0x006);//租户被禁用
define('LOGIN_STATUS_DISABLE_USER', 0x007);//用户被禁用
define('LOGIN_STATUS_INVALID_EXPIRED' , 0x008);//用户过期
define('LOGIN_STATUS_NO_USER' , 0x009);//用户过期


interface AuthorizationInterface
{
    /**
     * @param string $account 帐号
     * @param string $password 密码 
     * @param int $from 登录点, 如 客服系统pc端、客服手机端
     * @param bool $rememberMe
     * @return array ['status'=> LOGIN_STATUS_SUCCESS, 'session'=> $session];
     */
    public function login($account, $password,  $from = 0);  
    /**
     * 退出
     * @return void
     */
    public function logout();

    /**
     * 是否登录
     * @return bool
     */
    public function isAuthorized();
}
<?php
namespace app\framework\auth;

use app\framework\biz\cache\OrganizationCacheManager;
use app\framework\utils\Security;
use app\framework\utils\WebUtility;


//该类不再使用了，目前存在只为了兼容以前代码
//请直接使用 app\framework\auth\interfaces\AuthorizationInterface的实例
class AuthService
{

    public static function login($account, $password, $tenantCode, $from = 0, $rememberMe=false)
    {
        $auth = \Yii::$container->get('app\framework\auth\interfaces\AuthorizationInterface');
        if(!isset($auth)){
            throw new \Exception('请注入app\framework\auth\interfaces\AuthorizationInterface实例');
        }

        return $auth->login($account, $password, $tenantCode, $from, $rememberMe);
    }

    public static function logout()
    {
        $auth = \Yii::$container->get('app\framework\auth\interfaces\AuthorizationInterface');
        if(!isset($auth)){
            throw new \Exception('请注入app\framework\auth\interfaces\AuthorizationInterface实例');
        }

        $auth->logout();
    }

    public static function isAuthorized()
    {
        $auth = \Yii::$container->get('app\framework\auth\interfaces\AuthorizationInterface');
        if (!isset($auth)) {
            throw new \Exception('请注入app\framework\auth\interfaces\AuthorizationInterface实例');
        }

        return $auth->isAuthorized();
    }
}

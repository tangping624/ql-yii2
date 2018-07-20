<?php

namespace app\framework\biz;
 
use app\framework\biz\cache\UserGroupRightsCacheManager; 
use yii\web\ForbiddenHttpException;

/**
 * 业务公共类
 */
class Common
{
    /**
     * 获得登录信息
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public static function getLoginInfo()
    {
        $user = \Yii::$container->get('app\framework\auth\interfaces\UserSessionAccessorInterface')->getUserSession();
        $userName = '';  
        if ($user) {
            $userName = $user->displayName; 
        }
        $url = "/system/user/password";

        return ["userName" => '<a class="user-name" data-user="' . ($user->account?:'') . '" href="' . $url . '">' . $userName . '</a>'];
    } 
}

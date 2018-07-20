<?php 
namespace app\framework\biz\cache; 

class UserSessionManager {
    /**
     * 获得当前用户的session
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public static function getUserSession()
    {
        $sessionAccessor = \Yii::$container->get('app\framework\auth\interfaces\UserSessionAccessorInterface');
        $userSession = $sessionAccessor->getUserSession();
        return $userSession;
    }
 
}
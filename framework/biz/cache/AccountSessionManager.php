<?php 
namespace app\framework\biz\cache;
use app\framework\biz\cache\UserSessionManager;
use app\framework\auth\PublicAccountSessionAccessor;

class AccountSessionManager extends UserSessionManager{
     /**
     * 获得当前公众号session
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public static function getAccountSession()
    {
        $sessionAccessor = new PublicAccountSessionAccessor();
        $accountSession = $sessionAccessor->getAccountSession();
        return $accountSession;
    }
    /**
     * 校验租户应用
     * @param $appCode
     * @param bool $throwException
     * @return bool
     * @throws ForbiddenHttpException
     */
    public static function checkAccountApp($appCode, $throwException = false)
    {
        $accountSession = self::getAccountSession();
        if (!(isset($accountSession) && ApplicationCacheManager::AccountHasApp($accountSession->account_id, $appCode))) {
            if ($throwException) {
                throw new ForbiddenHttpException('应用未授权，禁止访问');
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
}

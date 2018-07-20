<?php

namespace app\services;

use app\repositories\AccountRepository;
use yii\db\Query;
use app\framework\db\EntityBase;
class LoginService
{
    const REFERER_URL_COOKIE_NAME = 'referer_url';

    private $_accountRepo;
 

    public function __construct(AccountRepository $accountRepo)
    {
        $this->_accountRepo = $accountRepo; 
    }

    /**
     * 记住上一次访问地址
     */
    public function rememberPreAccessUrl()
    {
        $preUrl = $_SERVER['HTTP_REFERER'];
        $cookieUrl = new \yii\web\Cookie();
        $cookieUrl->name = static::REFERER_URL_COOKIE_NAME;
        $cookieUrl->value = $preUrl;

        \Yii::$app->response->cookies->add($cookieUrl);
    }

    /**
     * 获取登录后默认站
     * @param $userId
     * @param $tenantCode
     * @return string
     */
    public function getSiteUrlAfterLogin($userId)
    {
        $db = EntityBase::getDb();
        $appCodeList = $this->_accountRepo->getAppCodeListOfUser($userId, $db);
        if (empty($appCodeList)) {
            return false;
        }

        $appCodeList = array_values($appCodeList);

        if (count($appCodeList) > 1) {
            foreach ($appCodeList as $item) {
                if (strtolower($item) != 'managementcenter') {
                    $appCode = $item;
                    break;
                }
            }
        } else {
            $appCode = $appCodeList[0];
        } 
        if (empty($appCode)) {
            return false;
        } 
        return $this->_accountRepo->getSiteUrlByAppCode($appCode);
    }


    /**
     * @return string
     */
    protected function getPreAccessUrl()
    {
        $cookie = \Yii::$app->request->cookies->get(static::REFERER_URL_COOKIE_NAME);
        return $cookie == null ? '' : $cookie->value;
    }

    /**
     * 加入微信登录用户绑定
     * @param $openid
     * @param $account
     * @param $tenantCode
     * @return int
     */
    public function insertWXUser($openid, $account)
    {
        return $this->_accountRepo->insertWXUser($openid, $account);
    }

    public function getWechatUserByOpenid($openid)
    {
        if (empty($openid)) {
            return false;
        }
        return $this->_accountRepo->getWechatUserByOpenid($openid);
    
    }
}

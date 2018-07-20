<?php

namespace app\framework\web\filters;

use yii\base\ActionFilter;
use yii\db\Connection;
use app\framework\utils\StringHelper;
use app\framework\utils\WebUtility;
use app\framework\biz\cache\FanCacheManager; 
use yii\web\ForbiddenHttpException;
use app\framework\db\EntityBase;
use Yii;

// 自动添加粉丝, 获取openid
class AutoAddFanFilter extends ActionFilter
{
    public function beforeAction($action)
    {
        if (\Yii::$app->request->isGet && !\Yii::$app->request->isAjax) { 
            $tReader = \Yii::$container->get('app\framework\biz\tenant\TenantReaderInterface');  
            $publicId = $tReader->getPublicId();
            $openId = '';

            //校验通过则更新openid
            if (isset($_GET['openid']) && isset($_GET['_state'])) {
                $cachedState = \Yii::$app->session->get('account:openid:token:', false);
                if ($_GET['_state'] == $cachedState) {
                    //set openid
                    $openId = $tReader->getOpenId($_GET['openid']);
                    $currentUrl = \Yii::$app->request->absoluteUrl;
                    $currentUrlWithoutOpenid = $this->removeOpenidUrlToken($currentUrl);
                    \Yii::$app->response->redirect($currentUrlWithoutOpenid);
                    \Yii::$app->end();
                }
            } else {
                $openId = $tReader->getOpenId();
            }

            if (empty($openId)) {
                $currentUrl = \Yii::$app->request->absoluteUrl;
                $currentUrlWithoutOpenid = $this->removeOpenidUrlToken($currentUrl); 
                $publicAccountUrl = $this->_getManageSiteUrl();
                if ($publicAccountUrl == false || $publicAccountUrl == '') {
                    throw new \Exception('未找到manage_site在应用的url');
                }
                $publicAccountUrl = $publicAccountUrl.  '/wxauth/openid?&public_id=' . $publicId . '&return_url=' . urlencode($currentUrlWithoutOpenid);
                $state = $this->createOpenidRequestState();
                $publicAccountUrl .= '&_state=' . $state;

                \Yii::$app->response->redirect($publicAccountUrl);
                \Yii::$app->end();
            }

            $fan = FanCacheManager::getFan($openId);
            if (is_null($fan)) {
                $this->_addFans($publicId, $openId);
            }

        }

        return parent::beforeAction($action);
    }

    private function removeOpenidUrlToken($urlWithToken)
    {
        $urlWithToken = WebUtility::unsetParam('openid', $urlWithToken);
        $urlWithToken = WebUtility::unsetParam('_state', $urlWithToken);
        $urlWithToken = WebUtility::unsetParam('access_token', $urlWithToken);
        return WebUtility::unsetParam('state', $urlWithToken);
    }

    /**
     * 生成state,用来验证返回openid的回调
     * @return string
     */
    private function createOpenidRequestState()
    {
        $token = StringHelper::random();
        \Yii::$app->session->set('account:openid:token:', $token);
        return $token;
    }

   
    /**
     * @return Connection
     */
    protected function getTenantDb()
    { 
        $conn = EntityBase::getDb();
        return $conn;
    }

    /**
     * @return bool|null|string
     */
    private function _getManageSiteUrl()
    { 
        $settingAccessor = \Yii::$container->get('app\framework\settings\interfaces\SettingsAccessorInterface');
        $config = $settingAccessor->get('manage_site');

        if (!isset($config)) {
            throw new \Exception('缺少配置项 manage_site');
        } 
        return $config;
    }

    private function _exitsFan($openId, $publicId)
    {
        $sql = "SELECT 1 FROM p_fan WHERE openid=:openid AND account_id=:account_id AND is_deleted=0";
        $result = $this->getTenantDb()->createCommand($sql, [':openid' => $openId, ':account_id' => $publicId])->queryScalar();
        return $result;
    }

    public function _insertFans($columnsData)
    {
        $now = \app\framework\utils\DateTimeHelper::now();
        $columnsData['id'] = \app\framework\utils\StringHelper::uuid();
        $columnsData['month_pushed'] = 0;
        $columnsData['created_on'] = $now;
        $columnsData['modified_on'] = $now;

        $db = $this->tenantDb;
        $db->createCommand()->insert('p_fan', $columnsData)->execute();
        return $columnsData;
    }

    public function findAccountByPublicId($accountId)
    {
        $sql = "select original_id from p_account where id=:id and is_deleted=0";
        $result = $this->getTenantDb()->createCommand($sql, [':id' => $accountId])->queryOne();
        return $result;
    }

    /**
     * 添加粉丝
     * @param $publicId
     * @param $openId
     * @return mixed
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     */
    private function _addFans($publicId, $openId)
    {
        $accessTokenRepository = \Yii::$container->get('app\framework\weixin\interfaces\IAccessTokenRepository');
        $accountRow = $this->findAccountByPublicId($publicId);

        if ($accountRow == false) {
            throw new \Exception('publicId not exits, ' . $publicId);
        }

        $originalId = $accountRow['original_id']; 

        $accessTokenHelper = new \app\framework\weixin\AccessTokenHelper($originalId, $accessTokenRepository);
        $user = new \app\framework\weixin\proxy\fw\User($accessTokenHelper);
        $wxUserInfo = $user->info($openId);
        if ($wxUserInfo == false) {
            throw new \Exception('读取粉丝信息失败! 网络错误');
        }

        if (isset($wxUserInfo->errcode) && $wxUserInfo->errcode > 0) {
            throw new \Exception('读取粉丝信息失败! openid:' . $openId . ', response:' . json_encode($wxUserInfo, JSON_UNESCAPED_UNICODE));
        }

        $data = [
            'openid' => $wxUserInfo->openid,
            'nick_name' => $wxUserInfo->nickname,
            'sex' => ($wxUserInfo->sex == 1 ? '男' : ($wxUserInfo->sex == 2 ? '女' : '未知')),
            'city' => $wxUserInfo->city,
            'country' => $wxUserInfo->country,
            'province' => $wxUserInfo->province,
            'language' => $wxUserInfo->language,
            'headimg_url' => $wxUserInfo->headimgurl,
            'follow_time' => empty($wxUserInfo->subscribe_time) ? null : date("Y-m-d H:i:s", $wxUserInfo->subscribe_time),
            'union_id' => property_exists($wxUserInfo, 'unionid') ? $wxUserInfo->unionid : '',
            'account_id' => $publicId
        ];

        $data['is_followed'] = empty($wxUserInfo->subscribe_time) ? 0 : 1;

        // 查找公众号下粉丝是否已存在
        $isExists = $this->_exitsFan($data['openid'], $publicId);
        if ($isExists == false) { 
            $this->_insertFans($data);
        }

    }
}

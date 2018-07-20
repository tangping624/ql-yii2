<?php

namespace app\services;

use Yii;
use app\framework\utils\WebUtility;

class WeixinOAuthService
{
     /**
     * @var PublicAccountService
     */
    private $_accountService;

    public function __construct(PublicAccountService $accountService)
    {
        $this->_accountService = $accountService;
    }

    /**
     * 获取OAuth的token 
     * @param string $public_id 公众号id
     * @param string $openid 用户的openid
     * @param string $app_encrypt
     * @return mixed|null
     * @throws \Exception
     */
    public function getOAuthToken( $public_id, $openid, $app_encrypt = '')
    {
        $cacheName = $this->getOAuthCacheKey($public_id, $openid, $app_encrypt);
            
        $oauthInfo = Yii::$app->cache[$cacheName];

        //如果token还没过期，则直接返回，否则刷新token
        if ($oauthInfo["expire_time"] >= time()) {
            return $oauthInfo;
        } //token过期,并且没有刷新过，刷新token
        elseif ($oauthInfo) {
            $account = $this->_accountService->getAccount( $public_id, $app_encrypt);
            if ($account == false) {
                throw new \Exception("不存在该公众号, " .  ' public_id: ' . $public_id);
            }

            $appId = $account['app_id'];
            $isAuth = $account['is_authed'] == 1;
            if (empty($appId)) {
                throw new \Exception('app_id 不能为空!');
            }

            $argCode = $_GET['code'];
            $refresgToken = $oauthInfo["refresh_token"];
            $wxApi = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid={$appId}&grant_type=refresh_token&refresh_token={$refresgToken}";
            if ($isAuth) {
                $componentAppId = \app\framework\weixin\proxy\component\WxComponent::getComponentAppId();
                $accessTokenHelper = new \app\framework\weixin\component\ComponentAccessTokenHelper($componentAppId, new \app\framework\weixin\component\ComponentAccessTokenRepository());
                $componentAccessToken = $accessTokenHelper->getAccessToken();
                $wxApi = "https://api.weixin.qq.com/sns/oauth2/component/refresh_token?appid={$appId}&grant_type=refresh_token&component_appid={$componentAppId}&component_access_token={$componentAccessToken}&refresh_token={$refresgToken}";
            }
            Yii::trace('get from weixin, ' . $wxApi);

            $result = false;
            $fgcCnt = 0;
            while ($result === false && $fgcCnt < 3) {
                $result = file_get_contents($wxApi);
                $fgcCnt++;
            }

            if (!$result) {
                return null;
            }

            $resultObj = json_decode($result, true);
            $resultObj['wx_scope'] = $oauthInfo['wx_scope'];
            
            //如果refresh_token过期，则重新获取
            if ($resultObj["errcode"] == 42002) {
                $this->clearOAuthToken($public_id, $openid, $app_encrypt);
                return null;
            }
            
            if (empty($resultObj["openid"])) {
                $this->clearOAuthToken($public_id, $openid, $app_encrypt);
                \Yii::warning("刷新access_token失败! {$resultObj["errmsg"]}, openid:{$openid}, public_id:{$public_id}");
                return null;
            }

            $this->saveOAuthToken($public_id, $resultObj, $app_encrypt);

            return $resultObj;
        } else {
            return null;
        }
    }
    
    /**
     * 获取OAuth的token
     * @param string $public_id 公众号id
     * @param string $oauthInfo oauth授权数据
     */
    public function saveOAuthToken($public_id, $oauthInfo, $app_encrypt = "")
    {
        $oauthInfo["expire_time"] = time() + $oauthInfo["expires_in"] - 200;
        
        $cacheName = $this->getOAuthCacheKey($public_id, $oauthInfo['openid'], $app_encrypt);
         
        //refresh_token过期时间比较长（最长30天），但是策略较复杂，由调用处来判断是否已经过期，这里不设置缓存过期时间
        Yii::$app->cache->set($cacheName, $oauthInfo);
    }
    
    /**
     * 清除缓存
     */
    private function clearOAuthToken($public_id, $openid, $app_encrypt = "")
    {
        $cacheName = $this->getOAuthCacheKey($public_id, $openid, $app_encrypt);
        
        Yii::$app->cache->delete($cacheName);
    }
    
    /**
     * 获取缓存key
     * @param string $public_id 公众号id
     * @param string $openid 用户openid
     * @return 缓存key
     */
    private function getOAuthCacheKey($public_id, $openid, $app_encrypt = "")
    {
        $encryptKey = empty($app_encrypt) ? "" : md5($app_encrypt);
        return "oauth2_{$public_id}_{$openid}_{$encryptKey}";
    }
}

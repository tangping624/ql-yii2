<?php

namespace app\controllers;

use app\framework\utils\StringHelper;
use Yii;
 
use app\framework\utils\WebUtility;
use app\services\PublicAccountService;
use app\framework\web\extension\PassportController;
use app\services\WeixinOAuthService;
use yii\web\BadRequestHttpException;
use yii\web\Cookie;
use yii\web\HttpException;

class WxauthController extends PassportController
{

    /**
     * @var PublicAccountService
     */
    private $_accountService;
    private $_weixinOAuthService;

    public function __construct(
        $id,
        $module,
        PublicAccountService $accountService,
        WeixinOAuthService $weixinOAuthService,
        $config = []
    ) {
        $this->_weixinOAuthService = $weixinOAuthService;
        $this->_accountService = $accountService;
        parent::__construct($id, $module, $config);
    }

    public function actionLogout()
    { 
        $logoutUrl = WebUtility::createBeautifiedUrl('auth/logout');
        Yii::$app->response->redirect($logoutUrl);
    }

    public function actionRemoveSession($sid)
    {
        return $this->logOutApp($sid);
    }

    /** 
     * @param string $public_id 公众号id
     * @param string $return_url 需要获取openid的调用方url
     * @param string $app_encrypt 支持自定义公众号参数，使用public_id的商户密钥前16位进行加密，用于自定义公众号支付，默认为空
     * @param string $scope 微信的数据范围
     * @param string $_state 业务的_state
     * @param string $state 微信回传的state
     * @param string $code 微信回传的code
     * @throws BadRequestHttpException
     * @throws HttpException
     * @throws \Exception
     */
    public function actionOpenid( $public_id = '', $return_url = '', $app_encrypt = '', $scope = 'snsapi_base', $_state = '', $state = '', $code = '')
    {
        if (empty($scope)) {
            $scope = 'snsapi_base';
        }
         
        if (!in_array($scope, ['snsapi_base', 'snsapi_userinfo'])) {
            throw new BadRequestHttpException('scope无效:' . $scope);
        }

        if (empty($public_id) || empty($return_url)) {
            throw new BadRequestHttpException("public_id或return_url参数无效" . $_SERVER['HTTP_REFERER']);
        }
        
        $stateSessionKey = 'wx:openid:state';

        //回调时需要带上_state验证码, 对方会验证该_state合法性
        $myState = $_state;// \Yii::$app->request->get('_state', '');  // come from 业务 filter
        $wxState = $state;// \Yii::$app->request->get('state', ''); // from weixin return state
        $wxCode = $code;// \Yii::$app->request->get('code', ''); //come from weixin

        $encryptKey = empty($app_encrypt) ? "" : md5($app_encrypt);
        $cookieName = "openid_{$public_id}_{$encryptKey}";
        $cookieOpenid = '';
        $cookie = \Yii::$app->request->cookies->get($cookieName);
        if ($cookie) {
            $cookieOpenid = $cookie->value;
        }

        //加入缓存机制,并且支持获取oauth2的access_token
        if (!empty($cookieOpenid)) {
            $oauthInfo = $this->_weixinOAuthService->getOAuthToken( 
                $public_id,
                $cookieOpenid,
                $app_encrypt
            );
            $cachedScope = $oauthInfo['wx_scope'];
            // snsapi_userinfo 具有更高的权限
            if (!empty($oauthInfo) && ($cachedScope == 'snsapi_userinfo' || $cachedScope == $scope)) {
                $return_url = WebUtility::buildQueryUrl(
                    $return_url,
                    [
                        'openid' => $cookieOpenid,
                        'access_token' => $oauthInfo["access_token"],
                        '_state' => $myState,
                        'state' => $myState //云客需求
                    ]
                );
                    $this->redirect($return_url);
            }
        }

        $currentUrl = Yii::$app->request->absoluteUrl;

        /******************  获取appId, secret ***********************/
        $account = $this->_accountService->getAccount( $public_id, $app_encrypt);
        if ($account == false) {
            \Yii::trace('不存在该公众号,  public_id:' . $public_id . ', app_encrypt:' . $app_encrypt);
            throw new HttpException(403, '不存在该公众号');
        }
        $appId = $account['app_id'];
        $secret = $account['app_secret'];
        $isAuth =false;// $account['is_authed'] == 1;
        if (empty($appId)) {
            throw new \Exception('app_id 不能为空!');
        }
        if (!$isAuth && empty($secret)) {
            throw new \Exception('app_secret 不能为空!');
        }

        $cachedWxState = \Yii::$app->session->get($stateSessionKey, ''); //
        //校验是否来自微信的跳转
        if (!empty($wxCode) && $wxState == $cachedWxState) {
            \Yii::$app->session->remove($stateSessionKey);

            /******************  调用wx api 获取 openid, access_token ***********************/
            $wxApi = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appId}&secret={$secret}&code={$wxCode}&grant_type=authorization_code";
            if ($isAuth) {
                $componentAppId = \app\framework\weixin\proxy\component\WxComponent::getComponentAppId();
                $accessTokenHelper = new \app\framework\weixin\component\ComponentAccessTokenHelper($componentAppId, new \app\framework\weixin\component\ComponentAccessTokenRepository());
                $componentAccessToken = $accessTokenHelper->getAccessToken();
                $wxApi = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid={$appId}&code={$wxCode}&grant_type=authorization_code&component_appid={$componentAppId}&component_access_token={$componentAccessToken}";
            }
            
            \Yii::trace('get from weixin, ' . $wxApi);

            $result = false;
            $fgcCnt = 0;
            while ($result === false && $fgcCnt < 3) {
                $result = file_get_contents($wxApi);
                $fgcCnt++;
            }

            if ($result === false) {
                throw new \Exception('file_get_contents失败，网络错误');
            }

            $resultObj = json_decode($result, true);
            if (empty($resultObj["openid"])) {
                throw new \Exception('获取openid失败! $public_id: [' . $public_id . '], $appId: [' . $appId . '], cached_status:' . $cachedWxState . "\n" .' 微信返回:['. $result . ']');
            }

            /******************  保存token，避免频繁刷新 **********************************/
            $resultObj['wx_scope'] = $scope;
            $this->_weixinOAuthService->saveOAuthToken($public_id, $resultObj, $app_encrypt);

            $cookie = new Cookie();
            $cookie->name = $cookieName;
            $cookie->value = $resultObj['openid'];
            //cookie长时间不过期（1年）
            $cookie->expire = time() + 31536000;
            Yii::$app->response->cookies->add($cookie);

            /******************************  redirect back *************************************/

            $return_url = WebUtility::buildQueryUrl(
                $return_url,
                [
                    openid => $resultObj['openid'],
                    access_token => $resultObj["access_token"],
                    '_state' => $myState,
                    'state'=> $myState //云客需求
                ]
            );
            $this->redirect($return_url);
        } else {
            //缓存随机码
            $newState = StringHelper::random();
            \Yii::$app->session->set($stateSessionKey, $newState);
            $wxUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='
                . $appId . '&redirect_uri='
                . urlencode($currentUrl) . '&response_type=code&scope='. $scope .'&state='
                . $newState
                . ($isAuth ? ("&component_appid=" . \app\framework\weixin\proxy\component\WxComponent::getComponentAppId()) : "")
                . '#wechat_redirect';

            Yii::trace('get token from weixin, redirect to ' . $wxUrl);
            $this->redirect($wxUrl);
        }

    }
}

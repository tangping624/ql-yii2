<?php
 
namespace app\framework\oauth2;
 
use app\framework\utils\WebUtility;
use app\framework\webService\Curl;

class ClientRequest
{
    /**
     * @var AppKeyGetter
     */
    protected $appKeyGetter;
 

    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @var string
     */
    private $_tenantCode;


    public function __construct($tenantCode)
    {
        if(empty($tenantCode)){
            throw new \InvalidArgumentException('$tenantCode');
        }

        $this->_tenantCode = $tenantCode;

        if (\Yii::$container->has('app\framework\oauth2\AppKeyGetter')) {
            $this->appKeyGetter = \Yii::$container->get('app\framework\oauth2\AppKeyGetter');
        } else {
            $this->appKeyGetter = new AppKeyGetter();
        } 
        if(\Yii::$container->has('app\framework\webService\Curl')){
            $this->curl = \Yii::$container->get('app\framework\webService\Curl');
        }else{
            $this->curl = new Curl();
        }
    }

    /**
     * 获取passport access_token of oauth2
     * 已使用缓存
     * @param array $appKeyPair 指定appId, secret dict: ['id'=>'', 'secret' =>'']
     * @return string
     * @throws \Exception
     */
    public function getToken($appKeyPair=null)
    {
        if ($appKeyPair == null) {
            list($appId, $secret) = $this->appKeyGetter->getAppIdAndSecret($this->_tenantCode);
        } else {
            list($appId, $secret) = [$appKeyPair['id'], $appKeyPair['secret']];
        }

        $tokenCacheKey = 'oauth2:access_token:' . $this->_tenantCode . ':' . $appId;
        $cacheItem = \Yii::$app->cache->get($tokenCacheKey);
        if ($cacheItem != false) {
            return $cacheItem;
        }

        $accessTokenApiUrl = $this->_getAccessTokenApiUrl();
        //Init curl

        $param = [
            'grant_type' => 'client_credentials',
            'client_secret' => $secret,
            'client_id' => $appId
        ];

        $response = $this->curl->setOption(CURLOPT_POSTFIELDS, http_build_query($param))->post($accessTokenApiUrl);
        $result = json_decode($response);
        \Yii::trace('getToken: ' . $accessTokenApiUrl . 'params: ' . json_encode($param));
        if (isset($result->access_token)) {
            $expiresIn = $result->expires_in - 120 <= 0 ? $result->expires_in : $result->expires_in - 120;
            \Yii::$app->cache->set($tokenCacheKey, $result->access_token, $expiresIn);
            return $result->access_token;
        } else {
            throw new \Exception('获取token失败!' . $response);
        }
    }


    /**
     * @param $url
     * @param $params
     * @param string $method GET/POST
     * @param array|null $appKeyPair 指定appId, secret dict: ['id'=>'', 'secret' =>'']
     * @param bool|true $autoGetToken 是否自动获取access_token
     * 值为false则在$url参数里传入access_token
     * @return mixed
     * @throws \Exception
     */
    public function request($url, $params, $method, $appKeyPair=null, $autoGetToken=true)
    {
        if (!in_array($method, ['POST', 'GET'])) {
            throw new \InvalidArgumentException('$method参数只支持POST/GET');
        }

        $urlBuild = $url;
        if ($autoGetToken) {
            $token = $this->getToken($appKeyPair);
            if (empty($token)) {
                throw new \Exception('获取token失败!');
            }
            $urlBuild = WebUtility::buildQueryUrl($url, 'access_token=' . $token);
        }

        if ($method == 'GET') {
            $urlGet = WebUtility::buildQueryUrl($urlBuild, $params);
            \Yii::trace('start get' . $urlGet);
            $response = $this->curl->get($urlGet);
        } else {
            \Yii::trace('start post' . $urlBuild . ', with param:' . json_encode($params));
            $response = $this->curl->setOption(CURLOPT_POSTFIELDS, http_build_query($params))->post($urlBuild);
        }

        $result = json_decode($response);
        //token 无效,则重新获取access_token
        if (isset($result->errcode) && $result->errcode == 9003 && $autoGetToken) {
            $token = $this->getToken($appKeyPair);
            if (empty($token)) {
                throw new \Exception('获取token失败!');
            }
            $urlBuild = WebUtility::buildQueryUrl($url, 'access_token=' . $token);

            if ($method == 'GET') {
                $urlGet = WebUtility::buildQueryUrl($urlBuild, $params);
                \Yii::trace('start get' . $urlGet);
                $response = $this->curl->get($urlGet);
            } else {
                \Yii::trace('start post' . $urlBuild . ', with param:' . json_encode($params));
                $response = $this->curl->setOption(CURLOPT_POSTFIELDS, http_build_query($params))->post($urlBuild);
            }

            return $response;
        } else {
            return $response;
        }
    }
     private function _getManageSiteUrl()
    {
        $settingAccessor = Yii::$container->get('app\framework\settings\interfaces\SettingsAccessorInterface');
        $config = $settingAccessor->get('manage_site');
        if (!isset($config)) {
            throw new \Exception('缺少配置项 manage_site');
        }
        return $config;
    }
    private function _getAccessTokenApiUrl()
    {
        $passportUrl = $this->_getManageSiteUrl();
        if (substr($passportUrl, -1) != '/') {
            $passportUrl = $passportUrl . '/';
        }
        return $passportUrl . '/oauth2/access_token';
    }
}
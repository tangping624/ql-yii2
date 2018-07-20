<?php

namespace app\framework\weixin;

/*
 * 获取accesstoken帮助类，支持企业号、服务号、订阅号，其中对于服务号和订阅号支持开发模式和授权模式
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use app\framework\weixin\interfaces\IAccessTokenRepository;

/**
 * Description of AccessTokenHelper
 *
 * @author Chenxy
 */
class AccessTokenHelper implements interfaces\IAccessTokenHelper
{
    /**
     * accessTokenRepository
     * @var \app\framework\weixin\IAccessTokenRepository
     */
    public $accessTokenRepository;
    
    protected $_wxInvoker;
    protected $_id;
    private $_isAuthAccess;
    
    /**
     * 构造方法
     * @param string $id 公众号对应的唯一标识符,支持account_id和original_id
     * @param \app\framework\weixin\IAccessTokenRepository $repository
     */
    public function __construct($id, IAccessTokenRepository $repository)
    {
        $this->accessTokenRepository = $repository;
        $this->_id = $id;
        $this->_isAuthAccess = ($repository->getConfigValue($id, 'is_authed') == 1);
        // 通过仓储获取当前调用接口的信息
        $this->_wxInvoker = $this->accessTokenRepository->getWxInvoker($id);
        // 增强健壮性
        if ($this->_isAuthAccess && empty($this->_wxInvoker->authRefreshToken)) {
            $authRefreshToken = $repository->getConfigValue($id, 'authorizer_refresh_token');
            $this->_wxInvoker->authRefreshToken = $authRefreshToken;
        }
    }
    
    /**
     * 获取access_token在url的参数名
     * @return string
     */
    public function getAccessTokenParamName()
    {
        // 官方文档说明（只是需将调用API时提供的公众号自身access_token参数，替换为authorizer_access_token）但实际不是
        return "access_token";
        //return $this->_isAuthAccess ? "authorizer_access_token" : "access_token";
    }
    
    /**
     * 吴中打通方案提供
     * @param type $forceExpire
     * @return type
     */
    public function accessToken($forceExpire = false)
    {
        if ($forceExpire) {
            // 不再提供强制过期功能
            //$this->makeExpire();
        }
        
        if ($this->isExpire()) {
            $this->freshAccessToken();
        }
        
        return ['access_token' => $this->_wxInvoker->accessToken
               ,'expire_time' => $this->_wxInvoker->expireTime];
    }
    
    /**
     * 获取access_token
     * @return string
     */
    public function getAccessToken()
    {
        if ($this->isExpire()) {
            $this->freshAccessToken();
        }
        
        return $this->_wxInvoker->accessToken;
    }
    
    /**
     * 设置access_token过期,无参时强制过期
     * @param int $errorCode
     */
    public function makeExpire($errorCode = -1)
    {
        if ($errorCode == -1 || $this->checkIsExpired($errorCode)) {
            $this->_wxInvoker->accessToken = '';
            $this->_wxInvoker->expireTime = 0;
            $this->accessTokenRepository->updateAccessToken($this->_id, '', null);
        }
    }
    
    /**
     * 通过接口获取access_token
     * @throws WeixinException
     */
    protected function freshAccessToken()
    {
        $result = $this->_isAuthAccess
                ? $this->invokeAccesTokenByWechatAuth()
                : $this->invokeAccessTokenByAppSecert();
        
        // 获取accesstoken失败
        if (isset($result->errcode)) {
            throw new WeixinException('获取access_token失败，错误码：' . $result->errcode . '消息：' . $result->errmsg);
        }
        
        $accessToken = $this->_isAuthAccess ? $result->authorizer_access_token : $result->access_token;
        $authRefreshToken = $this->_isAuthAccess ? $result->authorizer_refresh_token : "";
        // 有效期－60
        $expireTime = time() + intval($result->expires_in) - 60;
        
        $this->_wxInvoker->accessToken = $accessToken;
        $this->_wxInvoker->expireTime = $expireTime;
        $this->_wxInvoker->authRefreshToken = $authRefreshToken;
        
        // 更新
        $this->accessTokenRepository->updateAccessToken($this->_id, $accessToken, $expireTime, $authRefreshToken);
    }
    
    /**
     * 判断access_token是否已过期
     * @return bool
     */
    protected function isExpire()
    {
        return empty($this->_wxInvoker)
                || empty($this->_wxInvoker->accessToken)
                || empty($this->_wxInvoker->expireTime)
                || time() >= $this->_wxInvoker->expireTime;
    }
    
    private function invokeAccesTokenByWechatAuth()
    {
        $apiProxy = new proxy\component\WxComponent();
        $appId = $this->_wxInvoker->appId;
        $result = $apiProxy->getAuthorizerToken($appId, $this->_wxInvoker->authRefreshToken);
        return $result;
    }
    
    private function invokeAccessTokenByAppSecert()
    {
        $restClient = new \app\framework\webService\RestClientHelper();
        $apiUrl = $this->_wxInvoker->buildGetTokenUrl();
        $result = $restClient->invoke($apiUrl, []);
        return $result;
    }
    
    /**
     * 根据错误码判断access_token是否过期
     * @param int $errorCode
     * @return bool
     */
    public function checkIsExpired($errorCode)
    {
        return ($errorCode == 42001 || $errorCode == 40001 || $errorCode == 40014);
    }
    
    /**
     * 写接口调用日志
     * @param type $method
     * @param type $invokeUrl
     * @param type $invokeTime
     * @param type $parameter
     * @throws \Exception
     */
    public function writeLog($method, $invokeUrl, $invokeTime, $parameter)
    {
        try {
            $accountId = $this->_accessTokenRepository->getConfigValue($this->_id, 'id');
        } catch (\Exception $ex) {
            $accountId = $this->_id;
        }
        
        $logRow = [
            'id' => \app\framework\utils\StringHelper::uuid(),
            'account_id' => $accountId,
            'method' => $method,
            'invoke_url' => $invokeUrl,
            'invoke_time' => $invokeTime,
            'parameter' => $parameter
        ];
          $dbConn = \app\framework\db\EntityBase::getDb();
        $dbConn->createCommand()->insert('p_wechat_api_log', $logRow)->execute();
    }
}

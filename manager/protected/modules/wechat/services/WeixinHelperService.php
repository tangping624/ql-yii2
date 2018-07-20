<?php

namespace app\modules\wechat\services;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of WeixinHelperService
 *
 * @author Chenxy
 */

use app\framework\weixin\proxy\fw\CustomService;
use app\modules\ServiceBase;
use app\framework\weixin\AccessTokenHelper;

class WeixinHelperService extends ServiceBase
{
    private $_accessTokenRepository;
    private $_wxComponentApiProxy;
    private $_wxComponentAppId;

    public function __construct()
    {
        $this->_accessTokenRepository = \Yii::$container->get('app\framework\weixin\interfaces\IAccessTokenRepository');
        $this->_wxComponentAppId = \app\framework\weixin\proxy\component\WxComponent::getComponentAppId();
        $this->_wxComponentApiProxy = new \app\framework\weixin\proxy\component\WxComponent($this->_wxComponentAppId);
    }

    /**
     * 获取预授权码
     * @return string
     */
    public function getPreAuthCode()
    {
        $apiResult = $this->_wxComponentApiProxy->createPreauthcode();
        return $apiResult->pre_auth_code;
    }
    
    /**
     * 获取公众号授权信息
     * @param string $authCode
     * @return array
     */
    public function getWechatAuthInfo($authCode)
    {
        $apiResult = $this->_wxComponentApiProxy->queryAuth($authCode);
        $authAppId= $apiResult->authorization_info->authorizer_appid;
        $authAccessToken= $apiResult->authorization_info->authorizer_access_token;
        $expriesIn= $apiResult->authorization_info->expires_in;
        $authRefreshToken= $apiResult->authorization_info->authorizer_refresh_token;
        $authFuncInfo= $apiResult->authorization_info->func_info;
        // 获取授权信息
        $data = ['app_id' => $authAppId,
            'authorizer_refresh_token' => $authRefreshToken,
            'authorizer_code' => $authCode,
            'authorizer_func' => json_encode($authFuncInfo),
            'authorizer_access_token' => $authAccessToken,
            'expries_in' => $expriesIn
        ];
        
        // 确认授权后再更新这里不更新
//        $this->_accessTokenRepository->updateAccessToken($authAppId, $authAccessToken, $expriesIn - 300);
        
        return $data;
    }
    
    /**
     * 获取微信公众号信息
     * @param string $appId
     * @return array
     */
    public function getWechatAccountInfo($appId)
    {
        $apiResult = $this->_wxComponentApiProxy->getAuthorizerInfo($appId);
        $data = ['name' => $apiResult->authorizer_info->nick_name, // 名称
                'type' => $apiResult->authorizer_info->service_type_info->id == 2 ? '服务号' : '订阅号',
                'qrcode_url' => $apiResult->authorizer_info->qrcode_url,
                'headimg_url' => $apiResult->authorizer_info->head_img,
                'original_id' => $apiResult->authorizer_info->user_name,
                'wechat_number' => $apiResult->authorizer_info->alias // 微信号
        ];
        
        return $data;
    }

    public function getKfList($accountId)
    {
        try {
            $accessTokenHelper = new AccessTokenHelper($accountId, $this->_accessTokenRepository);
            $kfInfo = new CustomService($accessTokenHelper);
            $kfList = $kfInfo->getKfList();
            $kfAccounts = [];
            foreach ($kfList->kf_list as $row) {
                $kfAccounts[] = $row->kf_account;
            }
            return $kfAccounts;
        } catch (\Exception $ex) {
            \Yii::error($ex->getMessage());
            throw $ex;
        }
    }
}

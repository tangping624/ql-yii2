<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\api\services;

use app\framework\weixin\msg\HttpMsgContext;
use app\framework\weixin\msg\MessageServer;

/**
 * Description of FilterInvalidAppIdModule 拦截非法AppId(如：删除了公众号或改为授权模式但还在不停的推送消息)
 * 只用于老的开发模式
 * @author chenxy
 */
class FilterInvalidAppIdModule implements \app\framework\weixin\interfaces\IHttpMsgModule
{
    public function init(MessageServer $app)
    {
        $app->beforeExecHandleEvents[__CLASS__] = 'begin_request_event';
    }
    
    // 拦截非法Appid请求（只支持开发模式）
    public static function begin_request_event(HttpMsgContext $context, $args)
    {
        // 拦截无效的appid请求
        $appId = $context->request->getQueryParam("appid", "");
        $accessTokenRepository = \Yii::$container->get('app\framework\weixin\interfaces\IAccessTokenRepository');
        if (empty($appId)) {
                $originalId = $context->request->requestData['ToUserName'];
                $appId = $accessTokenRepository->getConfigValue($originalId, 'app_id');
        }
        if (empty($appId)) {
            exit("success");
        }
        // 判断APPID是否有效
       // $accessTokenRepository = \Yii::$container->get('app\framework\weixin\interfaces\IAccessTokenRepository');
        // 尝试判断appid是否有效，无效的进行拦截
        try {
            $accessTokenRepository->getConfigValue($appId, 'id');
        } catch (\app\framework\weixin\WeixinException $wxEx) {
            exit("success");
        }
        
        $isAuthed = $accessTokenRepository->getConfigValue($appId, 'is_authed');
        // 授权后发送要拦截
        if ($isAuthed) {
            exit("success");
        }
    }
}

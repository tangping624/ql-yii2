<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\api\services;

use app\framework\weixin\msg\HttpMsgContext;
use app\framework\weixin\msg\MessageServer;
use app\framework\weixin\helper\MsgSecretHelper;

/**
 * Description of FilterInvalidRequestModule 拦截非法请求（如重新授权的）
 *
 * @author chenxy
 */
class FilterInvalidRequestModule implements \app\framework\weixin\interfaces\IHttpMsgModule
{
    public function init(MessageServer $app)
    {
        $app->beforeExecHandleEvents[__CLASS__] = 'begin_request_event';
    }
    
    // 拦截非法请求（只支持托管模式）
    public static function begin_request_event(HttpMsgContext $context, $args)
    {
        // 签名验证拦截
        $appId = \app\framework\weixin\proxy\component\WxComponent::getComponentAppId();
        $repo = new \app\framework\weixin\component\ComponentAccessTokenRepository();
        $token = $repo->getConfigValue($appId, 'token');
        if (!\app\framework\weixin\helper\MsgSecretHelper::validataSignature($context->request->postXml, $token)) {
            \Yii::error("拦截到非法访问(签名错误)IP：" . $_SERVER['REMOTE_ADDR'] . ",xml:" . $context->request->postXml);
            exit("success");
        }
        
        // 全网发布验证不拦截
        if ($context->request->isFullWebPublishing) {
            return;
        }
        
        // 第三方平台通知不拦截
        if (array_key_exists('InfoType', $context->request->requestData)) {
            return;
        }
        
        // 拦截无效的appid请求
        $appId = $context->request->getQueryParam("appid", "");
        if (empty($appId)) {
            \Yii::error("拦截到非法访问(非白名单APPID)IP：" . $_SERVER['REMOTE_ADDR']);
            exit("success");
        }
       
        $accountRepo = new \app\modules\api\repositories\PublicAccountRepository();
        $tenantCode = $accountRepo->getTenantCodeByAppId($appId);
        if ($tenantCode === false) {
            exit("success");
        }
        
        // todo:第三方白名单拦截，需要提供白名单
    }
}

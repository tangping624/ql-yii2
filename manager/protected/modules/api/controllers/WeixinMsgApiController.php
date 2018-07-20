<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\api\controllers;

require_once(__DIR__  . '/../../../framework/3rd/wxmsg/wxBizMsgCrypt.php');

use app\framework\weixin\exceptions\AccountNotFoundException;
use app\modules\api\services\PublicAccountService;
use app\framework\weixin\helper\MsgSecretHelper;

/**
 * 微信消息API事件通知接口
 *
 * @author Chenxy
 */
class WeixinMsgApiController extends \app\framework\web\extension\Controller
{
    private $_publicAccountService;

    public function __construct($id, $module, PublicAccountService $publicAccountService, $config = [])
    {
        $this->_publicAccountService = $publicAccountService;
        parent::__construct($id, $module, $config);
    }
    
    /**
     * 站点测试
     */
    public function actionTest()
    {
        /*
        try {
            $appId = $this->request->get('appid');
            if ($appId == "wx570bc396a51b8ff8") {
                $data = [];
            } else {
                $tenantInfo = $this->_publicAccountService->getTenantInfoByAppId($appId);
                $data = ['tenantCode' => $tenantInfo['code'], 'tenantDbConn' => $tenantInfo['dbConn']];
            }
            // 加载推送事件处理
            $eventHandler = new \app\modules\api\services\WeixinEventHandler($data);
            // 加载推送消息处理
            $msgHandler = new \app\modules\api\services\WeixinMessageHandler($data);
            // 加载全网发布验证
            $publishingHandler = new \app\framework\weixin\msg\FullWebPublishingHandler($data);
            $processor = new \app\framework\weixin\msg\MessageProcessor();
            // 安装业务处理器
            $processor->install($eventHandler)->install($msgHandler)->install($publishingHandler);
            $app = \app\framework\weixin\msg\MessageServer::getApp($processor);
            // 注册非法请求拦截程序
//            $app->regist('\app\modules\api\services\FilterInvalidRequestModule');
            // 注册消息包加解密处理程序
//            $app->regist('\app\framework\weixin\msg\HttpMsgCryptModule');
            // 注册消息分发程序
//            $app->regist('\app\modules\api\services\WechatMsgForwardModule');
            // 处理请求
            $app->processRequest();
        } catch (AccountNotFoundException $notFoundEx) {
            // 记录日志;
            \Yii::warning($notFoundEx->getMessage());
            exit('success');
        } catch (\Exception $ex) {
            // 记录日志;
            \Yii::error($ex);
            exit('success');
        }
        
//        $this->getFakeSendMsg();
       // $this->getEncryptMsg();
//        $this->getEncryptTicket();
         */
        $uri = $_SERVER['REQUEST_URI'];
        echo 'OK[' . $uri . ']';
    }
    
    /**
     * 接收非微信转发的消息事件
     */
    public function actionThird()
    {
        try {
            $appId = $this->request->get('appid');
            $tenantInfo = $this->_publicAccountService->getTenantInfoByAppId($appId);
            $data = ['tenantCode' => $tenantInfo['code'], 'tenantDbConn' => $tenantInfo['dbConn']];
            // 加载推送事件处理
            $eventHandler = new \app\modules\api\services\WeixinEventHandler($data);
            // 加载推送消息处理
            $msgHandler = new \app\modules\api\services\WeixinMessageHandler($data);
            $processor = new \app\framework\weixin\msg\MessageProcessor();
            // 安装业务处理器
            $processor->install($eventHandler)->install($msgHandler);
            $app = \app\framework\weixin\msg\MessageServer::getApp($processor);
            // 注册非法请求拦截程序
            $app->regist('\app\modules\api\services\FilterInvalidRequestModule');
            // 注册消息包加解密处理程序
            $app->regist('\app\framework\weixin\msg\HttpMsgCryptModule');
            // 处理请求
            $app->processRequest();
        } catch (AccountNotFoundException $notFoundEx) {
            // 记录日志;
            \Yii::warning($notFoundEx->getMessage());
            exit('success');
        } catch (\Exception $ex) {
            // 记录日志;
            \Yii::error($ex);
            exit('success');
        }
    }
    
    /**
     * 接收微信消息事件（公众号授权模式）
     * @throws \Exception
     */
    public function actionWechat()
    {
        try {
            $appId = $this->request->get('appid');
            if ($appId == "wx570bc396a51b8ff8") {
                $data = [];
            } else {
                $tenantInfo = $this->_publicAccountService->getTenantInfoByAppId($appId);
                $data = ['tenantCode' => $tenantInfo['code'], 'tenantDbConn' => $tenantInfo['dbConn']];
            }
            
            // 找不到租户
            if (isset($tenantInfo) && empty($tenantInfo['code'])) {
                exit('success');
            }
            
            // 加载推送事件处理
            $eventHandler = new \app\modules\api\services\WeixinEventHandler($data);
            // 加载推送消息处理
            $msgHandler = new \app\modules\api\services\WeixinMessageHandler($data);
            // 加载全网发布验证
            $publishingHandler = new \app\framework\weixin\msg\FullWebPublishingHandler($data);
            $processor = new \app\framework\weixin\msg\MessageProcessor();
            // 安装业务处理器
            $processor->install($eventHandler)->install($msgHandler)->install($publishingHandler);
            $app = \app\framework\weixin\msg\MessageServer::getApp($processor);
            // 注册非法请求拦截程序
            $app->regist('\app\modules\api\services\FilterInvalidRequestModule');
            // 注册消息包加解密处理程序
            $app->regist('\app\framework\weixin\msg\HttpMsgCryptModule');
            // 注册消息分发程序
            $app->regist('\app\modules\api\services\WechatMsgForwardModule');
            // 处理请求
            $app->processRequest();
        } catch (AccountNotFoundException $notFoundEx) {
            // 记录日志;
            \Yii::warning($notFoundEx->getMessage());
            exit('success');
        } catch (\Exception $ex) {
            // 记录日志;
            \Yii::error($ex);
            exit('success');
        }
    }
    
    /**
     * 接收微信开放平台推送事件（推送component_verify_ticket协议和推送取消授权通知）
     */
    public function actionTicket()
    {
        try {
            // 加载全网发布验证
            $publishingHandler = new \app\framework\weixin\msg\FullWebPublishingHandler([]);
            $componentHandler = new \app\modules\api\services\WeixinTicketHandler([]);
            $processor = new \app\framework\weixin\msg\MessageProcessor();
            // 安装业务处理器
            $processor->install($componentHandler)->install($publishingHandler);
            $app = \app\framework\weixin\msg\MessageServer::getApp($processor);
            // 注册非法请求拦截程序
            $app->regist('\app\modules\api\services\FilterInvalidRequestModule');
            // 注册消息包加解密处理程序
            $app->regist('\app\framework\weixin\msg\HttpMsgCryptModule');
            // 处理请求
            $app->processRequest();
        } catch (\Exception $ex) {
            // 记录日志;
            \Yii::error($ex);
            exit('success');
        }
    }
    
    public function actionDecrypt()
    {
        $postXml = file_get_contents("php://input");
    }
    
    private function getSignature()
    {
        $xml = $this->getEncryptMsg();
        $appId = \app\framework\weixin\proxy\component\WxComponent::getComponentAppId();
        $repo = new \app\framework\weixin\component\ComponentAccessTokenRepository();
        $token = $repo->getConfigValue($appId, 'token');
        $tmpStr = MsgSecretHelper::createSignature($xml, $token);
        return $tmpStr;
    }
    
    private function getEncryptTicket()
    {
        $originalXml = '<xml>
        <AppId><![CDATA[wx570bc396a51b8ff8]]></AppId>
        <CreateTime>63472346</CreateTime>
        <InfoType><![CDATA[component_verify_ticket]]></InfoType>
        <ComponentVerifyTicket><![CDATA[testticket567]]></ComponentVerifyTicket>
        </xml>';
        // 加密消息
        $appId = \app\framework\weixin\proxy\component\WxComponent::getComponentAppId();
        $repo = new \app\framework\weixin\component\ComponentAccessTokenRepository();
        $token = $repo->getConfigValue($appId, 'token');
        $encodingAesKey = $repo->getConfigValue($appId, 'encoding_key');
        $msgCrypt = new \WXBizMsgCrypt($token, $encodingAesKey, $appId);
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $msgXml = '';
        $errCode = $msgCrypt->encryptMsg($originalXml, $timestamp, $nonce, $msgXml);
        if ($errCode != \ErrorCode::$OK) {
            throw new \app\framework\weixin\WeixinException("加密消息包出错，错误码：{$errCode}, 消息包：" . $context->response->responseXml);
        }
        
        return $msgXml;
    }
    
    private function getEncryptMsg()
    {
        $originalXml = '<xml>
        <ToUserName><![CDATA[gh_3c884a361561]]></ToUserName>
        <FromUserName><![CDATA[o28Pmsm9V0uikDdKaNR8WQM5yRZM]]></FromUserName>
        <CreateTime>63472346</CreateTime>
        <MsgType><![CDATA[event]]></MsgType>
        <Event><![CDATA[subscribe]]></Event>
        </xml>';
        // 加密消息
        $appId = \app\framework\weixin\proxy\component\WxComponent::getComponentAppId();
        $repo = new \app\framework\weixin\component\ComponentAccessTokenRepository();
        $token = $repo->getConfigValue($appId, 'token');
        $encodingAesKey = $repo->getConfigValue($appId, 'encoding_key');
        $msgCrypt = new \WXBizMsgCrypt($token, $encodingAesKey, $appId);
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $msgXml = '';
        $errCode = $msgCrypt->encryptMsg($originalXml, $timestamp, $nonce, $msgXml);
        if ($errCode != \ErrorCode::$OK) {
            throw new \app\framework\weixin\WeixinException("加密消息包出错，错误码：{$errCode}, 消息包：" . $context->response->responseXml);
        }
        
        return $msgXml;
    }
    
    private function getFakeSendMsg()
    {
        $originalXml = '<xml>
        <ToUserName><![CDATA[gh_3c884a361561]]></ToUserName>
        <FromUserName><![CDATA[o28Pmsm9V0uikDdKaNR8WQM5yRZM]]></FromUserName>
        <CreateTime>63472346</CreateTime>
        <MsgType><![CDATA[text]]></MsgType>
        <Content><![CDATA[QUERY_AUTH_CODE:query_auto_code_123]]></Content>
        <MsgId>1234567890123456</MsgId>
        </xml>';
        // 加密消息
        $appId = \app\framework\weixin\proxy\component\WxComponent::getComponentAppId();
        $repo = new \app\framework\weixin\component\ComponentAccessTokenRepository();
        $token = $repo->getConfigValue($appId, 'token');
        $encodingAesKey = $repo->getConfigValue($appId, 'encoding_key');
        $msgCrypt = new \WXBizMsgCrypt($token, $encodingAesKey, $appId);
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $msgXml = '';
        $errCode = $msgCrypt->encryptMsg($originalXml, $timestamp, $nonce, $msgXml);
        if ($errCode != \ErrorCode::$OK) {
            throw new \app\framework\weixin\WeixinException("加密消息包出错，错误码：{$errCode}, 消息包：" . $context->response->responseXml);
        }
        
        return $msgXml;
    }
}

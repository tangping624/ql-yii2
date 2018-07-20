<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\api\services;

require_once(__DIR__  . '/../../../framework/3rd/wxmsg/wxBizMsgCrypt.php');

use app\framework\weixin\msg\HttpMsgContext;
use app\framework\weixin\msg\MessageServer;
use app\modules\api\services\PublicAccountService;
use app\modules\api\repositories\PublicAccountRepository;

/**
 * Description of WechatMsgForwardModule
 *
 * @author chenxy
 */
class WechatMsgForwardModule implements \app\framework\weixin\interfaces\IHttpMsgModule
{
    const WECHAT_MSG_FORWARD_SETTING_KEY_PREFIX = "wechat_msg_forward_setting_";
    
    public function init(MessageServer $app)
    {
        $app->beforeExecHandleEvents[__CLASS__] = 'before_exec_handle_event';
    }
    
    // 分发请求
    public static function before_exec_handle_event(HttpMsgContext $context, $args)
    {
        // 全网发布验证不分发
        if ($context->request->isFullWebPublishing) {
            return;
        }

        // 第三方平台通知不分发
        if (array_key_exists('InfoType', $context->request->requestData)) {
            return;
        }

        // 拦截不需要转发的事件通知
        if (self::filterEvents($context->request)) {
            return;
        }
        
        // 转发失败不拦截
        try {
            // 根据公众号原始ID查找AppId
            $appId = $context->request->getQueryParam('appid', '');
            $accessTokenRepository = \Yii::$container->get('app\framework\weixin\interfaces\IAccessTokenRepository');
            if (empty($appId)) {
                $originalId = $context->request->requestData['ToUserName'];
                $appId = $accessTokenRepository->getConfigValue($originalId, 'app_id');
            }
            
//            // 旧模式转发保留，后续删除（与云客吴中对接）
            $token = $accessTokenRepository->getConfigValue($appId, 'token');
            $forwardUrl = $accessTokenRepository->getConfigValue($appId, 'partner_event_url');
            $encodingKey = $accessTokenRepository->getConfigValue($appId, 'partner_secret_key');
            self::forwardPartnerEvent($appId, $token, $encodingKey, $forwardUrl, $context->request->requestXml);
            
            // 队列模式
            $accountId = $accessTokenRepository->getConfigValue($appId, 'id');
            $thirdList = self::getDistributeThirdList($appId, $accountId);

            // 未设置不转发
            if (count($thirdList) == 0) {
                return;
            }

            // 明文入列
            $queueData = ['appId' => $appId, 'xml' => $context->request->requestXml, 'thirdList' => $thirdList];
            \Yii::$app->queue->enque("forward-msg:{$appId}", 'app\job\jobs\weixin\WechatMsgForwardPartnerJob', $queueData, true);
        } catch (\Exception $ex) {
            \Yii::error(['data' => ['errmsg' => $ex->getMessage(), 'data' => $context->request->requestXml], 'msg' => "转发微信消息失败"]);
        }
    }
    
    private static function getDistributeThirdList($appId, $accountId)
    {
        // 取缓存
        $cacheKey = static::WECHAT_MSG_FORWARD_SETTING_KEY_PREFIX . $accountId;
        $cache = \Yii::$app->cache;
        if ($cache->exists($cacheKey)) {
            return $cache[$cacheKey];
        }
        
//        $tenantCode = \app\framework\weixin\helper\BizTenantCodeHelper::getTenantCodeByAppId($appId);
//        if (empty($tenantCode)) {
//            $tenantReader= \Yii::$container->get('app\framework\biz\tenant\TenantReaderInterface');
//            $tenantCode = $tenantReader->getCurrentTenantCode();
//        }
//        $dbConn = \app\framework\biz\cache\OrganizationCacheManager::getTenantDbConn($tenantCode);
        $dbConn = \app\framework\db\EntityBase::getDb();
        $query = new \yii\db\Query();
        $rows = $query->from('p_forward_setting')
                ->select("id, partner_name, url, token, secret_key")
                ->where("account_id =:account_id and is_deleted = 0", [':account_id' => $accountId])
                ->all($dbConn);
        
        $cache[$cacheKey] = $rows;
        return $rows;
    }
    
    /**
     * 拦截不转发的事件
     * @param \app\framework\weixin\msg\HttpMsgRequest $request
     */
    private static function filterEvents($request)
    {
        $data = $request->requestData;
        return array_key_exists('InfoType', $data)
                || (array_key_exists('Event', $data) && strtolower($data['Event']) == "masssendjobfinish")
                || (array_key_exists('Event', $data) && strtolower($data['Event']) == "templatesendjobfinish")
                || (array_key_exists('Event', $data) && strtolower($data['Event']) == "kf_create_session")
                || (array_key_exists('Event', $data) && strtolower($data['Event']) == "kf_close_session")
                || (array_key_exists('Event', $data) && strtolower($data['Event']) == "kf_switch_session");
        
    }
    
    /**
     * 旧模式转发(明文转发)
     */
    private static function forwardPartnerEvent($appId, $token, $encodingKey, $forwardUrl, $nocryptMsg)
    {
        if (empty($forwardUrl)) {
            return;
        }
        $timestamp = time();
        $nonce = \app\framework\auth\NonceService::createNonce(6, false);
        $regex = "/^[0-9a-zA-Z]{43}$/";
        if (empty($encodingKey) || !preg_match($regex, $encodingKey)) {
            throw new \app\framework\weixin\WeixinException("无效的加密key:{$encodingKey},必须由43位字母和数字组成");
        }
        $encryptXml = self::cryptMsg($nocryptMsg, $appId, $token, $encodingKey, $timestamp, $nonce);
        $signature = \app\framework\weixin\helper\MsgSecretHelper::createSignature($encryptXml, $token);
        $url = $forwardUrl . (strpos($forwardUrl, "?") === false ? "?" : "&") . "appid={$appId}&timestamp={$timestamp}&nonce={$nonce}&msg_signature={$signature}";
        // by test
//        $url = "http://localhost:85/mysoft/member/test/recive-notice?account_id=39d039b2-d8be-bfe7-3310-d34ecb76e09b";
        $parts = parse_url($url);
        $fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $errno, $errstr, 30);

        if ($fp === false) {
            fclose($fp);
            throw new \Exception("转发微信事件消息时发生错误：{$errstr}({$errno})，消息包：" . $nocryptMsg);
        }
        $out = "POST " . $parts['path'] . '?' . $parts['query']. " HTTP/1.1\r\n";
        $out .= "Host: " . $parts['host'] . "\r\n";
        $out .= "Content-Type: text/xml\r\n";
        $out .= "Content-Length: " . strlen($encryptXml) . "\r\n";
        $out .= "Connection: Close\r\n\r\n";
        $out .= $encryptXml;
        fwrite($fp, $out);
        $content = '';
        while (!feof($fp)) {
            $content = $content . fgets($fp, 1024);
        }
        fclose($fp);
    }

    private static function cryptMsg($msg, $appId, $token, $encodingAesKey, $timeStamp, $nonce)
    {
        $msgCrypt = new \WXBizMsgCrypt($token, $encodingAesKey, $appId);
        $msgXml = '';
        $errCode = $msgCrypt->encryptMsg($msg, $timeStamp, $nonce, $msgXml);
        if ($errCode != \ErrorCode::$OK) {
            throw new \app\framework\weixin\WeixinException("加密消息包出错，错误码：{$errCode}, 消息包：" . $msg);
        }
        
        return $msgXml;
    }
    
    /**
     * 清除第三方消息转发设置缓存
     * @param string $id 公众号id
     */
    public static function clearWechatMsgForwardSettingCache($id)
    {
        $cacheKey = static::WECHAT_MSG_FORWARD_SETTING_KEY_PREFIX . $id;
        $cache = \Yii::$app->cache;
        $cache->exists($cacheKey) && $cache->delete($cacheKey);
    }
}

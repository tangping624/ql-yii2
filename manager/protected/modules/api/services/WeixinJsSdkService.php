<?php

namespace app\modules\api\services;

use Yii;
use app\framework\weixin\DbAccessTokenRepository;
use app\modules\wechat\repositories\AccountRepository;
use app\framework\weixin\AccessTokenHelper;
use app\framework\weixin\proxy\fw\JsSdk;

/**
 * 微信JS-SDK服务类
 *
 * @author Zengsy
 */
class WeixinJsSdkService
{

    private $_accountRepository;
    private $_accessTokenRepository;

    public function __construct(AccountRepository $accountRepository, DbAccessTokenRepository $accessTokenRepository)
    {
        $this->_accountRepository = $accountRepository;
        $this->_accessTokenRepository = $accessTokenRepository;
    }

    /**
     * 获取js-sdk签名包
     * @param type $accountId 公众号id
     * @param type $url 要执行的js-api的url
     * @return array json签名包
     */
    public function getSignPackage($accountId, $url)
    {
        $jsapiTicket = $this->getJsSdkTicket($accountId);

        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = [
            "appId" => $this->getAppId($accountId),
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature
        ];
        return $signPackage;
    }

    private function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function getAppId($accountId)
    {
        $accountInfo = $this->_accountRepository->findAccountInfoById($accountId);
        if ($accountInfo === false || !$accountInfo['app_id']) {
            throw new \Exception("公众号{$accountId}不存在，或者app_id为空");
        }
        
        return $accountInfo['app_id'];
    }

    /**
     * 获取accessToken帮助类
     * @param string $accountId 公众号id
     * @return \app\modules\api\services\AccessTokenHelper
     */
    private function getAccessTokenHelper($accountId)
    {
        $wechat = $this->_accountRepository->getWeChatOriginalId($accountId);
        return new AccessTokenHelper($wechat, $this->_accessTokenRepository);
    }

    /**
     * 获取jssdk票据
     * @param string $accountId 公众号id
     * @return string jssdk_ticket
     */
    private function getJsSdkTicket($accountId)
    {
        $key = 'JsSdkTicket_' . $accountId;

        $data = Yii::$app->cache->get($key);

        //如果有缓存，并且票据没过期，则直接返回
        if ($data && time() < $data->expire_time) {
            return $data->ticket;
        } else {
            $jsApi = new JsSdk($this->getAccessTokenHelper($accountId));

            $result = $jsApi->getTicket();

            if ($result->errcode != 0) {
                Yii::error("获取jssdk票据出现错误，微信返回:" . json_encode($result));
                throw new \Exception($result->errmsg);
            }

            $expireIn = $result->expires_in - 200;

            if ($expireIn <= 0) {
                $expireIn = 7000;
            }

            $expireTime = time() + $expireIn;

            $result->expire_time = $expireTime;

            Yii::$app->cache->set($key, $result, $expireIn);

            return $result->ticket;
        }
    }
}

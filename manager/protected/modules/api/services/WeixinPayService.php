<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\api\services;

require_once(dirname(__FILE__) . '/../../../framework/3rd/wxpay/lib/WxPay.Data.php');
require_once(dirname(__FILE__) . '/../../../framework/3rd/wxpay/lib/WxPay.Api.php');
require_once(dirname(__FILE__) . '/../../../framework/3rd/wxpay/lib/WxPay.Config.php');
require_once(dirname(__FILE__) . '/../../../framework/3rd/wxpay/unit/WxPay.JsApiPay.php');

use app\modules\api\repositories\WeixinLogRepository;
use app\modules\wechat\repositories\AccountRepository;
use app\framework\weixin\DbAccessTokenRepository;
use WxPayRefundQuery;
use WxPayUnifiedOrder;
use WxPayConfig;
use WxPayApi;
use WxPayOrderQuery;
use app\framework\utils\WebUtility;

/**
 * 微信支付服务
 *
 * @author Zengsy
 */
class WeixinPayService
{

    private $_accountRepository;
    private $_accessTokenRepository;

    public function __construct(AccountRepository $accountRepository, DbAccessTokenRepository $accessTokenRepository)
    {
        $this->_accountRepository = $accountRepository;
        $this->_accessTokenRepository = $accessTokenRepository;
    }

    /**
     * 根据微信配置来设置支付参数
     * @param string $accountId 公众号id
     */
    private function setWxPayConfig($accountId, $app_encrypt = "", $ssl_cert_key = '')
    {
        $wxInfo = $this->getWxInfo($accountId);
        if (!empty($app_encrypt)) {
            $app_encrypt = base64_decode($app_encrypt);
            $halfKey = substr($wxInfo->mchKey, 0, 16);
            $appInfo = \Yii::$app->getSecurity()->decryptByPassword($app_encrypt, $halfKey);
            $appArr = explode(',', $appInfo);
            if (count($appArr) > 0) {
                WxPayConfig::$APPID = $appArr[0];
                WxPayConfig::$APPSECRET = $appArr[1];
                WxPayConfig::$MCHID = $appArr[2];
                WxPayConfig::$KEY = $appArr[3];
                $appArr[4] && WxPayConfig::$SUBMCHID = $appArr[4];
            }

            $ssl_cert_key_arr = explode(',', $ssl_cert_key);
            if (!empty($ssl_cert_key_arr)) {
                WxPayConfig::$SSLCERT_CONTENT = $this->_decodeSSlString($ssl_cert_key_arr[0], WxPayConfig::$KEY);
                WxPayConfig::$SSLKEY_CONTENT = $this->_decodeSSlString($ssl_cert_key_arr[1], WxPayConfig::$KEY);
            }

        } else {
            WxPayConfig::$APPID = $wxInfo->appId;
            WxPayConfig::$APPSECRET = $wxInfo->appSecret;
            WxPayConfig::$MCHID = $wxInfo->mchId;
            WxPayConfig::$KEY = $wxInfo->mchKey;

            WxPayConfig::$SSLCERT_CONTENT = $this->_decodeSSlString($wxInfo->mchSSLCert, $wxInfo->mchKey);
            WxPayConfig::$SSLKEY_CONTENT = $this->_decodeSSlString($wxInfo->mchSSLKey, $wxInfo->mchKey);
        }
    }

    /**
     * 获取微信信息
     * @param type $accountId
     * @return type
     */
    private function getWxInfo($accountId)
    {
        $wechat = $this->_accountRepository->getWeChatOriginalId($accountId);

        return $this->_accessTokenRepository->getWxInvoker($wechat);
    }

    /**
     * 验证签名算法
     * @param string $accountId 公众号id
     * @param string $sign 签名数据
     * @param array $params 待校验的参数数组
     * @return boolean 是否校验成功
     */
    public function checkSign($accountId, $sign, $params)
    {

        $wxInfo = $this->getWxInfo($accountId);

        $halfKey = substr($wxInfo->mchKey, 0, 16);

        $params["key"] = $halfKey;

        $keyNames = array_keys($params);

        sort($keyNames);

        $sortedHash = [];

        foreach ($keyNames as $keyName) {
            array_push($sortedHash, "{$keyName}={$params[$keyName]}");
        }

        $rawString = join("&", $sortedHash);

        $encodeString = strtoupper(md5($rawString));

        return $encodeString == $sign;
    }

    /**
     * 微信支付统一下单
     * @param string $accountId 公众号id
     * @param string $openId 微信用户的openid
     * @param string $out_trade_no 商户业务订单号，唯一，最长32位，可设置为不带横杠的guid
     * @param int $total_fee 支付总金额，单位为分
     * @param string $body 商品描述
     * @param string $notify_url 微信成功通知url，必须为绝对地址
     * @param $spbill_create_ip
     * @param type|string $time_expire 订单失效时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010。时区为GMT+8 beijing。该时间取自商户服务器
     * @param string $app_encrypt
     * @param array $wxPayApiLog 支付日志
     * @return array 微信下单结果
     * @throws \WxPayException
     */
    public function pay($accountId, $openId, $out_trade_no, $total_fee, $body, $notify_url, $spbill_create_ip, $time_expire = "", $app_encrypt = "", &$wxPayApiLog = [])
    {
        $this->setWxPayConfig($accountId, $app_encrypt);
        $order = new WxPayUnifiedOrder();
        $order->SetOut_trade_no($out_trade_no);
        $order->SetTotal_fee($total_fee);
        if (!empty($time_expire)) {
            $order->SetTime_expire($time_expire);
        }
        $order->SetSpbill_create_ip($spbill_create_ip);

        $order->MakeSign();

        $notify_params = [
            'account_id' => $accountId,
            'open_id' => $openId,
            'out_trade_no' => $out_trade_no,
            'total_fee' => $total_fee,
            'notify_url' => $notify_url,
            'app_id' => WxPayConfig::$APPID,
            'mch_id' => WxPayConfig::$MCHID,
            'trade_type' => 'JSAPI'
        ];

        $order->SetNotify_url(WebUtility::buildQueryUrl($notify_url, [
            "order_no" => $out_trade_no, 'notify_sign' => $this->_genNotifySign($notify_params, WxPayConfig::$KEY)
        ]));
        $order->SetTrade_type("JSAPI");
        $order->SetOpenid($openId);
        $order->SetBody($body);

        $wxPayApiLog = array_merge($wxPayApiLog, [
            'appid'=>WxPayConfig::$APPID,
            'app_secret'=>WxPayConfig::$APPSECRET,
            'mch_id'=>WxPayConfig::$MCHID,
            'mch_key'=>WxPayConfig::$KEY,
        ]);

        $unorder = WxPayApi::unifiedOrder($order, 6, $wxPayApiLog);
        if ($unorder["return_code"] != "SUCCESS") {
            \yii::error("支付参数 " . json_encode($order->GetValues()) . "\n\n" . "支付结果：" . json_encode($unorder));
            return [
                "success"=> false,
                "msg" => isset($unorder["err_code_des"])?$unorder["err_code_des"]:"",
                "order" => $unorder
            ];
        }

        return [
            "success" => true,
            "msg" => "",
            "order" => $unorder
        ];
    }

    public function getJsApiParameters($order)
    {
        $tools = new \JsApiPay();
        return $tools->GetJsApiParameters($order);
    }

    /**
     * 查询支付订单
     * @param string $accountId 公众号id
     * @param string $out_trade_no 商户业务订单号
     * @return array 订单结果
     */
    public function queryOrder($accountId, $out_trade_no, $app_encrypt = '')
    {
        $this->setWxPayConfig($accountId, $app_encrypt);

        $input = new WxPayOrderQuery();
        $input->SetOut_trade_no($out_trade_no);

        return WxPayApi::orderQuery($input);
    }

    /**
     * 获取用户地址js-sdk签名包
     * @param string $accountId 公众号id
     * @param string $accessToken 网页授权access_token
     * @param string $url 调用js-sdk的url
     * @return array
     */
    public function getAddressSignPackage($accountId, $accessToken, $url, $app_encrypt = '')
    {
        $this->setWxPayConfig($accountId, $app_encrypt); //配置了自定义支付参数时取其配置值，否则取主公众号配置值
        $appId = $this->getAppId($accountId);
        $timestamp = time();
        $nonceStr = \app\framework\utils\StringHelper::getNonceStr(16);
        $string = "accesstoken=$accessToken&appid=$appId&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature = sha1($string);
        $signPackage = [
            "appId" => $appId,
            "nonceStr" => $nonceStr,
            "timeStamp" => (string)$timestamp,
            "url" => $url,
            "addrSign" => $signature
        ];
        return $signPackage;
    }

    private function getAppId($accountId)
    {
        if (!empty(WxPayConfig::$APPID)) {
            return WxPayConfig::$APPID;
        }
        $wechat = $this->_accountRepository->getWeChatOriginalId($accountId);
        $wxInfo = $this->_accessTokenRepository->getWxInvoker($wechat);
        return $wxInfo->appId;
    }

    /**
     * 退款接口
     * @param $account_id
     * @param $out_trade_no
     * @param $total_fee
     * @param $refund_fee
     * @param $isCustomerAccount
     * @param $app_encrypt
     * @param $ssl_cert_key
     * @param array $wxPayApiLog
     * @return array
     * @throws \Exception
     * @throws \WxPayException
     */
    public function refund($account_id, $out_trade_no, $total_fee, $refund_fee, $isCustomerAccount, $app_encrypt, $ssl_cert_key, &$wxPayApiLog = [])
    {
        $this->setWxPayConfig($account_id, $app_encrypt, $ssl_cert_key);
        $input = new \WxPayRefund();
        $input->SetOut_trade_no($out_trade_no);
        $input->SetTotal_fee(intval($total_fee));
        $input->SetRefund_fee(intval($refund_fee));
        $input->SetOut_refund_no($out_trade_no);
        $input->SetOp_user_id(WxPayConfig::$MCHID);

        $wxPayApiLog = array_merge($wxPayApiLog, [
            'appid'=>WxPayConfig::$APPID,
            'app_secret'=>WxPayConfig::$APPSECRET,
            'mch_id'=>WxPayConfig::$MCHID,
            'mch_key'=>WxPayConfig::$KEY,
        ]);

        $dirCert = dirname(getcwd()) . "/protected/cert/";
        if (!file_exists($dirCert)) {
            mkdir($dirCert, 0700);
        }
        // 创建证书存放目录
        if ($isCustomerAccount) {
            // 获取支付证书
            if (WxPayConfig::$SSLCERT_CONTENT === false || WxPayConfig::$SSLKEY_CONTENT === false) {
                throw new \WxPayException("获取支付证书内容异常");
            }

            $certFile = $dirCert . WxPayConfig::$MCHID . "_wxpay_cert.pem";
            $keyFile = $dirCert . WxPayConfig::$MCHID . "_wxpay_key.pem";
        } else {
            $certFile = $dirCert . WxPayConfig::SSLCERT_PATH;
            $keyFile = $dirCert . WxPayConfig::SSLKEY_PATH;
        }
        WxPayConfig::$SELF_SSLCERT_PATH = $certFile;
        WxPayConfig::$SELF_SSLKEY_PATH = $keyFile;

        if (empty(WxPayConfig::$SSLCERT_CONTENT) || empty(WxPayConfig::$SSLKEY_CONTENT)) {
            throw new \Exception("Api证书 密钥为空");
        }

        //去掉了缓存
        file_put_contents($certFile, WxPayConfig::$SSLCERT_CONTENT, LOCK_EX);
        file_put_contents($keyFile, WxPayConfig::$SSLKEY_CONTENT, LOCK_EX);

        $result = WxPayApi::refund($input, 6, $wxPayApiLog);
        if ($result["result_code"] != "SUCCESS") {
            \yii::error("api-key:" . WxPayConfig::$KEY . " 退款参数 " . json_encode($input->GetValues()) . "\n\n" . "退款结果：" . json_encode($result));
            return [
                'success' => false,
                'msg' => $result["err_code_des"]
            ];
        }

        return [
            'success' => true,
            'transaction_id' => $result['transaction_id'],//微信订单号
            'out_trade_no' => $result['out_trade_no'],//商户订单号
            'out_refund_no' => $result['out_refund_no'],//商户退款单号
            'refund_id' => $result['refund_id']//微信退款单号
        ];
    }

    public function queryRefund($accountId, $transaction_id, $app_encrypt = '')
    {
        $this->setWxPayConfig($accountId, $app_encrypt);

        $input = new WxPayRefundQuery();
        $input->SetTransaction_id($transaction_id);

        return WxPayApi::refundQuery($input);
    }

    private function _decodeSSlString($content, $mchKey)
    {
        $content = base64_decode($content);
        if ($content === false) {
            return false;
        }
        $encryKey = substr($mchKey, 0, 16);
        return \Yii::$app->getSecurity()->decryptByKey($content, $encryKey);
    }

    private function _genNotifySign($params, $key)
    {
        unset($params['sign']);
        $halfKey = substr($key, 0, 16);

        $params["key"] = $halfKey;

        $keyNames = array_keys($params);

        sort($keyNames);

        $sortedHash = [];

        foreach ($keyNames as $keyName) {
            array_push($sortedHash, "{$keyName}={$params[$keyName]}");
        }

        $rawString = join("&", $sortedHash);

        $encodeString = strtoupper(md5($rawString));

        return $encodeString;
    }

    public function writeWxPayLog($data)
    {
        $logReps = new WeixinLogRepository();
        return $logReps->insertPayLog($data);
    }
}

<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\api\controllers;

use app\framework\webService\Exceptions\NotFoundException;
use app\modules\api\services\WeixinPayService;
use app\framework\utils\WebUtility;
use app\modules\api\services\WeixinJsSdkService;
use app\framework\db\EntityBase;

/**
 * 微信调用Url
 *
 * @author Chenxy
 */
class WeixinController extends \app\framework\web\extension\Controller
{

    private $_public_id;
    private $_weixinPayService;
    private $_weixinJsSdkService;
   const PUBLIC_QUERY_STRING_KEY = 'public_id';
    public function __construct($id, $module, WeixinPayService $weixinPayService, WeixinJsSdkService $weixinJsSdkService, $config = [])
    {
        $this->_weixinPayService = $weixinPayService;
        $this->_weixinJsSdkService = $weixinJsSdkService;

        $tenantReader = \Yii::$container->get('app\framework\biz\tenant\TenantReaderInterface');
        if (!isset($tenantReader)) {
            throw new \Exception('未注入app\framework\biz\tenant\TenantReaderInterface实例');
        }
        // 读取企业代码和id
         $this->_public_id = isset($_GET[static::PUBLIC_QUERY_STRING_KEY]) ? $_GET[static::PUBLIC_QUERY_STRING_KEY] : ''; 
        parent::__construct($id, $module, $config);
    }

    /**
     * 接收微信消息处理器（开发模式明文消息）
     */
    public function actionHandle()
    {
        try { 
            $data = ['publicid' => $this->_public_id, 'publicDbConn' => EntityBase::getDb()];
            $eventHandler = new \app\modules\api\services\WeixinEventHandler($data);
            $msgHandler = new \app\modules\api\services\WeixinMessageHandler($data);
            $processor = new \app\framework\weixin\msg\MessageProcessor();
            // 安装业务处理器
            $processor->install($eventHandler)->install($msgHandler);
            $app = \app\framework\weixin\msg\MessageServer::getApp($processor);
            // 注册非法请求拦截程序
            $app->regist('\app\modules\api\services\FilterInvalidAppIdModule');
            // 注册消息分发程序
            $app->regist('\app\modules\api\services\WechatMsgForwardModule');
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
     * 站点可访问测试
     */
    public function actionTest()
    {
        $uri = $_SERVER['REQUEST_URI'];
        echo 'OK[' . $uri . ']';
    }

    /**
     * 微信支付通用接口
     * @param $sign
     * @param $account_id
     * @param $out_trade_no
     * @param $total_fee
     * @param $body
     * @param $notify_url
     * @param $return_url
     * @param $showwxpaytitle
     * @param string $time_expire
     * @param string $app_encrypt
     * @return string|void
     * @throws \Exception
     */
    public function actionPay($sign, $account_id, $out_trade_no, $total_fee, $body, $notify_url, $return_url, $showwxpaytitle, $time_expire = "", $app_encrypt = '')
    {
        $openid = $this->request->get("openid");

        //记录支付日志
        $wxPayApiLog = [
            'trade_no'=>$out_trade_no,
            'openid'=>$openid,
            'pay_time'=>date('Y-m-d H:i:s'),
            'total_fee'=>$total_fee,
            'pay_type'=>'付款',
            'status'=>'成功',
        ];

        $signArr = [
            'account_id' => $account_id,
            'body' => $body,
            'notify_url' => $notify_url,
            'out_trade_no' => $out_trade_no,
            'return_url' => $return_url,
            'time_expire' => $time_expire,
            'total_fee' => $total_fee,
            'showwxpaytitle' => $showwxpaytitle,
            'app_encrypt' => $app_encrypt
        ];
        $checkResult = $this->_weixinPayService->checkSign($account_id, $sign, $signArr);

        if (!$checkResult) {
            try {
                $this->notFound();
            } catch (\Exception $e) {
                $wxPayApiLog['original_request_xml'] = print_r($signArr, true);
                $wxPayApiLog['error_msg'] = $e->getMessage();
                $wxPayApiLog['status'] = '失败';
                $this->_weixinPayService->writeWxPayLog($wxPayApiLog);
                throw new \Exception($e->getMessage());
            }
        }

        if (empty($openid)) {
            $red_url = $this->createUrl("/auth/openid", [ 
                "public_id" => $account_id,
                "app_encrypt" => $app_encrypt,
                "return_url" => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
            ], false);
            $this->redirect($red_url);
        }



        try {
            $orderInfo = $this->_weixinPayService->pay($account_id, $openid, $out_trade_no, $total_fee, $body, $notify_url, WebUtility::getClientIP(), $time_expire, $app_encrypt, $wxPayApiLog);
            if ($orderInfo["success"]) {
                $wxPayApiLog['status'] = '成功';
                $jsPara = $this->_weixinPayService->getJsApiParameters($orderInfo["order"]);
            } else {
                $wxPayApiLog['status'] = '失败';
                $jsPara = null;
            }

            $ret_url = WebUtility::buildQueryUrl($return_url, [
                "out_trade_no" => $out_trade_no,
            ]);
        } catch (\Exception $e) {
            $wxPayApiLog['error_msg'] = $e->getMessage();
            $wxPayApiLog['status'] = '失败';
            throw new \Exception($e->getMessage());
        } finally {
            //记录支付日志
            $this->_weixinPayService->writeWxPayLog($wxPayApiLog);
        }

        return $this->renderPartial('pay', [
            "jsApiParameters" => $jsPara,
            "ret_url" => $ret_url,
            "success" => $orderInfo["success"],
            "msg" => $orderInfo["msg"]
        ]);
    }

    /**
     * 微信支付订单查询
     * @param $sign
     * @param $account_id
     * @param $out_trade_no
     * @param string $app_encrypt
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionQueryorder($sign, $account_id, $out_trade_no, $app_encrypt = '')
    {
        $checkResult = $this->_weixinPayService->checkSign($account_id, $sign, [
            'account_id' => $account_id,
            'out_trade_no' => $out_trade_no,
            'app_encrypt' => $app_encrypt
        ]);

        if (!$checkResult) {
            return $this->notFound();
        }

        $order = $this->_weixinPayService->queryOrder($account_id, $out_trade_no, $app_encrypt);
        return $this->json($order);
    }

    /**
     * 获取jssdk签名包，用于页面js-sdk调用
     * @param type $accountId 公众号id
     * @param type $url 需要调用js-sdk的页面地址，必须用户访问页面的绝对地址，包括查询参数，但是不包括＃及其后面的部分
     * @return array jssdk签名包
     */
    public function actionJssdksign($accountId='39d87f3e-141f-9e5c-7ab2-6848a8953b5e', $url='http://localhost:9212/home/home/index?public_id=39d87f3e-141f-9e5c-7ab2-6848a8953b5e&openid=1111')
    {
        $package = $this->_weixinJsSdkService->getSignPackage($accountId, $url); 
        return $this->json($package);
    }

    /**
     * 获取用户地址签名包，用于页面js-sdk调用
     * @param $account_id
     * @param string $return_uri
     * @param string $app_encrypt
     * @return string
     */
    public function actionAddress($account_id, $return_uri = "", $app_encrypt = '')
    {

        try {
            $openid = $this->request->get("openid");
            $accessToken = $this->request->get("access_token");

            if (empty($openid) || empty($accessToken)) {
                $red_url = $this->createUrl("/auth/openid", [ 
                    'public_id' => $account_id,
                    'return_url' => $this->request->absoluteUrl,
                    'app_encrypt' => $app_encrypt
                ], false);

                $this->redirect($red_url);
            }

            $package = $this->_weixinPayService->getAddressSignPackage($account_id, $accessToken, $this->request->absoluteUrl, $app_encrypt);
            $package['scope'] = 'jsapi_address';
            $package['signType'] = 'sha1';

            return $this->renderPartial('address', [
                'jsApiParameters' => json_encode($package),
                'ret_url' => $return_uri,
                'success' => 'success',
                'msg' => 'ok'
            ]);
        } catch (\Exception $ex) {
            return $this->renderPartial('address', ['msg' => $ex->getMessage()]);
        }
    }

    /**
     * 退款
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionRefund(  )
    {
        $sign = $this->request->post('sign');
        $account_id = $this->request->post('account_id');
        $out_trade_no = $this->request->post('out_trade_no');
        $total_fee = $this->request->post('total_fee');
        $refund_fee = $this->request->post('refund_fee');
        $isCustomerAccount = $this->request->post('isCustomerAccount');
        $app_encrypt = $this->request->post('app_encrypt');
        $ssl_cert_key = $this->request->post('ssl_cert_key'); 

        //记录支付日志
        $wxPayApiLog = [
            'trade_no'=>$out_trade_no,
            'openid'=>'',
            'pay_time'=>date('Y-m-d H:i:s'),
            'total_fee'=>$refund_fee,
            'pay_type'=>'退款',
            'status'=>'成功',
        ];

        try {
            $signArr = [
                'account_id' => $account_id,
                'out_trade_no' => $out_trade_no,
                'total_fee' => $total_fee,
                'refund_fee' => $refund_fee,
                'app_encrypt' => $app_encrypt,
                'isCustomerAccount' => $isCustomerAccount,
                'ssl_cert_key' => $ssl_cert_key
            ]; 

            $checkResult = $this->_weixinPayService->checkSign($account_id, $sign, $signArr);

            if (!$checkResult) {
                $wxPayApiLog['original_request_xml'] = print_r($signArr, true);
                $this->notFound();
            }
            $refundInfo = $this->_weixinPayService->refund($account_id, $out_trade_no,$total_fee, $refund_fee, $isCustomerAccount, $app_encrypt, $ssl_cert_key, $wxPayApiLog);
            $wxPayApiLog['status'] = $refundInfo['success'] ? '成功' : '失败';
        } catch (\WxPayException $wex) {
            \yii::error($wex);
            $wxPayApiLog['error_msg'] = $wex->getMessage();
            $wxPayApiLog['status'] = '失败';
            return $this->json(['success' => false, 'msg' => '退款失败']);
        } catch (NotFoundException $nex) {
            $wxPayApiLog['error_msg'] = $nex->getMessage();
            $wxPayApiLog['status'] = '失败';
            \yii::error($nex);
            return $this->json(['success' => false, 'msg' => $nex->getMessage()]);
        } catch (\Exception $ex) {
            $wxPayApiLog['error_msg'] = $ex->getMessage();
            $wxPayApiLog['status'] = '失败';
            \yii::error($ex);
            return $this->json(['success' => false, 'msg' => $ex->getMessage()]);
        } finally {

            $this->_weixinPayService->writeWxPayLog($wxPayApiLog);
        }

        return $this->json($refundInfo);
    }

    /**
     * 微信退款查询
     * @param $sign
     * @param $account_id
     * @param $transaction_id
     * @param string $app_encrypt
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionQueryRefund($sign, $account_id, $transaction_id, $app_encrypt)
    {
        $checkResult = $this->_weixinPayService->checkSign($account_id, $sign, [
            'account_id' => $account_id,
            'transaction_id' => $transaction_id,
            'app_encrypt' => $app_encrypt
        ]);

        if (!$checkResult) {
            return $this->notFound();
        }

        $order = $this->_weixinPayService->queryRefund($account_id, $transaction_id, $app_encrypt);
        return $this->json($order);
    }
}

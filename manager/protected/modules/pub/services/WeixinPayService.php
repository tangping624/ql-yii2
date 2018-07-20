<?php
namespace app\modules\pub\services;
use app\modules\ServiceBase;
use \app\framework\webService\RestClientHelper;
use app\framework\biz\cache\OAuth2CacheManager;
use app\modules\api\services\PublicAccountService;


class WeixinPayService extends ServiceBase{
      private $_publicAccountService;

    public function __construct( PublicAccountService $publicAccountService)
    {
        $this->_publicAccountService = $publicAccountService; 
    }
     /**
     * 获取签名
     * @param array $params 待校验的参数数组
     * @return boolean 是否校验成功
     */
    public function getSign($params,$public_id)
    {
        $halfKey = $this->getPayKey($public_id);// substr($this->_pay_key, 0, 16);
        if(empty($halfKey)){
            throw new \InvalidArgumentException('微信支付接口KEY失败！');
        }
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
 
//    /**
//     * 获取接口的access_token
//     * @return string
//     */
//    public function getAccessToken($public_id='')
//    {
//        try {
//            $path = "/oauth2/access_token";
//            $params = [
//                "grant_type" => "client_credentials",
//                "client_id" => APP_NAME,
//                "client_secret" => OAuth2CacheManager::getSecretByAppId(APP_NAME)
//            ];
//            $params = http_build_query($params);
//            $fullpath= $this->getManageCenterSite().$path;
//            $restClientHelper =new RestClientHelper();
//            $key = $path . '#' . $params;
//            if (!$result = \YII::$app->cache->get($key)) {
//                $result =$restClientHelper->invoke($fullpath, $params, 'POST');
//                if (isset($result->errcode) && $result->errcode) {
//                    throw new \Exception('error:'.$result->errcode.', '.$result->errmsg);
//                }
//                if (is_object($result) && $result->access_token && isset($result->expires_in)) {
//                    \Yii::$app->cache->set($key, $result, $result->expires_in);
//                }
//            }
//            return $result->access_token;
//        } catch (\Exception $ex) {
//            throw $ex;
//        }
//    }
   /*
     *  获取微信支付接口KEY
     */
    public function getPayKey($public_id){  
        if (empty($public_id)) {
            throw new \InvalidArgumentException('$public_id');
        }  
        try {
            $mch =  $this->_publicAccountService->getMch($public_id);
            if($mch == false){
                throw new \Exception('获取公众号商户密钥失败！');
            }
            if (empty($mch['mch_half_key']) || strlen($mch['mch_half_key']) != 16) {
                throw new \Exception('获取公众号商户密钥失败！');
            } 
            return $mch['mch_half_key'];
        } catch (\Exception $ex) {
            \Yii::error($ex->getMessage());
            throw $ex;
        } 
    }
     /**
     * 生成用来支付的链接
     * @param string $orderNo 订单编号
     * @param float $fee 支付金额
     * @param $description
     * @param string $return_url 完整url
     * @param string $notify_url 完整url
     * @return string 用来支付的链接
     */
    public function generatePayUrl($orderNo, $fee, $description, $return_url, $notify_url,$public_id,$openid)
    {
        if (empty($orderNo)) {
            throw new \InvalidArgumentException('orderNo 不能为空');
        }

        if (strlen($orderNo) > 32) {
            throw new \InvalidArgumentException('订单号长度不能大于 32');
        }

        $fee = (float) $fee;
        if ($fee <= 0) {
            throw new \InvalidArgumentException('orderNo$fee 无效的货币值');
        }

        if (empty($return_url)) {
            throw new \InvalidArgumentException('$return_url');
        }

        if (empty($notify_url)) {
            throw new \InvalidArgumentException('$notify_url');
        }
       if (empty($openid)) {
            throw new \InvalidArgumentException('$openid');
        }
        //$app_encrypt = $this->_getAppEncrypt($merchantInfo);

        $fee_fen = ($fee * 100); //转化成分
        $params = [
            'account_id' => $public_id,
            'body' => $description,
            'notify_url' => $notify_url,
            'out_trade_no' => $orderNo,
            'return_url' => $return_url,
            'total_fee' => $fee_fen,
            'time_expire' => '',
            'showwxpaytitle' => 1,
            'app_encrypt' =>''// $app_encrypt
        ];
        $sign = $this->getSign($params,$public_id);
        $params = http_build_query(array_merge($params, ['sign' => $sign,'openid'=>$openid]));

        $url = $this->getManageCenterSite()."/api/weixin/pay/?$params";
        return $url;
    } 
    /*
     * 获取订单支付信息
     */
    public function queryWxOrderResult($wxpaylogid,$public_id){
         if (empty($public_id)) {
            throw new \InvalidArgumentException('$public_id');
        } 
        $params = [
            'out_trade_no' => $wxpaylogid,
            'account_id' => $public_id,
            'app_encrypt'=>''
        ]; 
        
        $sign = $this->getSign($params,$public_id); 
        $params = http_build_query(array_merge($params, ['sign' => $sign]));

        $client = new RestClientHelper( $this->getManageCenterSite());
        $result = $client->invoke("/api/weixin/queryorder/?" . $params, [], 'GET');  
        $res = $this->getResult($result);  
        if ($res->result_code == 'SUCCESS') {
            return $res;
        }
        return null;
    }
    /**
     * 处理接口返回值
     * @param type $data
     * @return string
     */
    protected function getResult($data)
    {
        $result = is_string($data) ? json_decode($data) : $data;
        if (isset($result->errcode) && $result->errcode != 0) {
            //之前代码直接抛出异常导致 http 500 错误
            \Yii::error($data->errmsg);
            return '';
        }

        return $result;
    }
}

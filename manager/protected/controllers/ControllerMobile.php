<?php

namespace app\controllers;

use app\framework\web\extension\MobileController;
use app\framework\utils\WebUtility;

/**
 * @property \app\framework\web\extension\MobileContext $context This property is read-only.
 * @package app\controllers
 */
abstract class ControllerMobile extends MobileController
{
    public $enableCsrfValidation = false;

    public function __construct($id, $module, $config = [])
    {
        //覆盖inject
        require_once __DIR__ . '/../boot/inject_weixin.php';
        \Yii::$app->set('context', new \app\framework\web\extension\MobileContext());
        parent::__construct($id, $module, $config);
    }
    
    public function getContext()
    {
        $context = \Yii::$app->context;
        return $context;
    }

    private function getRequestHeaders() {
        $headers = array();
        foreach($_SERVER as $key => $value) {
            if(strpos($key, 'HTTP_') === 0) {
                $headers[str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))))] = $value;
            }
        }
        return $headers;
    }
    
    private function getRequestParamter($paramName)
    {
        if (array_key_exists($paramName, $_REQUEST)) {
            return $_REQUEST[$paramName];
        }
        
        return null;
    }
    
    public function buildUrl($urlPath, $params = [])
    {
        $protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === false ? 'http' : 'https';
        $host = $_SERVER['HTTP_HOST'];
        $url = "{$protocol}://{$host}{$urlPath}" . http_build_query($params);
        return $url;
    }    
    
    public function getJssdksign($url)
    {
        if (empty($url)) {
            throw new \InvalidArgumentException("url参数无效");
        }
        $context = $this->context;
        $signConfig = $this->getJssdksignConfig( $context->publicId, urldecode($url));
        return $signConfig;
    }

    public function getJssdksignConfig( $accountId, $url)
    {
        $invokeUri = WebUtility::createBeautifiedUrl('wxapi/weixin/jssdksign');
        $restClient = new \app\framework\webService\RestClientHelper();
        \Yii::trace('call api: ' . $invokeUri);
        $signConfig = $restClient->invoke($invokeUri, ['accountId'=>$accountId, 'url'=>$url], 'GET');
        return $signConfig;
    }
    
    public function actionGetJssdksign()
    {
        $url = $this->request->post('signurl');
        $signConfig = $this->getJssdksign($url);
        return $this->json(['wx' => $signConfig]);
    }
    
    /**
     * 友好输出表单验证提示信息
     * @param type $message
     * @return string
     */
    public function showValidateMsg($message)
    {
        $msg = '';
        foreach ($message as $value) {
            $msg .= $value.'; ';
        }
        return $msg;
    }
    
    /**
     * 写缓存
     * @param type $key
     * @param type $value
     * @param type $duration
     * @return type
     */
    public function setCache($key, $value, $duration=null)
    {
        $duration = empty($duration) ? 86400*30 : $duration;
        return \Yii::$app->cache->set($key, json_encode($value), $duration);
    }
    
    /**
     * 读缓存数据
     * @param type $key
     * @return type
     */
    public function getCache($key)
    {
        $json = \Yii::$app->cache->get($key);
        return json_decode($json, true);
    }
}

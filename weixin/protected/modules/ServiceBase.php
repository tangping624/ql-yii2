<?php

namespace app\modules;
use yii;

/**
 * 所有模块的Service需继承此类，公共的控制在此处实现
 */
abstract class ServiceBase
{
    /**
     * 通过Yii::$container创建对象实例
     * @param $classFullName
     * @throws \yii\base\InvalidConfigException
     */
    protected function getInstance($classFullName)
    {
        \Yii::$container->get($classFullName);
    }

    public function getJssdksign( $accountId, $url)
    {
         $publicAccountUrl = $this->_getManageSiteUrl();
        if ($publicAccountUrl == false || $publicAccountUrl == '') {
            throw new \Exception('未找到manage_site在应用的url');
        }
        $invokeUri =$publicAccountUrl. "/index.php?r=api/weixin/jssdksign";
        $restClient = new \app\framework\webService\RestClientHelper();
        \Yii::trace('call api: ' . $invokeUri);
        $signConfig = $restClient->invoke($invokeUri, ['accountId'=>$accountId, 'url'=>$url], 'GET');
        return $signConfig;
    }
    
    public function getManageCenterSite()
    {
        return $this->_getManageSiteUrl();
    }
      /**
     * @return bool|null|string
     */
    private function _getManageSiteUrl()
    { 
        $settingAccessor = Yii::$container->get('app\framework\settings\interfaces\SettingsAccessorInterface');
        $config = $settingAccessor->get('manage_site');
        if (!isset($config)) {
            throw new \Exception('缺少配置项 manage_site');
        } 
        return $config;
    }
}

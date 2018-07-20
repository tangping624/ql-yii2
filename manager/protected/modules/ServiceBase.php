<?php
namespace app\modules;
use Yii;
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
     public function getWeiXinSite()
    {
        return $this->_getWeiXinSiteUrl();
    }
      private function _getWeiXinSiteUrl()
    { 
        $settingAccessor = Yii::$container->get('app\framework\settings\interfaces\SettingsAccessorInterface');
        $config = $settingAccessor->get('weixin_site');
        if (!isset($config)) {
            throw new \Exception('缺少配置项 weixin_site');
        } 
        return $config;
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

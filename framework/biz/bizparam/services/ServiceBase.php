<?php
 

namespace app\framework\biz\bizparam\services;

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
}

<?php
use app\framework\cache\CachePackageManger;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/7
 * Time: 9:42
 */

class Repository {

    public function getConfigData()
    {
        //todo
        $cacheKey=null;
        $cacheObject = CachePackageManger::instance($cacheKey);
        $data = $cacheObject->get();
        if (!isset($data)) {
            $data = T_parameter_value::find()->all();
            $cacheObject->set($cacheKey);
        }
        return $data;
    }
}
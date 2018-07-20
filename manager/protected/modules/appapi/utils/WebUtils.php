<?php
/**
 * Created by PhpStorm.
 * User: FUYL
 * Date: 2015/5/13
 * Time: 16:38
 */

namespace app\modules\appapi\utils;

class WebUtils{
    /**
     * 是否提供请求参数
     * @param $param_name
     */
    public static function IsRequestParam($param_name){
        if(!isset($_REQUEST[$param_name])){
            return false;
        }

        if(strlen($_REQUEST[$param_name]) == 0) {
            return false;
        }

        return true;

    }
}

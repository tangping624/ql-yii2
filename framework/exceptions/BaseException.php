<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\framework\exceptions;

/**
 * Description of BaseException
 *
 * @author Chenxy
 */
class BaseException extends \Exception
{
    /**
     * 转换成标准的错误格式： {"errcode":40013,"errmsg":"invalid appid"}
     * @return type
     */
    public function toJson()
    {
        $errData = ['errcode' => $this->code, 'errmsg' => $this->message];
        return json_encode($errData, JSON_UNESCAPED_UNICODE);
    }
}

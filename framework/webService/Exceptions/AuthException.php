<?php
namespace app\framework\webService\Exceptions;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description 授权失败异常。
 *
 * @author likg
 */
class AuthException extends \Exception {
    
    public function __construct() {
        parent::__construct("获取授权失败.", 500);
    }
}

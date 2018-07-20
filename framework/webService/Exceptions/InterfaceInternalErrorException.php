<?php
namespace app\framework\webService\Exceptions;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description 500 接口内部异常
 *
 * @author likg
 */
class InterfaceInternalErrorException extends \Exception {
     public function __construct() {
         parent::__construct("接口内部错误，请查看接口错误日志.", 500);
     }
}

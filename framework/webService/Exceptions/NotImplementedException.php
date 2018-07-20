<?php
namespace app\framework\webService\Exceptions;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description 501未实现异常
 *
 * @author likg
 */
class NotImplementedException extends \Exception {
     public function __construct($url='') {
         parent::__construct("请求的接口{$url}未实现，请检查调用方代码.", 501);
     }
}

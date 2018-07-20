<?php
namespace app\framework\webService\Exceptions;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description 客户端错误
 *
 * @author likg
 */
class BadRequestException extends \Exception {
     public function __construct($url='') {
         parent::__construct("接口访问出现客户端错误，请检查调用方代码.", 400);
     }
}

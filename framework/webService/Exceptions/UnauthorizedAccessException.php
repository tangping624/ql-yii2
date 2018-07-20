<?php
namespace app\framework\webService\Exceptions;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description 接口未授权
 *
 * @author likg
 */
class UnauthorizedAccessException extends \Exception {
     public function __construct($url='') {
         parent::__construct("接口{$url}未授权.", 401);
     }
}

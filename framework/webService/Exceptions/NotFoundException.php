<?php
namespace app\framework\webService\Exceptions;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description 404 页面不存在
 *
 * @author likg
 */
class NotFoundException extends \Exception {
     public function __construct($url='') {
         parent::__construct("请求的接口地址{$url}不存在，请检查调用方代码.", 404);
     }
}

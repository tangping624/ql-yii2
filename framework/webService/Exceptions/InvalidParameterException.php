<?php
namespace app\framework\webService\Exceptions;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description 调用的方法参数错误
 *
 * @author likg
 */
class InvalidParameterException  extends \Exception{
    public function __construct() {
        parent::__construct("请求参数错误.", 400, null);
    }
}

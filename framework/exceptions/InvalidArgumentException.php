<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\framework\exceptions;

/**
 * 参数校验失败异常
 *
 * @author Chenxy
 */
class InvalidArgumentException extends BaseException
{
    public function __construct($previous = null)
    {
        parent::__construct("参数校验失败", 1002, $previous);
    }
}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\framework\exceptions;

/**
 * Description of ApplicationException
 *
 * @author Chenxy
 */
class ApplicationException extends BaseException
{
    public function __construct($previous = null)
    {
        parent::__construct("invalid appid", 40013, $previous);
    }
}

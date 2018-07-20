<?php
namespace app\framework\webService\Exceptions;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ConnectException
 *
 * @author likg
 */
class ConnectException extends \Exception{
    
public function __construct($msg,$code) {
    parent::__construct($msg ,$code, null);
    }
}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\framework\utils\timer;
 
interface ITimelogger
{
    /**
     * 写日志
     * @param string $content
     */
    public function log($content);
    
    /**
     * 输出日志
     */
    public function end();
}

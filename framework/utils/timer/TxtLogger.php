<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\framework\utils\timer; 
class TxtLogger implements ITimeLogger
{
    private $_file = '';
    
    private $_content = '';
    
    public function __construct($file = '')
    {
        $this->_file = $file ?: (\yii::$app->runtimePath . "/logs/timer_" . date('Y-m-d')  . '.log');
    }


    public function log($content)
    {
        $time = date('Y-m-d H:i:s');
        $this->_content .= ("{$time} {$content}\r\n");
        if (strlen($this->_content) >= 10000) {
            $this->end();
        }
    }
    
    public function end()
    {
        if (empty($this->_content)) {
            return;
        }
        
        // 输出到文件
        try {
            $handle = fopen($this->_file, "a+");
            fwrite($handle, $this->_content);
            fclose($handle);
            $this->_content = '';
        } catch (\Exception $ex) {
            \Yii::error($ex);
        }
    }
}


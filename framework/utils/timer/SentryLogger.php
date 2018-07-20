<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\framework\utils\timer;
 
class SentryLogger implements ITimeLogger
{
    private $_content = '';
    
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
        
        // 输出到sentry
        \Yii::error($this->_content);
        $this->_content = '';
    }
}

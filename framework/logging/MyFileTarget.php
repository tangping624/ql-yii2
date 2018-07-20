<?php

namespace app\framework\logging;

use yii\base\InvalidConfigException;
use yii\log\FileTarget;

class MyFileTarget extends FileTarget
{
    public $context = [];
    
    /**
     * Writes log messages to a file.
     * @throws InvalidConfigException if unable to open the log file for writing
     */
    public function export()
    {
        foreach ($this->messages as &$message) {
            $msg = $message[0];
            if(is_object($msg)){
                $errStr = $msg->getMessage();
                $traces = $msg->getTrace();
                $message[0] = $errStr;
                if(isset($message[4])){
                    $message[4] = $traces;
                }
            }
        }

        unset($message); // 最后取消掉引用
        parent::export();
    }

    public function getContext()
    {
        return $this->context;
    }

    public function setContext($context)
    {
        if ($context == null) {
            $context = [];
        }
        $this->context = $context;
    }
}
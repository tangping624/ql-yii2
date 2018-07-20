<?php

namespace app\framework\logging;
use yii\helpers\VarDumper;
use yii\log\Target;

class AliyunTarget extends Target
{
    public function init()
    {
        parent::init();
    }

    /**
     * Exports log [[messages]] to a specific destination.
     * Child classes must implement this method.
     */
    public function export()
    {
        //
    }
}
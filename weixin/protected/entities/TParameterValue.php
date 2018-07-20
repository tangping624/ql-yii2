<?php
/**
 * Created by PhpStorm.
 * User: tx-07
 * Date: 2016/8/4
 * Time: 10:18
 */
namespace app\entities;
use app\framework\db\AppEntity;

class TParameterValue extends AppEntity
{
    protected function getAutoInsertingColumns(){
        return [];
    }

    protected  function getAutoUpdatingColumns(){
        return [];
    }
    public static function tableName()
    {
        return 't_parameter_value';
    }
}
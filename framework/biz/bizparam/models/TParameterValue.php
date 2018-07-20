<?php
/**
 * Created by PhpStorm.
 * User: kongy
 * Date: 2015/4/15
 * Time: 11:06
 */

namespace app\framework\biz\bizparam\models;

use app\framework\db\EntityBase;

class TParameterValue extends EntityBase
{
    public static $level = 1;

    public static function tableName()
    {
        return 't_parameter_value';
    }
}

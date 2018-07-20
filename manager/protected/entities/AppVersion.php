<?php
/**
 * Created by PhpStorm.
 * User: 斌
 * Date: 2015/4/22
 * Time: 16:00
 */

namespace app\entities;
use app\framework\db\EntityBase;

class AppVersion extends PEntityBase{
    public static function tableName()
    {
        return "m_app_version";
    }


}
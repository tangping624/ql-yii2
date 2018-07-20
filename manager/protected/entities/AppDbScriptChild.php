<?php
namespace app\entities;

use app\framework\db\EntityBase;
use app\framework\webService\Exceptions\NotImplementedException;

/**
 * Class AppDbScriptChild
 * @package app\entities
 */
class AppDbScriptChild extends PEntityBase
{
    /**
     * 表名
     * @return string
     */
    public static function tableName()
    {
        return "m_app_db_script_child";
    }


}
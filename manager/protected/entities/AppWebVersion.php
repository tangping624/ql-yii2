<?php
namespace app\entities;

use app\framework\db\EntityBase;

/**
 * Class AppWebVersion
 * @package app\entities
 */
class AppWebVersion extends PEntityBase
{
    /**
     * 表名
     * @return string
     */
    public static function tableName()
    {
        return "m_app_web_version";
    }

}
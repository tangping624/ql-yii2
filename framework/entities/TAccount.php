<?php
namespace app\framework\entities;
use app\framework\db\ConfigEntity;
/**
 * 应用实体
 */
class TAccount extends ConfigEntity
{
    public static function tableName()
    {
        return 't_account';
    }
    
} 

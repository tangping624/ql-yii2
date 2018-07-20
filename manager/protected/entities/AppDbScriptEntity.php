<?php

namespace app\entities;
use app\framework\db\EntityBase;

class AppDbScriptEntity extends PEntityBase{
    public static function tableName()
    {
        return "m_app_db_script";
    }


}
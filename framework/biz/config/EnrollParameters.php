<?php

namespace app\framework\biz\config;


class EnrollParameters
{
    public static $ENROLL_SYSTEM_COLUMNS = ['姓名' => 'name'
                        ,'手机' => 'mobile'
                        ,'证件信息' => 'id_info'];

    public static function getKeys()
    {
        return array_keys(self::$ENROLL_SYSTEM_COLUMNS);
    }

    public static function getValues()
    {
        return array_values(self::$ENROLL_SYSTEM_COLUMNS);
    }

}
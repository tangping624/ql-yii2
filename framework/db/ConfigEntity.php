<?php

namespace app\framework\db;
use Yii;
use yii\db\ActiveRecord;

// 配置库活动记录
abstract class ConfigEntity extends ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->get('db');
    }
}

?>
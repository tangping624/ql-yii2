<?php

namespace app\framework\db;
use Yii;
use app\framework\db\EntityBase;

/**
 * 微网站,具体app所使用的实体
 * Class AppEntity
 */
abstract class AppEntity extends EntityBase
{

    /**
     * @return Connection
     * @throws \yii\base\InvalidConfigException
     */
    public static $connectionPool = [];

    public static function getDb()
    {
 

        return new \yii\db\Connection(\Yii::$app->get('db'));  
    }
 

}

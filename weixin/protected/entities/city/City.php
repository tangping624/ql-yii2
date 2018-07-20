<?php
namespace app\entities\city;

use app\entities\PEntityBase;
/**
 * 城市实体
 */
class City extends PEntityBase
{
    public static function tableName()
    {
        return 'city';
    }
}

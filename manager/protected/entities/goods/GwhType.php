<?php
namespace app\entities\goods;
use app\entities\PEntityBase;
/**
 * 购物惠分类表
 */
class GwhType extends PEntityBase
{
    public static function tableName()
    {
        return 'gwh_type';
    }
}

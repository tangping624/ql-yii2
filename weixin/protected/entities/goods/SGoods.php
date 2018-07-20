<?php
namespace app\entities\goods;
use app\entities\PEntityBase;
/**
 * 商品表
 */
class SGoods extends PEntityBase
{
    public static function tableName()
    {
        return 's_goods';
    }
}

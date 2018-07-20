<?php
namespace app\entities\merchant;
use app\entities\PEntityBase;
class SellerType extends PEntityBase
{
    public static function tableName()
    {
        return 'seller_type';
    }
}

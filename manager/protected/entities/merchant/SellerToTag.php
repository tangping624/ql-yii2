<?php
namespace app\entities\merchant;
use app\entities\PEntityBase;
class SellerToTag extends PEntityBase
{
    public static function tableName()
    {
        return 'seller_to_tag';
    }
}

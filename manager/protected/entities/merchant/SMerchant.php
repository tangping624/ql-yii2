<?php
namespace app\entities\merchant;
use app\entities\PEntityBase;
class SMerchant extends PEntityBase
{
    public static function tableName()
    {
        return 's_merchant';
    }
}

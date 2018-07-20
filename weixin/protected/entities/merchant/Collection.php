<?php
namespace app\entities\merchant;
use app\entities\PEntityBase;
class Collection extends PEntityBase
{
    public static function tableName()
    {
        return 'collection';
    }
}

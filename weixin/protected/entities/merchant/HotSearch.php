<?php
namespace app\entities\merchant;
use app\entities\PEntityBase;
class HotSearch extends PEntityBase
{
    public static function tableName()
    {
        return 'p_hot_search';
    }
}

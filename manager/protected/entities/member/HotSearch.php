<?php
namespace app\entities\member;
use app\entities\PEntityBase;
class HotSearch extends PEntityBase
{
    public static function tableName()
    {
        return 'p_hot_search';
    }
}

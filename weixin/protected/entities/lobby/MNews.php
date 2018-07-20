<?php
namespace app\entities\lobby;
use app\entities\PEntityBase;
/**
 * 新鲜事
 */
class MNews extends PEntityBase
{
    public static function tableName()
    {
        return 'm_news';
    }
}

<?php
namespace app\entities\lobby;

use app\entities\PEntityBase;
/**
 * 会员实体
 */
class MBlog extends PEntityBase
{
    public static function tableName()
    {
        return 'm_blog';
    }
}

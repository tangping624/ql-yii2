<?php
namespace app\entities\baike;

use app\entities\PEntityBase;
/**
 * 会员实体
 */
class MWikiCategory extends PEntityBase
{
    public static function tableName()
    {
        return 'm_wiki_category';
    }
}

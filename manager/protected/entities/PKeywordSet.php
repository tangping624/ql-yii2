<?php
/**
 * 关键字自动回复规则实体
 * User: robert
 * Date: 2015/5/7
 * Time: 14:17
 */
namespace app\entities;

/**
 * 关键字自动回复规则实体
 */
class PKeywordSet extends PEntityBase
{
    public static function tableName()
    {
        return 'p_keyword_set';
    }
}

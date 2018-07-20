<?php
/**
 * 关键字自动回复关键字实体
 * User: robert
 * Date: 2015/5/7
 * Time: 14:17
 */
namespace app\entities;

/**
 * 消息自动回复实体
 */
class PKeyword extends PEntityBase
{
    public static function tableName()
    {
        return 'p_keyword';
    }
}

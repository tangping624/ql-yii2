<?php
/**
 * 消息自动回复实体
 * User: robert
 * Date: 2015/5/7
 * Time: 14:17
 */
namespace app\entities;

/**
 * 消息自动回复实体
 */
class PReply extends PEntityBase
{
    public static function tableName()
    {
        return 'p_reply';
    }
}
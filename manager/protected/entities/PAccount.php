<?php
/**
 * 公众号实体
 * User: robert
 * Date: 2015/5/5
 * Time: 16:09
 */
namespace app\entities;

/**
 * 公众号实体
 */
class PAccount extends PEntityBase
{
    public static function tableName()
    {
        return 'p_account';
    }
}
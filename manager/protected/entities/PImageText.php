<?php
/**
 * 公众号实体
 * User: robert
 * Date: 2015/5/5
 * Time: 16:09
 */
namespace app\entities;

/**
 * 图文实体
 */
class PImageText extends PEntityBase
{
    public static function tableName()
    {
        return 'p_image_text';
    }
}
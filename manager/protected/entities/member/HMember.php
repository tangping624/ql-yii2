<?php
namespace app\entities\member;
use app\entities\PEntityBase;
class HMember extends PEntityBase
{
    public static function tableName()
    {
        return 'h_member';
    }
}

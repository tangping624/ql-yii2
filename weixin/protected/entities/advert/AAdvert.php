<?php
namespace app\entities\advert;
use app\entities\PEntityBase;

class AAdvert extends PEntityBase
{
    public static function tableName()
    {
        return 'a_advert';
    }
}
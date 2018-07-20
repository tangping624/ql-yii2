<?php
/**
 * Created by PhpStorm.
 * User: 斌
 * Date: 2015/4/22
 * Time: 16:03
 */

namespace app\entities;


use app\framework\db\EntityBase;

class AppDownloadUrl extends PEntityBase{
    public static function tableName()
    {
        return "m_app_download_url";
    }


}
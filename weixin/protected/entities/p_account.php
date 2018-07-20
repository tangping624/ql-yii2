<?php
/**
 * Created by PhpStorm.
 * User: tx-07
 * Date: 2016/8/4
 * Time: 10:18
 */
namespace app\entities;
use app\framework\db\AppEntity;

class p_account extends AppEntity
{ 
    public static function tableName()
    {
        return 'p_account';
    }
}
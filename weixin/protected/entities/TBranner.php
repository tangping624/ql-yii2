<?php 
namespace app\entities;
 
use app\framework\db\AppEntity;
class TBranner extends AppEntity
{
    protected function getAutoInsertingColumns(){
        return [];
    }

    protected  function getAutoUpdatingColumns(){
        return [];
    }
    public static function tableName()
    {
        return 't_branner';
    }
} 

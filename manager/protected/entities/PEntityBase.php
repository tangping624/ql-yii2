<?php 
namespace app\entities;

use app\framework\db\EntityBase;

/**
 * 实体基类
 */
class PEntityBase extends EntityBase
{
     protected function getAutoInsertingColumns(){
        return [];
    }

    protected  function getAutoUpdatingColumns(){
        return [];
    }
}

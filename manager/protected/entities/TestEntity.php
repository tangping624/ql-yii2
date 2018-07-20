<?php

namespace app\entities;

use app\framework\db\EntityBase;

class TestEntity extends EntityBase
{
    public static function tableName()
    {
        return "test";
    }

    protected function getAutoInsertingColumns()
    {
        // TODO: Implement getAutoInsertingColumns() method.
    }

    protected function getAutoUpdatingColumns()
    {
        // TODO: Implement getAutoUpdatingColumns() method.
    }
}


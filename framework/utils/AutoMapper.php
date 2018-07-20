<?php

namespace app\framework\utils;


class AutoMapper
{
    public static function entityToModel($entity, $model)
    {
        throw new \Exception('暂不支持该接口');
    }

    /**
     * @param object $entity
     * @param array $row
     * @return object
     */
    public static function RowToEntity($entity, $row)
    {
        if(empty($row)){
            throw new \InvalidArgumentException('$row');
        }

        if(!isset($entity)){
            throw new \InvalidArgumentException('$entity');
        }

        foreach($row as $columnName=>$value)
        {
         $entity->$columnName = $value;
        }

        return $entity;

    }
}

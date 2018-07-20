<?php

namespace app\modules;
 
use app\framework\db\EntityBase;
use yii\base\InvalidCallException;
use yii\base\UnknownPropertyException;
use yii\db\Connection;


/**
 * 所有模块的Repository需继承此类，公共的控制在此处实现
 * @property Connection $tenantDb 租户db connection
 */
abstract class RepositoryBase
{
    /**
     * 插入实体
     * @param EntityBase $entity 实体
     * @return bool 是否插入成功
     */
    public function insert(EntityBase $entity)
    {
        return $entity->save();
    }

    /**
     * 更新实体
     * @param EntityBase $entity 实体
     * @return bool 是否更新成功
     */
    public function update(EntityBase $entity)
    {
        return $entity->save();
    }

    /**
     * 删除实体
     * @param EntityBase $entity 实体
     * @return bool 是否删除成功
     */
    public function delete(EntityBase $entity)
    {
        if (empty($entity) || empty($entity->id)) {
            return false;
        }
        $entity->is_deleted = 1;
        return $this->update($entity);
    }

    /**
     * 获取有效实体（非删除的实体）
     * @param EntityBase $entity 实体
     * @param $id 主键
     * @return array|null|\yii\db\ActiveRecord 返回实体对象
     */
    public function getOne(EntityBase $entity, $id)
    {
        return $entity->find()->where(['id' => $id])
            ->andWhere(['is_deleted' => 0])
            ->one();
    } 

    /**
     * @return Connection
     */
    public function getTenantDb()
    { 
        $conn = EntityBase::getDb();
        return $conn;
    }

    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            // read property, e.g. getName()
            return $this->$getter();
        }

        if (method_exists($this, 'set' . $name)) {
            throw new InvalidCallException('Getting write-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new UnknownPropertyException('Getting unknown property: ' . get_class($this) . '::' . $name);
        }
    }
}

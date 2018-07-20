<?php
/**
 * Created by 孔焱
 * Date: 2015/4/16
 * Time: 14:10
 */

namespace app\framework\biz\bizparam\repositories;

use app\framework\db\EntityBase;


class ParameterRepositoryBase
{
    public function checkAttributeRepeated(EntityBase $entity, $attributeName, $attributeValue, $id = '', $filter = [])
    {
        $query = $entity::find()->where([$attributeName => $attributeValue]);
        $query->andWhere(["=", 'is_deleted', 0]);
        if (!empty($id)) {
            $query->andWhere(["<>", 'id', $id]);
        }

        if (!empty($filter)) {
            $query->andWhere($filter);
        }

        return $query->count() > 0;
    }

    /**
     * 获取
     * @param EntityBase $entity
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getOne(EntityBase $entity, $id)
    {
        return $entity::find()
            ->where(['is_deleted' => 0, 'id' => $id])
            ->one();
    }

    /**
     * 新增
     * @param $entity
     * @return bool
     */
    public function insert(EntityBase $entity)
    {
        if (isset($entity) === false) {
            return false;
        }

        return $entity->save();
    }

    /**
     * 修改
     * @param $entity
     * @return bool
     */
    public function update(EntityBase $entity)
    {
        if (isset($entity) === false) {
            return false;
        }

        return $entity->save();
    }

    /**
     * 删除
     * @param $entity
     * @return bool
     */
    public function delete(EntityBase $entity)
    {
        if (isset($entity) === false) {
            return false;
        }

        $entity->is_deleted = 1;
        return $entity->save();
    }
}

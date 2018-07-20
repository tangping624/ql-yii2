<?php  
namespace app\framework\biz\bizparam\repositories;

use app\framework\biz\bizparam\models\TParameter;
use app\framework\biz\bizparam\models\TParameterValue;
use app\framework\db\SqlHelper;

class ParameterValueRepository
{
    /**
     *获取参数选项值
     * @param $parameter_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getParameterOptionList($parameter_id)
    {
        return TParameterValue::find()
            ->select(['id', 'title', 'value', 'is_system'])
            ->where(['parameter_id' => $parameter_id, 'is_deleted' => 0])
            ->orderBy(['sort' => SORT_ASC])
            ->all();
    }

    /**
     * 根据Id获取有效的问题提分类
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getOne($id)
    {
        return TParameterValue::find()
            ->where(['is_deleted' => 0, 'id' => $id])
            ->one();
    }

    /**
     *  通过参数parameter_id获取最大参数选项Sort
     * @param $parameter_id
     * @return bool|null|string
     */
    public function getParameterMaxSort($parameter_id)
    {
        return TParameterValue::find()
            ->select('Max(Sort)')
            ->where(['is_deleted' => 0, 'parameter_id' => $parameter_id])
            ->createCommand()
            ->queryScalar();
    }

    /**
     * 新增
     * @param $entity
     * @return bool
     */
    public function insert($entity)
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
    public function update($entity)
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
    public function delete($entity)
    {
        if (isset($entity) === false) {
            return false;
        }
        $entity->is_deleted = 1;
        return $entity->save();
    }

    /**
     * 只更新业务参数值的Title
     * @param $parameterValueId  业务参数值ID
     * @param $title 业务参数值
     * @return bool
     * @throws \Exception
     */
    public function updateParameterTitle($parameterValueId, $title)
    {
        $conn = TParameterValue::getDb();
        $data = ["title" => $title];
        try {
            SqlHelper::update('t_parameter_value', $conn, $data, ['id' => $parameterValueId]);
            return true;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 只更新业务参数值的Title
     * @param $parameterValueId  业务参数值ID
     * @param $value 业务参数值
     * @return bool
     * @throws \Exception
     */
    public function updateParameterValue($parameterValueId, $value)
    {
        $conn = TParameterValue::getDb();
        if(is_array($value) ) {
            $data = $value;
        }else {
            $data = ["value" => $value];
        }
        
        try {
            SqlHelper::update('t_parameter_value', $conn, $data, ['id' => $parameterValueId]);
            return true;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}

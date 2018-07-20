<?php 

namespace app\framework\biz\bizparam\services;

use app\entities\TParameter;
use app\entities\TParameterValue;
use app\framework\biz\bizparam\repositories\ParameterRepository;
use app\framework\biz\bizparam\repositories\ParameterValueRepository;
use yii\helpers\ArrayHelper;

class ParameterValueService extends ServiceBase
{
    /**
     * 业务参数仓储类
     * @var ParameterRepository
     */
    private $_parameterValueRepository;
    private $_parameterRepository;

    /**
     * 构造器
     * @param ParameterValueRepository $parameterValueRepository
     * @param ParameterRepository $parameterRepository
     */
    public function __construct(ParameterValueRepository $parameterValueRepository, ParameterRepository $parameterRepository)
    {
        $this->_parameterValueRepository = $parameterValueRepository;
        $this->_parameterRepository = $parameterRepository;
    }

    /**
     * 获取所有参数
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getParameterIdByCode($paramCode)
    {
        return $this->_parameterRepository->getParameterIdByCode($paramCode);
    }

    /**
     * 获取指定参数的所有选项值
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getParameterOptions($paramter_id)
    {
        //$paramter_id = $this->getParameterIdByCode($paramCode);
        return $this->_parameterValueRepository->getParameterOptionList($paramter_id);
    }


    /**
     * 新增
     * @param $entity
     * @return bool
     */
    public function insert($entity)
    {
        return $this->_parameterValueRepository->insert($entity);
    }

    /**
     * 更新
     * @param $entity
     * @return bool
     */
    public function update($entity)
    {
        return $this->_parameterValueRepository->update($entity);
    }

    /**
     * 删除参数选项值
     * @param string $parameter_value_id
     */
    public function delete($parameter_value_id)
    {
        if (empty($parameter_value_id)) {
            //throw new InvalidValueException('删除参数选项错误，id 不能为空');
        }
        //逻辑删除数据
        $parameterValue = $this->getOne($parameter_value_id);
        $this->_parameterValueRepository->delete($parameterValue);
    }

    /**
     * 获取一个有效的投诉问题
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getOne($id)
    {
        return $this->_parameterValueRepository->getOne($id);
    }

    /**
     * 获取参数选项值信息
     * @param $oid
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getParameterValue($oid)
    {
        return $this->getOne($oid);
    }

    /**
     * 获取指定参数选项最大序号+1
     * @param $parameter_id
     * @return int
     */
    public function getParameterMaxSortNo($parameter_id)
    {
        $maxSortNo = 1;
        if (!empty($parameter_id)) {
            $maxSort = $this->_parameterValueRepository->getParameterMaxSort($parameter_id);
            if (!is_null($maxSort)) {
                $maxSortNo = $maxSort + 1;
            }
        }
        return $maxSortNo;
    }
    /**
     * 获取指定参数选项最大序号+1
     * @param $parameter_id
     * @return int
     */
    /**
     * 判断参数选项值是否重复
     * @param $parameter_id
     * @param $title
     * @param $parameter_value_id
     * @return bool
     */
    public function getIsRepeatParameterTitle($parameter_id, $title, $parameter_value_id)
    {
        $isRepeat = false;
        if (!empty($parameter_id) && !empty($title)) {
            $query = TParameterValue::find()
                ->select('id')
                ->where(['is_deleted' => 0, 'parameter_id' => $parameter_id, 'title' => $title]);
            if (!empty($parameter_value_id)) {
                $query->andWhere('id<>:id', [':id' => $parameter_value_id]);
            }
            $tmpId = $query->scalar();
            if (!empty($tmpId)) {
                $isRepeat = true;
            }
        }
        return $isRepeat;
    }

    /**
     * 只更新业务参数值
     * @param $parameterValueId  业务参数值ID
     * @param $title 业务参数的title
     * @return bool
     * @throws \Exception
     */
    public function updateParameterTitle($parameterValueId, $title)
    {
        return $this->_parameterValueRepository->updateParameterTitle($parameterValueId, $title);
    }

    /**
     * 只更新业务参数值
     * @param $parameterValueId  业务参数值ID
     * @param $value 业务参数的value
     * @return bool
     * @throws \Exception
     */
    public function updateParameterValue($parameterValueId, $value)
    {
        return $this->_parameterValueRepository->updateParameterValue($parameterValueId, $value);
    }
}

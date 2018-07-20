<?php

namespace app\modules\basic\services;

use app\modules\basic\repositories\ParameterRepository;
use yii\helpers\ArrayHelper;
use app\modules\ServiceBase;

class ParameterService extends ServiceBase
{
    /**
     * 业务参数仓储类
     * @var ParameterRepository
     */
    private $_parameterRepository;

    /**
     * 构造器
     * @param ParameterRepository $parameterRepository 业务参数仓储类
     */
    public function __construct(ParameterRepository $parameterRepository)
    {
        $this->_parameterRepository = $parameterRepository;
    }

    public function getParameterTitle($id)
    {
        return $this->_parameterRepository->getParameterTitle($id);
    }

    public function getAllParameterValue($code)
    {
        return $this->_parameterRepository->getAllParameterValue($code);
    }

    /**
     * 获取所有参数分组
     * @param string $accountId 公众号Id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getAllParameterGroup()
    {
        return $this->_parameterRepository->getAllParameterGroup();
    }

    /**
     * 获取所有参数
     * @param string $appCode 应用编码
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getAllParameter()
    {
        return $this->_parameterRepository->getAllParameter();
    }

    /**
     * 获取所有分组与参数
     * @param string $accountId 公众号Id
     * 返回格式：['groupName'=>[TParameter1,TParameter2]]
     * @return array|null
     */
    public function getAllParameterAndGroup()
    {
        $allGroupName = ArrayHelper::getColumn($this->getAllParameterGroup(), 'group_name');
        if (isset($allGroupName) === false || count($allGroupName) === 0) {
            return null;
        }
        $result = [];
        foreach ($allGroupName as $groupName) {
            $result[$groupName] = [];
        }
        $allParams = $this->getAllParameter();
        foreach ($allParams as $param) {
            if (array_key_exists($param['group_name'], $result) === false) {
                continue;
            }
            
            array_push($result[$param['group_name']], $param);
        }
        return $result;
    }

    /**
     * 获取所有参数的KEY键与类型
     * @author denghg 2015-4-16
     * @return array
     */
    public function getAllParameterKey()
    {
        return $this->_parameterRepository->getAllParameterKey();
    }

    /**
     * 根据业务参数ID获得业务参数数据集
     * @param $parameterId
     * @param $scopeId
     * @return array
     */
    public function getParameterValueByParameterId($parameterId, $scopeId)
    {
        return $this->_parameterRepository->getParameterValueByParameterId($parameterId, $scopeId);
    }

    /*
     * 根据参数类型获取参数
     * @param $type
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getParameterByType($type)
    {
        if (isset($type)) {
            return null;
        }
        return $this->_parameterRepository->getParameterByType($type);
    }

    public function getContractorKindTree()
    {
        return $this->_parameterRepository->getAllParameterValueTree(BIZ_CODE_CONTRACTOR_KIND);
    }
}

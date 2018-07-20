<?php 

namespace app\framework\biz\bizparam\services;

use app\framework\biz\bizparam\repositories\ParameterRepository;
use yii\helpers\ArrayHelper;

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
     * @param string $appCode 应用编码
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getAllParameterGroup($appCode = null)
    {
        return $this->_parameterRepository->getAllParameterGroup($appCode);
    }

    /**
     * 获取所有参数
     * @param string $appCode 应用编码
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getAllParameter($appCode = null)
    {
        return $this->_parameterRepository->getAllParameter($appCode);
    }

    /**
     * 获取所有分组与参数
     * @param string $appCode 应用编码
     * 返回格式：['groupName'=>[TParameter1,TParameter2]]
     * @return array|null
     */
    public function getAllParameterAndGroup($appCode = null)
    {
        $allGroupName = ArrayHelper::getColumn($this->getAllParameterGroup($appCode), 'group_name');
        if (isset($allGroupName) === false || count($allGroupName) === 0) {
            return null;
        }
        $result = [];
        foreach ($allGroupName as $groupName) {
            $result[$groupName] = [];
        }
        $allParams = $this->getAllParameter($appCode);
        foreach ($allParams as $param) {
            if (array_key_exists($param->group_name, $result) === false) {
                continue;
            }
            array_push($result[$param->group_name], $param);
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
    
    /**
     * 获取所有参数分组通过分组名
     * @param string $appCode 应用编码
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getAllParameterGroupName($groupName)
    {
        return $this->_parameterRepository->getAllParameterGroupName($groupName);
    }

    /**
     * 获取项目列表及指定参数的配置信息
     * @param $parameterCode
     * @param $projType，first=1级项目，final=末级项目，其它=所有项目
     * @param $corpId，取指定公司下的项目列表，为空则获取系统中所有项目列表
     * @return array
     */
    public function getProjectListWithParameterValue($parameterCode, $projType = 'first', $corpId = null)
    {
        return $this->_parameterRepository->getProjectListWithParameterValue($parameterCode, $projType, $corpId);
    }
}

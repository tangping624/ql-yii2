<?php
/**
 * Created by PhpStorm.
 * User: kongy
 * Date: 2015/4/15
 * Time: 11:04
 */

namespace app\framework\biz\bizparam\repositories;

use app\framework\biz\bizparam\models\TParameter;
use app\framework\biz\bizparam\models\TParameterValue;

class ParameterRepository
{
    /**
     * 根据参数code和名称获取参数值id
     * @param $code
     * @param $title
     * @return string
     */
    public function getParameterValueIdByCodeAndTitle($code, $title)
    {
        $parameterId = TParameter::find()
            ->select('id')
            ->where(['is_deleted' => 0, 'code' => $code])
            ->createCommand()
            ->queryScalar();
        if (!empty($parameterId)) {
            return TParameterValue::find()
                ->select(['id'])
                ->where(['parameter_id' => $parameterId, 'is_deleted' => 0])
                ->andWhere(['like', 'title', $title])
                ->createCommand()
                ->queryScalar();
        }

        return '';
    }

    /**
     * 获取参数名称
     * @param $id
     * @return bool|null|string
     */
    public function getParameterTitle($id)
    {
        return TParameterValue::find()
            ->select(['title'])
            ->where(['id' => $id, 'is_deleted' => 0])
            ->createCommand()
            ->queryScalar();
    }

    /**
     * @param $code
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getAllParameterValue($code)
    {
        $parameterId = $this->getParameterIdByCode($code);
        if (!empty($parameterId)) {
            return TParameterValue::find()
                ->where(['parameter_id' => $parameterId, 'is_deleted' => 0])
                ->orderBy('level,sort,title')
                ->asArray()
                ->all();
        }

        return [];
    }

    public function getStandardParameterValue($code)
    {
        $parameterId = $this->getParameterIdByCode($code);
        if (!empty($parameterId)) {
            return TParameterValue::find()
                ->select('id,title as name,value')
                ->where(['parameter_id' => $parameterId, 'is_deleted' => 0])
                ->orderBy('level,sort,title')
                ->asArray()
                ->all();
        }

        return [];
    }

    public function getAllParameterValueTree($code)
    {
        $parameterValueArray = $this->getAllParameterValue($code);
        $parameterValueTree = $this->_getArrayToTree($parameterValueArray);

        return $parameterValueTree;
    }

    private function _getArrayToTree($array, $parentId = null)
    {
        $tree = [];
        $item = [];
        foreach ($array as $k => $v) {
            if ($v['parent_id'] === $parentId) {
                $item['treeText'] = $v['title'];
                $item['value'] = $v['id'];
                $item['level'] = $v['level'];
                $item['sort'] = $v['sort'];

                unset($array[$k]);
                $item['childNode'] = $this->_getArrayToTree($array, $item['value']);
                $tree[] = $item;
            }
        }
        return $tree;
    }

    private function getTreeJSON($rootEntity, $entityArray)
    {
        $subEntityArray = [];
        foreach ($entityArray as $entity) {
            if ($entity->parent_id === $rootEntity->id) {
                array_push($subEntityArray, $this->getOrganTreeJSON($entity, $entityArray));
            }
        }


        return ['treeText' => $rootEntity->title, 'value' => $rootEntity->id, 'childNode' => $subEntityArray, 'level' => $rootEntity->level];
    }

    public function getAllRootParameterValue($code)
    {
        $parameterId = $this->getParameterIdByCode($code);
        if (!empty($parameterId)) {
            return TParameterValue::find()
                ->where(['parameter_id' => $parameterId, 'is_deleted' => 0, 'parent_id' => null])
                ->asArray()
                ->all();
        }

        return [];
    }

    /**
     *  通过参数Code获取参数id
     * @param $code
     * @return bool|null|string
     */
    public function getParameterIdByCode($code)
    {
        return TParameter::find()
            ->select('id')
            ->where(['is_deleted' => 0, 'code' => $code])
            ->createCommand()
            ->queryScalar();
    }

    /**
     * 获取参数值
     * @param $id
     * @return bool|null|string
     */
    public function getParameterValue($id)
    {
        return TParameterValue::find()
            ->select(['value'])
            ->where(['id' => $id, 'is_deleted' => 0])
            ->createCommand()
            ->queryScalar();
    }

    /**
     * 获取所有参数分组
     * @param string $appCode 应用编码
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getAllParameterGroup($appCode = null)
    {
        $query = TParameter::find()
            ->where(['is_deleted' => 0])
            ->groupBy(['group_name'])
            ->orderBy(['group_sort' => SORT_ASC])
            ->select('group_name');
        if (isset($appCode) && empty($appCode) === false) {
            $query->andWhere(['app_code' => $appCode]);
        }
        return $query->all();
    }

    /**
     * 获取所有参数
     * @param string $appCode 应用编码
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getAllParameter($appCode = null)
    {
        $query = TParameter::find()
            ->where(['is_deleted' => 0])
            ->orderBy(['group_sort' => SORT_ASC, 'sort' => SORT_ASC]);
        if (isset($appCode) && empty($appCode) === false) {
            $query->andWhere(['app_code' => $appCode]);
        }
        return $query->all();
    }

    /**
     * 获取所有参数的KEY键与类型
     * @author denghg 2015-4-16
     * @return array
     */
    public function getAllParameterKey()
    {
        $query = (new \yii\db\Query())
            ->select('id,code,type')
            ->from('t_parameter')
            ->where('is_deleted=0');
        $connection = TParameter::getDb();
        $command = $query->createCommand($connection);
        $rows = $command->queryAll();
        return $rows;
    }

    /**
     * 根据业务参数ID获得业务参数数据集
     * @param $parameterId
     * @param $scopeId
     * @return array
     */
    public function getParameterValueByParameterId($parameterId, $scopeId)
    {
        $query = (new \yii\db\Query())
            ->select("id,title,value,parent_id,level,scope_id,parameter_id,sort,is_system")
            ->from('t_parameter_value')
            ->where('is_deleted=0')
            ->andWhere('parameter_id=:parameterId', [':parameterId' => $parameterId]);
        if (isset($scopeId) && $scopeId <> '') {
            $query->andWhere('scope_id=:scopeId', [':scopeId' => $scopeId]);
        }
        $query->orderBy('sort');
        $connection = TParameterValue::getDb();
        $command = $query->createCommand($connection);
        $rows = $command->queryAll();
        return $rows;
    }


    /*
    * 根据参数类型获取参数
    * @param $type
    * @return array|\yii\db\ActiveRecord[]
    */
    public function getParameterByType($type)
    {
        return TParameter::find()
            ->where(['is_deleted' => 0, 'type' => $type])
            ->orderBy(['group_sort' => SORT_ASC, 'sort' => SORT_ASC])
            ->all();
    }


    /**
     *
     *
     */
    public function getAllParameterGroupName($groupName)
    {
        $query = TParameter::find()
            ->where(['is_deleted' => 0, 'group_name' => $groupName])
            ->select('id,name,code,type');

        return $query->all();
    }

    /**
     * 获取项目列表及指定参数的配置信息
     * @param $parameterCode
     * @param $projType，first=1级项目，final=末级项目，其它=所有项目
     * @param $corpId，取指定公司下的项目列表，为空则获取系统中所有项目列表
     * @return array
     */
    public function getProjectListWithParameterValue($parameterCode, $projType, $corpId)
    {
        $query = (new \yii\db\Query())
            ->select("prj.id as proj_id, prj.name as proj_name, pv.title, pv.value")
            ->from('t_project as prj')
            ->innerJoin('t_parameter as p')
            ->leftJoin('t_parameter_value as pv', 'pv.parameter_id = p.id and pv.scope_id = prj.id and pv.is_deleted = 0')
            ->where('prj.is_deleted = 0 and p.is_deleted = 0')
            ->andWhere('p.code = :parameterCode', [':parameterCode' => $parameterCode]);

        if ($corpId) {
            $query->andWhere('prj.corp_id = :corpId', [':corpId' => $corpId]);
        }

        if ($projType == 'first') {
            $query->andWhere('prj.level = 1');
        } elseif ($projType == 'final') {
            $query->andWhere('prj.is_end = 1');
        }

        $query->orderBy(['CONVERT(prj.name USING gbk)' => SORT_ASC]);

        return $query->createCommand(TParameterValue::getDb())->queryAll();
    }
}

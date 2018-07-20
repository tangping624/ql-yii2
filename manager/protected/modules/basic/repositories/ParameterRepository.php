<?php
/**
 * Created by PhpStorm.
 * User: kongy
 * Date: 2015/4/15
 * Time: 11:04
 */

namespace app\modules\basic\repositories;

use app\framework\db\EntityBase;
use app\repositories\RepositoryBase;

class ParameterRepository extends RepositoryBase
{
    /**
     * 根据参数code和名称获取参数值id
     * @param $code
     * @param $title
     * @return string
     */
    public function getParameterValueIdByCodeAndTitle($code, $title)
    {
        $query = (new \yii\db\Query())
            ->select('id')
            ->from('t_parameter')
            ->where(['is_deleted' => 0, 'code' => $code]);

        $connection = EntityBase::getDb();
        $command = $query->createCommand($connection);
        $parameterId = $command->queryScalar();
        if (!empty($parameterId)) {
            $query = (new \yii\db\Query())
                ->select('id')
                ->from('t_parameter_value')
                ->where(['parameter_id' => $parameterId, 'is_deleted' => 0])
                ->andWhere(['like', 'title', $title]);

            $command = $query->createCommand($connection);
            $parameterId = $command->queryScalar();

            return $parameterId;
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
        $query = (new \yii\db\Query())
            ->select('title')
            ->from('t_parameter_value')
            ->where(['id' => $id, 'is_deleted' => 0]);

        $connection = EntityBase::getDb();
        $command = $query->createCommand($connection);
        $parameterTitle = $command->queryScalar();
        return $parameterTitle;
    }

    /**
     * @param $code
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getAllParameterValue($code)
    {
        $connection = EntityBase::getDb();
        $parameterId = $this->getParameterIdByCode($code);
        if (!empty($parameterId)) {
            $query = (new \yii\db\Query())
                ->select('*')
                ->from('t_parameter_value')
                ->where(['parameter_id' => $parameterId, 'is_deleted' => 0])
                ->orderBy('level,sort,title');

            $command = $query->createCommand($connection);
            return $command->queryAll();
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
        $connection = EntityBase::getDb();
        $parameterId = $this->getParameterIdByCode($code);
        if (!empty($parameterId)) {
            $query = (new \yii\db\Query())
                ->select('*')
                ->from('t_parameter_value')
                ->where(['parameter_id' => $parameterId, 'is_deleted' => 0, 'parent_id' => null]);

            $command = $query->createCommand($connection);
            return $command->queryAll();
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
        $query = (new \yii\db\Query())
            ->select('id')
            ->from('t_parameter')
            ->where(['is_deleted' => 0, 'code' => $code]);

        $connection = EntityBase::getDb();
        $command = $query->createCommand($connection);
        return $command->queryScalar();
    }

    /**
     * 获取参数值
     * @param $id
     * @return bool|null|string
     */
    public function getParameterValue($id)
    {
        $query = (new \yii\db\Query())
            ->select('value')
            ->from('t_parameter_value')
            ->where(['id' => $id, 'is_deleted' => 0]);

        $connection = EntityBase::getDb();
        $command = $query->createCommand($connection);
        return $command->queryScalar();
    }

    /**
     * 获取所有参数分组
     * @param string $accountId 公众号Id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getAllParameterGroup()
    {
        $query = (new \yii\db\Query())
            ->select('*')
            ->from('t_parameter')
            ->where(['is_deleted' => 0])
            ->groupBy(['group_name'])
            ->orderBy(['group_sort' => SORT_ASC])
            ->select('group_name'); 
        $connection = EntityBase::getDb();
        $command = $query->createCommand($connection);
        return $command->queryAll();
    }

    /**
     * 获取所有参数
     * @param string $accountId 公众号Id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getAllParameter()
    {
        $query = (new \yii\db\Query())
            ->select('*')
            ->from('t_parameter')
            ->where(['is_deleted' => 0])
            ->orderBy(['group_sort' => SORT_ASC, 'sort' => SORT_ASC]);
        
        $connection = EntityBase::getDb();
        $command = $query->createCommand($connection);
        return $command->queryAll();
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
        $connection = EntityBase::getDb();
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
    public function getParameterValueByParameterId($parameterId, $accountId)
    {
        $query = (new \yii\db\Query())
            ->select("id,title,value,parent_id,level,scope_id,parameter_id,sort,is_system")
            ->from('t_parameter_value')
            ->where('is_deleted=0')
            ->andWhere('parameter_id=:parameterId', [':parameterId' => $parameterId]);
        if (isset($accountId) && $accountId <> '') {
            $query->andWhere('account_id=:account_id', [':account_id' => $accountId]);
        }
        $query->orderBy('sort');
        $connection = EntityBase::getDb();
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
        $query = (new \yii\db\Query())
            ->select('*')
            ->from('t_parameter')
            ->where(['is_deleted' => 0, 'type' => $type])
            ->orderBy(['group_sort' => SORT_ASC, 'sort' => SORT_ASC]);

        $connection = EntityBase::getDb();
        $command = $query->createCommand($connection);
        return $command->queryAll();
    }
}

<?php

namespace app\controllers;

use app\models\Organization;
use app\framework\web\extension\Controller;

class SelectController extends Controller
{
    /**
     * 获取组织架构
     * @param int $leaf
     * @return array
     */
    public function actionOrgs($leaf = 0)
    {
        // 暂不支持分页处理
        $onlyDisplayLeaf = $leaf == 1 ? true : false;
        $model = new Organization();
        $items = $onlyDisplayLeaf ? $model->searchAllLeafOrgs() : $model->searchAllOrgs();
        // 统一接口返回格式，方便后续扩展
        return $this->json(['total' => count($items), 'items' => $items]);
    }
}

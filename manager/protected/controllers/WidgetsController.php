<?php
/**
 * Created by PhpStorm.
 * User: FUYL
 * Date: 2015/3/18
 * Time: 17:36
 */

namespace app\controllers;

use app\framework\web\extension\Controller;
use app\models\Building;
use app\cache\BuildingCacheManager;

class WidgetsController extends Controller
{
    private $_popupLayout = '../../views/layouts/popup.php';
    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    /**
     * 获取楼栋单元树
     * @return mixed
     */
    public function actionGetBuildingTree($corpId)
    {
        $projects = BuildingCacheManager::instance()->getBuildingCache($corpId);
        return $this->json($projects->buildingTree);
    }

    public function actionRemoveCache($corpId)
    {
        $result = BuildingCacheManager::instance()->removeBuildingCache($corpId);
        return $this->json($result);
    }

}

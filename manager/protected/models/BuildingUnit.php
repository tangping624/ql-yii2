<?php
/**
 * Created by PhpStorm.
 * User: FUYL
 * Date: 2015/3/19
 * Time: 18:01
 */
namespace app\models;

use Yii;
use yii\base\Model;
use app\entities\TBuildingUnit;
use app\models\Building;

class BuildingUnit extends Model
{
    public function getBuildingUnitsByProjectId($projectId)
    {
        if (!isset($projectId)) {
            return null;
        }

        $buildingIds = (new Building())->getBuildingIdsByProjectId($projectId);

        $result = TBuildingUnit::find()
            ->andWhere(["in","building_id",$buildingIds])
            ->select("id, building_id as buildingId,unit,building_name as name")
            ->orderBy(["building_name"=>SORT_ASC,"unit"=>SORT_ASC])
            ->createCommand()
            ->queryAll();

        if (is_null($result)) {
            return [];
        }

        return $result;
    }
}
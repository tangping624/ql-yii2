<?php
/**
 * Created by PhpStorm.
 * User: FUYL
 * Date: 2015/3/20
 * Time: 14:07
 */
namespace app\models;

use Yii;
use yii\base\Model;
use app\entities\TBuilding;

class Building extends Model
{
    public function getBuildingIdsByProjectId($projectId)
    {
        if (!isset($projectId)) {
            return null;
        }

        $objects = TBuilding::find()
            ->where(['proj_id' => $projectId])
            ->select("id,name")
            ->createCommand()
            ->queryAll();

        if (is_null($objects)) {
            return [];
        }

        return $objects;
    }

    public function listBuildingsByProjectId($projectId)
    {
        $result = TBuilding::find()
            ->select("id,name")
            ->where(['proj_id' => $projectId])
            ->createCommand()
            ->queryAll();

        $emptyUnits = (new BuildingUnit())->getBuildingsEmptyUnit($result);

        for ($i = 0; $i < count($result); $i++) {
            $result[$i]['has_multi_units'] = true;
            if (empty($emptyUnits)) {
                $result[$i]['has_multi_units'] = false;
            } else {
                for ($j = 0; $j < count($emptyUnits); $j++) {
                    if ($result[$i]['id'] == $emptyUnits[$j]['building_id']) {
                        $result[$i]['has_multi_units'] = false;
                        break;
                    }
                }
            }

        }


        return $result;
    }


    /**
     * 获取项目楼栋选项列表
     * @param $projectId
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getBuildingOptionsByProject($projectId)
    {
        return TBuilding::find()
            ->where(['proj_id' => $projectId, 'is_deleted' => 0])
            ->select('id,name')
            ->orderBy('name')
            ->createCommand()
            ->queryAll();
    }

    protected function extractIdsToArray($objects)
    {
        $ids = [];
        foreach ($objects as $item) {
            array_push($ids, $item["id"]);
        }
        return $ids;
    }

    /**
     * 获取楼栋名称
     * @param $buildingId
     * @return bool|string
     */
    public function getBuildingName($buildingId)
    {
        return TBuilding::find()
            ->select("name")
            ->where(['id' => $buildingId])
            ->scalar();
    }

    public function getTreeBuildings($corpId)
    {
        $allProjects = (new Project())->searchProjectsByCorpId($corpId, 1);
        $allChildProjects = (new Project())->searchProjectsByCorpId($corpId, 2);
        $tree = [];
        foreach ($allProjects as $project) {
            if ($project["is_end"] == 1) {
                $treetop = [];
                $treetop["value"] = $project["complaint_proj_id"];
                $treetop["treeText"] = $project["complaint_proj_name"];

                $areas = $this->getBuildingAreasByProject($project["complaint_proj_id"]);
                if (count($areas) > 0) {
                    $treeArea = [];
                    foreach ($areas as $area) {
                        $areaTop = [];
                        $areaTop["value"] = $area['id'];
                        $areaTop["treeText"] = $area['name'];

                        $units = $this->getBuildingOptionsByArea($area['code'], $project["complaint_proj_id"]);
                        $treeChild = [];
                        foreach ($units as $unit) {
                            $treeChild[] = ['value' => $unit['id'], 'treeText' => $unit['name'], 'is_room' => true];
                        }
                        $areaTop["items"] = $treeChild;
                        $treeArea[] = $areaTop;

                    }
                    $treetop["areas"] = $treeArea;
                    $tree[] = $treetop;
                } else {
                    $units = $this->getBuildingOptionsByProject($project["complaint_proj_id"]);
                    $treeChild = [];
                    foreach ($units as $unit) {
                        $treeChild[] = ['value' => $unit['id'], 'treeText' => $unit['name'], 'is_room' => true];
                    }
                    $treetop["items"] = $treeChild;
                    $tree[] = $treetop;
                }


            } else {
                $treetop = [];
                $treetop["value"] = $project["complaint_proj_id"];
                $treetop["treeText"] = $project["complaint_proj_name"];
                $treeChild = [];
                foreach ($allChildProjects as $childProject) {
                    if ($childProject["parent_code"] == $project["code"]) {
                        $treeProjectChild = [];
                        $treeProjectChild["value"] = $childProject["complaint_proj_id"];
                        $treeProjectChild["treeText"] = $childProject["complaint_proj_name"];

                        $areas = $this->getBuildingAreasByProject($childProject["complaint_proj_id"]);
                        if (count($areas) > 0) {
                            $treeArea = [];
                            foreach ($areas as $area) {
                                $areaTop = [];
                                $areaTop["value"] = $area['id'];
                                $areaTop["treeText"] = $area['name'];

                                $units = $this->getBuildingOptionsByArea($area['code'], $childProject["complaint_proj_id"]);
                                $treeUnitChild = [];
                                foreach ($units as $unit) {
                                    $treeUnitChild[] = ['value' => $unit['id'], 'treeText' => $unit['name'], 'is_room' => true];
                                }
                                $areaTop["items"] = $treeUnitChild;
                                $treeArea[] = $areaTop;

                            }
                            $treeProjectChild["areas"] = $treeArea;
                            $treeChild[] = $treeProjectChild;
                        } else {
                            $units = $this->getBuildingOptionsByProject($childProject["complaint_proj_id"]);
                            $treeBuildChild = [];
                            foreach ($units as $unit) {
                                $treeBuildChild[] = ['value' => $unit['id'], 'treeText' => $unit['name'], 'is_room' => true];
                            }
                            $treeProjectChild["items"] = $treeBuildChild;
                            $treeChild[] = $treeProjectChild;
                        }
                    }
                }
                $treetop["childNode"] = $treeChild;
                $tree[] = $treetop;
            }


        }
        return $tree;
    }

    public function getProjectBuildingName($Ids)
    {
        $str = "";
        if ($Ids != "") {
            $idArr = explode(",", $Ids);
            $result = TBuilding::find()
                ->select("t_building.name,t_project.name as proj_name")
                ->innerJoin("t_project", "t_building.proj_id = t_project.id ")
                ->where(['t_building.is_deleted' => 0])
                ->andWhere(["in","t_building.id",$idArr])
                ->orderBy(["t_project.name" => SORT_ASC])
                ->createCommand()
                ->queryAll();

            $projArr = [];
            foreach ($result as $row) {
                if (isset($projArr[$row["proj_name"]])) {
                    $projArr[$row["proj_name"]] .= ",".$row["name"];
                } else {
                    $projArr[$row["proj_name"]] = $row["name"];
                }
            }
            foreach ($projArr as $key => $value) {
                if ($str == "") {
                    $str = $key."[".$value."]";
                } else {
                    $str .= ";".$key."[".$value."]";
                }
            }
        }

        return $str;
    }

    /**
     * 获取项目楼栋分区
     * @param $projectId
     * @param string $keyword
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getBuildingAreasByProject($projectId, $keyword = '')
    {
        $q = TBuilding::find()
            ->select('id,name,code')
            ->where(['proj_id' => $projectId, 'is_deleted' => 0, 'is_end' => 0]);

        if ($keyword) {
            $q->andWhere(['like', 'name', $keyword]);
        }

        return $q->orderBy('name')->createCommand()->queryAll();
    }

    /**
     * 获取项目楼栋分区下的楼栋
     * @param $parentCode
     * @param $projectId
     * @param string $keyword
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getBuildingOptionsByArea($parentCode, $projectId = '', $keyword = '')
    {
        $q = TBuilding::find()
            ->where(['is_deleted' => 0, 'parent_code' => $parentCode]);

        if ($projectId) {
            $q->andWhere(['proj_id' => $projectId]);
        }

        if ($keyword) {
            $q->andWhere(['like', 'name', $keyword]);
        }

        return $q->select('id,name')->orderBy('name')->createCommand()->queryAll();
    }
}
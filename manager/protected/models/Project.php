<?php
/**
 * Created by PhpStorm.
 * User: FUYL
 * Date: 2015/3/19
 * Time: 17:57
 */

namespace app\models;

use Yii;
use yii\base\Model;
use app\entities\TProject;
use app\entities\TOrganization;

class Project extends Model
{
    public function searchProjects($keyword)
    {
        if (!isset($keyword)) {
            $keyword = "";
        }

        $projects = TProject::find()
            ->where(["is_end" => 1])
            ->andWhere(["like", 't_project.name', $keyword])
            ->select('t_project.id,t_project.name,t_project.corp_id')
            ->orderBy(["t_project.code" => SORT_ASC])
            ->createCommand()
            ->queryAll();

        if (is_null($projects)) {
            return [];
        }

        $orgs = TOrganization::find()
            ->select('id,name')
            ->createCommand()
            ->queryAll();

        for ($i = 0; $i < count($projects); $i++) {
            foreach ($orgs as $org) {
                if (ucwords($org["id"]) == ucwords($projects[$i]["corp_id"])) {
                    $projects[$i]["corp_name"] = $org["name"];
                    break;
                }
            }
        }

        return $projects;
    }

    public function listStageByParentId($parent_id)
    {
        $project = TProject::findOne(['id' => $parent_id]);
        if (!$project) {
            return [];
        }

        return TProject::find()
            ->select('id,name')
            ->where('is_end=1')
            ->andWhere(['=', 'parent_code', $project->code])
            ->orderBy(['name' => SORT_ASC])
            ->createCommand()
            ->queryAll();
    }

    /**
     *
     */
    public function listAllSubProjectIdsByProjectIds($project_ids)
    {
        $projects = TProject::find()
            ->select('code')
            ->where(['id' => $project_ids])
            ->createCommand()
            ->queryAll();

        $query = TProject::find()
            ->select('id');
        for ($i = 0; $i < count($projects); $i++) {
            $query->orWhere(['like', 'code', $projects[$i]['code']]);
        }

        $result_projects = $query
            ->andWhere('is_deleted = 0')
            ->createCommand()
            ->queryAll();

        $result_ids = [];
        for ($i = 0; $i < count($result_projects); $i++) {
            array_push($result_ids, $result_projects[$i]['id']);
        }
        return $result_ids;
    }

    public function searchAllProjects($page, $pageSize, $keyword, $level = '', $isEnd = null)
    {
        $offset = ($page - 1) * $pageSize;
        $query = TProject::find()
            ->select('id as complaint_proj_id,name as complaint_proj_name,code,is_end,parent_code,corp_id')
            ->where(['like', 'name', $keyword])
            ->andWhere(['=', 'is_deleted', 0]);
        if (!empty($level)) {
            $query = $query->andWhere(["=", 'level', $level]);
        }
        if (!is_null($isEnd)) {
            $query = $query->andWhere(["=", 'is_end', $isEnd]);
        }

        return $query->orderBy(["code" => SORT_ASC, "is_end" => SORT_ASC])
            ->offset($offset)
            ->limit($pageSize)
            ->createCommand()
            ->queryAll();
    }

    public function searchProjectsByCorpId($CorpId, $level = '')
    {
        $query = TProject::find()
            ->select('id as complaint_proj_id,name as complaint_proj_name,code,is_end,parent_code,corp_id')
            ->where(['=', 'corp_id', $CorpId]);
        if (!empty($level)) {
            $query = $query->andWhere(["=", 'level', $level]);
        }

        return $query->orderBy(["code" => SORT_ASC, "is_end" => SORT_ASC])
            ->createCommand()
            ->queryAll();
    }

    public function searchAllProjectsCount($keyword, $level = '', $isEnd = null)
    {
        $query = TProject::find()
            ->select('count(*) as total')
            ->where(['like', 'name', $keyword])
            ->andWhere(['=', 'is_deleted', 0]);
        if (!empty($level)) {
            $query = $query->andWhere(["=", 'level', $level]);
        }
        if (!is_null($isEnd)) {
            $query = $query->andWhere(["=", 'is_end', $isEnd]);
        }

        return $query->createCommand()
            ->queryAll();
    }

    public function listTopProject($project_ids)
    {
        $result = TProject::find()
            ->select('id,name,code')
            ->where('is_end=0')
            ->andWhere(['in', 'id', $project_ids])
            ->orderBy(["code" => SORT_ASC])
            ->createCommand()
            ->queryAll();

        $has_child_projects = TProject::find()
            ->select('parent_code')
            ->distinct()
            ->where('is_end=1')
            ->createCommand()
            ->queryAll();

        for ($i = 0; $i < count($result); $i++) {
            $result[$i]['has_childs'] = false;
            for ($j = 0; $j < count($has_child_projects); $j++) {
                if ($result[$i]['code'] == $has_child_projects[$j]['parent_code']) {
                    $result[$i]['has_childs'] = true;
                    break;
                }
            }
        }

        return $result;
    }


    /**
     * todo:这里暂时放在这里先满足功能，作为公共部分这块有点乱，没有规划，代码结构也比较乱，复用度低，后面需要重构 chenxy注
     * @param type $page
     * @param type $pageSize
     * @param type $filters
     * @return type
     */
    public function getFirstLevelProjects($page, $pageSize, $filters)
    {
        $offset = ($page - 1) * $pageSize;
        $query = $items = TProject::find()
            ->select('id ,name as text, code')
            ->where("is_deleted = 0");

        $items = $this->addFilters($query, $filters)
            ->orderBy('name asc')
            ->offset($offset)
            ->limit($pageSize)
            ->createCommand()
            ->queryAll();

        if (count($items) == 0) {
            $total = 0;
        } else {
            $query = TProject::find()
                ->select('count(*) as total')
                ->where("is_deleted = 0");

            $total = $this->addFilters($query, $filters)->count();
        }

        return ['items' => $items, 'total' => $total];
    }

    private function addFilters($query, $filters)
    {
        foreach ($filters as $f) {
            $query = $query->andWhere($f);
        }

        return $query;
    }

    public function getProject($projId)
    {
        return TProject::find()
            ->where(["is_deleted" => 0])
            ->andWhere(["=", 'id', $projId])
            ->select('id,name,code')
            ->one();
    }
}

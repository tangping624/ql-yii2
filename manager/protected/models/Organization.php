<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 2015/3/24
 * Time: 17:17
 */
namespace app\models;

use Yii;
use yii\base\Model;
use app\entities\TOrganization;

class Organization extends Model
{
    public function searchChildOrgs($orgid, $allorgs)
    {
        if (!isset($orgid)) {
            $orgid = "";
        }
        $childorgs = array();
        foreach ($allorgs as $org) {
            if ($org["parent_id"] == $orgid) {
                array_push($childorgs, $org);
            }
        }
        return $childorgs;
    }

    public function searchAllLeafOrgs()
    {
        return
            TOrganization::find()
                ->where("is_deleted=0")
                ->andWhere("is_company=1")
                ->orderBy("parent_id,name")
                ->createCommand()
                ->queryAll();
    }

    public function searchAllOrgs()
    {
        return TOrganization::find()
            ->where(["is_deleted" => 0])
            ->select('id,name,parent_id')
            ->orderBy(["name" => SORT_ASC])
            ->createCommand()
            ->queryAll();
    }

    public function getOrganization($orgid)
    {
        return TOrganization::find()
            ->where(["is_deleted" => 0])
            ->andWhere(["=", 'id', $orgid])
            ->select('id,name,parent_id')
            ->one();
    }

    public function getOrg($orgid)
    {
        if (!isset($orgid)) {
            $orgid = "";
        }
        if (empty($orgid)) {
            //取集团
            return TOrganization::find()->where('is_deleted=0 and is_company=1 and parent_id is null')
                ->select('id,name')
                ->createCommand()
                ->queryAll();
        }
        return TOrganization::find()
            ->where(["is_deleted" => 0])
            ->andWhere(["=", 'id', $orgid])
            ->select('id,name')
            ->createCommand()
            ->queryAll();
    }

    public function getTreeOrgs($orgid)
    {
        $allorgs = $this->searchAllOrgs();
        $org = $this->getOrg($orgid);
        $tree = array();
        $treetop = array();
        $treetop["value"] = $org[0]["id"];
        $treetop["treeText"] = $org[0]["name"];
        $treetop["childNode"] = $this->getCategoryTree($org[0]["id"], $allorgs);
        $tree[] = $treetop;

        return $tree;
    }

    public function getCategoryTree($pid, $allorgs)
    {
        $data = $this->searchChildOrgs($pid, $allorgs);
        //目录树
        $tree = array();
        if (!empty($data)) {
            foreach ($data as $v) {
                $child = $this->getCategoryTree($v['id'], $allorgs);
                $tree[] = array('value' => $v['id'], 'treeText' => $v['name'], 'childNode' => $child);
            }
        }
        return $tree;
    }
}

<?php
namespace app\modules\baike\repositories;
use app\modules\RepositoryBase;
use app\entities\baike\MEmergency;

use yii;
use app\framework\db\PageResult;
class EmergencyRepository  extends RepositoryBase
{


    //广告管理列表
    public function getBaikeList($skip, $limit,$keyword)
    {
        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $where='';
        if(!empty($keyword)){
            $where.=" where title like CONCAT('%', :keyword, '%')";
            $params[':keyword'] = $keyword;
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS id,title,content,logo,created_on,address,tel from m_emergency".$where."   ORDER BY created_on desc LIMIT :skip,:limit";
        $params[':skip'] = $skip;
        $params[':limit'] = $limit;
        $pageResult = new PageResult();
        $db = MEmergency::getDb();
        $cmd = $db->createCommand($sql, $params);
        $pageResult->items = $cmd->queryAll();
        $sql = "SELECT FOUND_ROWS() AS count;";
        $rows = $db->createCommand($sql)->queryAll();
        $pageResult->total = $rows[0]["count"];
        return $pageResult;

    }

    public function save($entiy)
    {
        if (!isset($entiy)) {
            throw new \InvalidArgumentException('$entiy');
        }
        return $entiy->save();
    }

    public function getWiki($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return MEmergency::findOne($id);
    }


    public function deleteWikiInfo($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return MEmergency::deleteAll(['id' => $id]);

    }
}


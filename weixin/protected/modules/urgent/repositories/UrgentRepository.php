<?php
namespace app\modules\urgent\repositories;

use app\entities\baike\MEmergency;
use app\modules\RepositoryBase;
use app\framework\db\PageResult;
class UrgentRepository extends RepositoryBase
{


    public function getEmergencyList($skip, $limit)
    {
        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS id,title,content,logo,tel,address,created_on from m_emergency  ORDER BY created_on desc  LIMIT :skip,:limit";
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

    public function getDetails($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }

        return MEmergency::find()->select('id,tel,address,logo,title,content,created_on')->where(['id'=>$id])->one();
    }

}
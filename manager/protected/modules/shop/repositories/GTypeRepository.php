<?php
namespace app\modules\shop\repositories;
use app\entities\goods\GwhType;
use app\modules\RepositoryBase;
use app\framework\db\PageResult;
class GTypeRepository extends RepositoryBase
{
    //购物惠分类列表
    public function getTypeList($skip, $limit,$keyword,$app_code)
    {
        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $where='';
        if(!empty($keyword)){
            $where.=" and name like CONCAT('%', :keyword, '%')";
            $params[':keyword'] = $keyword;
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS id,name FROM gwh_type WHERE app_code=:app_code  ".$where." ORDER BY created_on desc LIMIT :skip,:limit";
        $params[':app_code']=$app_code;
        $params[':skip'] = $skip;
        $params[':limit'] = $limit;
        $pageResult = new PageResult();
        $db = GwhType::getDb();
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

    public function getType($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return GwhType::findOne($id);
    }

    //购物惠分类删除
    public function deleteType($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return GwhType::deleteAll(['id' => $id]);

    }
    
}
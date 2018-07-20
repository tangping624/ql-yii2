<?php
namespace app\modules\common\repositories;
use app\entities\goods\SGoods;
use app\modules\RepositoryBase;
use app\framework\db\PageResult;
class CommonRepository extends RepositoryBase
{
    //旅游&投资&合作交流列表
    public function getShopList($skip, $limit,$keyword,$app_code)
    {
        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $where='';
        if(!empty($keyword)){
            $where.=" and (a.name like CONCAT('%', :keyword, '%') or b.name like CONCAT('%', :keyword, '%'))";
            $params[':keyword'] = $keyword;
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a. NAME, a.logo, b. NAME AS seller_name FROM s_goods a INNER JOIN s_merchant b ON a.seller_id = b.id WHERE a.app_code=:app_code  ".$where." ORDER BY a.created_on desc LIMIT :skip,:limit";
        $params[':app_code']=$app_code;
        $params[':skip'] = $skip;
        $params[':limit'] = $limit;
        $pageResult = new PageResult();
        $db = SGoods::getDb();
        $cmd = $db->createCommand($sql, $params);
        $pageResult->items = $cmd->queryAll();
        $sql = "SELECT FOUND_ROWS() AS count;";
        $rows = $db->createCommand($sql)->queryAll();
        $pageResult->total = $rows[0]["count"];
        return $pageResult;
    }

    //编辑获取旅游&投资&合作交流新增的商家
    public function getSellerList($skip,$limit,$name,$app_code){
        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $where='';
        if(!empty($name)){
            $where.=" and b.name like CONCAT('%', :name, '%')";
            $params[':name'] = $name;
        }
        //$sql = " SELECT SQL_CALC_FOUND_ROWS b.id,b.type_pid,b.type_id,b.name,a.app_code FROM seller_type a INNER JOIN s_merchant b ON a.id=b.type_pid WHERE (a.app_code='tour' OR a.app_code='invest' OR a.app_code='cooperation'  OR a.app_code='vip'  OR a.app_code='migrate'  OR a.app_code='serve'  OR a.app_code='teach'  OR a.app_code='sports'  OR a.app_code='specialty') AND b.is_deleted=0 ".$where." ORDER BY b.created_on desc LIMIT :skip,:limit";
        $sql = " SELECT SQL_CALC_FOUND_ROWS b.id,b.type_pid,b.type_id,b.name,a.app_code FROM seller_type a INNER JOIN s_merchant b ON a.id=b.type_pid WHERE a.app_code=:app_code AND b.is_deleted=0 ".$where." ORDER BY b.created_on desc LIMIT :skip,:limit";
        $params[':app_code']=$app_code;
        $params[':skip']=$skip;
        $params[':limit'] = $limit;
        $pageResult = new PageResult();
        $db = SGoods::getDb();
        $cmd = $db->createCommand($sql,$params) ;
        $pageResult->items = $cmd->queryAll();
        $sql = "SELECT FOUND_ROWS() AS count;";
        $rows = $db->createCommand($sql)->queryAll();
        $pageResult->total = $rows[0]["count"];
        return $pageResult;
    }

    //保存
    public function save($entiy)
    {
        if (!isset($entiy)) {
            throw new \InvalidArgumentException('$entiy');
        }
        return $entiy->save();
    }

    //编辑实体
    public function getShop($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return SGoods::findOne($id);
    }

    //旅游&投资&合作交流删除
    public function deleteShop($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return SGoods::deleteAll(['id' => $id]);

    }
    
}
<?php
namespace app\modules\shop\repositories;
use app\entities\goods\SGoods;
use app\modules\RepositoryBase;
use app\framework\db\PageResult;
class GShopRepository extends RepositoryBase
{
    //购物惠列表
    public function getShopList($skip, $limit,$keyword,$app_code)
    {
        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $where='';
        if(!empty($keyword)){
            //$where.=" and a.name like CONCAT('%', :keyword, '%')";
            $where.=" and (a.name like CONCAT('%', :keyword, '%') or b.name like CONCAT('%', :keyword, '%'))";
            $params[':keyword'] = $keyword;
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.logo, a.name, b. NAME AS shop_name, c. NAME AS type_name FROM s_goods a INNER JOIN s_merchant b ON a.seller_id = b.id LEFT JOIN gwh_type c ON a.type_id = c.id  WHERE a.app_code=:app_code AND b.is_deleted=0 ".$where." ORDER BY a.created_on desc LIMIT :skip,:limit";
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

    public function save($entiy)
    {
        if (!isset($entiy)) {
            throw new \InvalidArgumentException('$entiy');
        }
        return $entiy->save();
    }

    public function getShop($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return SGoods::findOne($id);
    }

    //购物惠删除
    public function deleteShop($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return SGoods::deleteAll(['id' => $id]);

    }

    
    public function getShopDetails($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        $params[':id'] = $id;
        $sql = "SELECT a.*, c.id AS s_id, c. NAME AS seller_name, b.id AS t_id, b. NAME AS tname FROM s_goods a LEFT JOIN gwh_type b ON a.type_id = b.id INNER JOIN s_merchant c ON a.seller_id = c.id WHERE a.id =:id";
        return SGoods::getDb()->createCommand($sql, $params)->queryOne();
    }

    //判断分类下有没有商品
    public function getTypeGoods($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        $params[':id'] = $id;
        $sql = "SELECT 1 FROM gwh_type a INNER JOIN s_goods b ON a.id=b.type_id WHERE a.id=:id";
        return SGoods::getDb()->createCommand($sql, $params)->queryOne();
    }

}
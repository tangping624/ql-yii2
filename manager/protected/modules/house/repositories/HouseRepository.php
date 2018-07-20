<?php
namespace app\modules\house\repositories;
use app\entities\goods\SGoods;
use app\modules\RepositoryBase;
use app\framework\db\PageResult;
class HouseRepository extends RepositoryBase
{
    //房产列表
    public function getHouseList($skip, $limit,$keyword)
    {
        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $where='';
        if(!empty($keyword)){
            $where.=" and a.name like CONCAT('%', :keyword, '%')";
            $params[':keyword'] = $keyword;
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,a.logo,a.name,b.name AS shop_name,d.name AS type_name FROM s_goods a INNER JOIN s_merchant b ON a.seller_id=b.id LEFT JOIN seller_to_tag c ON a.type_id=c.id LEFT JOIN estate_tag d ON c.estate_tag_id=d.id WHERE a.app_code='house' AND b.is_deleted=0 ".$where." ORDER BY a.created_on desc LIMIT :skip,:limit";
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

    public function getHouse($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return SGoods::findOne($id);
    }
    
    //房产删除
    public function deleteHouse($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return SGoods::deleteAll(['id' => $id]);

    }

    //编辑获取房产类别
    public function getHouseType($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        $params[':id'] = $id;
        $sql = "SELECT a.id,a.estate_tag_id,b.name,a.seller_id FROM seller_to_tag a INNER JOIN estate_tag b ON a.estate_tag_id=b.id WHERE a.seller_id=:id ORDER BY b.order_id ASC";
        return SGoods::getDb()->createCommand($sql, $params)->queryAll();
    }


    public function getHouseDetails($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        $params[':id'] = $id;
        $sql = "SELECT a.*,c.id AS s_id,c.name AS seller_name,d.id AS t_id,d.name AS tname  FROM s_goods a LEFT JOIN seller_to_tag b ON a.type_id=b.id LEFT JOIN s_merchant c ON a.seller_id=c.id LEFT JOIN estate_tag d ON b.estate_tag_id=d.id WHERE a.id=:id";
        return SGoods::getDb()->createCommand($sql, $params)->queryOne();
    }

}
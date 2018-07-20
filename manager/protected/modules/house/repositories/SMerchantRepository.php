<?php
namespace app\modules\house\repositories;
use app\entities\merchant\SMerchant;
use app\modules\RepositoryBase;
use app\framework\db\PageResult;
class SMerchantRepository extends RepositoryBase{

    //获取分类对应的商家
    public function getSellerList($skip,$limit,$name,$app_code){
        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $where='';
        if(!empty($name)){
            $where.=" and a.name like CONCAT('%', :name, '%')";
            $params[':name'] = $name;
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.name,a.type_id FROM s_merchant a INNER JOIN seller_type b ON a.type_pid = b.id WHERE b.app_code=:app_code AND a.is_deleted = 0 ".$where." ORDER BY a.created_on desc LIMIT :skip,:limit";
        $params[':app_code'] = $app_code;
        $params[':skip']=$skip;
        $params[':limit'] = $limit;
        $pageResult = new PageResult();
        $db = SMerchant::getDb();
        $cmd = $db->createCommand($sql,$params) ;
        $pageResult->items = $cmd->queryAll();
        $sql = "SELECT FOUND_ROWS() AS count;";
        $rows = $db->createCommand($sql)->queryAll();
        $pageResult->total = $rows[0]["count"];
        return $pageResult;
    }


}
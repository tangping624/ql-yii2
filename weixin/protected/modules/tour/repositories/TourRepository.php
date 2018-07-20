<?php
namespace app\modules\tour\repositories;

use app\entities\merchant\SMerchant;
use app\modules\RepositoryBase;
use app\framework\db\PageResult;
class TourRepository extends RepositoryBase
{

    //移民管理列表
    public function getTourList($skip, $limit,$type_pid,$type_id,$city_id,$keyword,$city_pid)
    {
        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $params[':type_pid'] = $type_pid;
        $where='where a.is_deleted=0 and a.type_pid=:type_pid';
        $order='';
        if(!empty($type_id)){
            $where.=" and a.type_id=:type_id";
            $params[':type_id'] = $type_id;
        }
        //好评排序
        if($keyword==3){
            $order.=" a.dz_num desc, ";
        }
        //收藏排序
        if($keyword==4){
            $order.=" a.sc_num desc, ";
        }
        if(!empty($city_id)){
            $where.=" and a.city_id=:city_id";
            $params[':city_id'] = $city_id;
        }
        if(!empty($city_pid)){
            $where.=" and a.city_pid=:city_pid";
            $params[':city_pid'] = $city_pid;
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,  a.name, a.logo, a.summary, a.created_on,a.logo,b.name as typename ,a.dz_num,a.sc_num FROM s_merchant a left join seller_type b on a.type_id=b.id ".$where." ORDER BY  ".$order." sort asc, created_on desc LIMIT :skip,:limit";
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
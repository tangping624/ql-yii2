<?php
namespace app\modules\repast\repositories;

use app\entities\merchant\SMerchant;
use app\modules\RepositoryBase;
use app\framework\db\PageResult;
use app\framework\utils\PagingHelper;
class RepastRepository extends RepositoryBase
{

    public function getData($lng=0,$lat=0,$typePid='',$pageIndex=1,$pageSize=10,$city_id,$type_id,$keyword)
    {
        $where=" WHERE a.latitudes <> '' AND a.longitudes <> '' AND a.type_pid = :typePid AND a.is_deleted = 0";
        $order='';
       $params=[];
        if(!empty($city_id)){
            $where.=" and a.city_id=:city_id ";
            $params[':city_id']=$city_id;

        }
        if(!empty($type_id)){
            $where.=" and a.type_id=:type_id ";
            $params[':type_id']=$type_id;
        }
        //好评排序
        if($keyword==3){
            $order.=" a.dz_num desc, ";
        }
        //收藏排序
        if($keyword==4){
            $order.=" a.sc_num desc, ";
        }
        $sql = "SELECT a.id,a.`name`,a.logo,a.summary,getDistance(:lng,:lat,longitudes,latitudes),a.sc_num,a.dz_num AS dis FROM s_merchant a".$where." ORDER BY ".$order."  dis ASC LIMIT :skip,:limit";
        $skip = PagingHelper::getSkip($pageIndex, $pageSize);
        $params[':lng']=(float)$lng;
        $params[':lat']=(float)$lat;
        $params[':typePid']=$typePid;
        $params[':skip']= (int)$skip;
        $params[':limit']=(int)$pageSize;
        $db = SMerchant::getDb();
        $rs = $db->createCommand($sql,$params)->queryAll();
        return $rs;
    }



}
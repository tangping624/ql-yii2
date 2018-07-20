<?php 
namespace app\modules\nearby\repositories;
use app\modules\RepositoryBase;
use app\framework\utils\PagingHelper;
use app\entities\merchant\SMerchant;

class NearRepository extends RepositoryBase
{
    public function getData($lng=0,$lat=0,$typePid='',$pageIndex=1,$pageSize=10)
    {
         $sql = "SELECT a.id,a.`name`,a.logo,a.summary,getDistance(:lng,:lat,longitudes,latitudes) AS dis FROM s_merchant a
                WHERE a.latitudes <> '' AND a.longitudes <> '' AND a.type_pid = :typePid AND a.is_deleted = 0
                ORDER BY  dis ASC LIMIT :skip,:limit";
        $skip = PagingHelper::getSkip($pageIndex, $pageSize);

        $params = [
            ':lng'=>(float)$lng,
            ':lat'=>(float)$lat,
            ':typePid'=>$typePid,
            ':skip'=> (int)$skip,
            ':limit'=> (int)$pageSize
        ]; 
        //var_dump($params);die;       
        $db = SMerchant::getDb();
        //echo $db->createCommand($sql,$params)->getRawSql();die;
        $rs = $db->createCommand($sql,$params)->queryAll();
        return $rs;
    }
}

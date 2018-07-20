<?php 
namespace app\modules\appapi\repositories;
use app\entities\city\City;
use app\modules\RepositoryBase;


class CityRepository extends RepositoryBase{



    //默认城市
    public function locCityByName()
    {
        //$param[":name"] = '%' . addcslashes(urldecode($keyword),'%_') . '%';
        //$param[":keyword"] = $keyword;
        /*$sql = "SELECT id,parent_id,`name`,longitudes,latitudes FROM city
                where (`name` LIKE :name OR LOCATE(`name`,:keyword) > 0) AND (parent_id is NULL or parent_id = '') and is_deleted = 0 
                order by `name` ASC LIMIT 1";*/
        $sql = "SELECT id,parent_id,`name`,longitudes,latitudes FROM city 
                where (parent_id is NULL or parent_id = '') and is_default = 1 
                order by `name` ASC LIMIT 1";
        $db = City::getDb();
        $rs = $db->createCommand($sql)->queryOne();
        if(!$rs){
            //若没有匹配到，默认取第一个城市
            $sql = "SELECT id,parent_id,`name`,longitudes,latitudes FROM city where (parent_id is NULL or parent_id = '') order by `name` ASC LIMIT 1";
            $rs = $db->createCommand($sql)->queryOne();
        }
        return $rs;
    }
}

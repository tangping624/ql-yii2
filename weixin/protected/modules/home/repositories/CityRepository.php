<?php 
namespace app\modules\home\repositories;
use app\entities\city\City;
use app\modules\RepositoryBase;


class CityRepository extends RepositoryBase{

    //获取城市
    public function getCity()
    {
       $param=[];
        $sql="SELECT id,parent_id,`name`,longitudes,latitudes FROM city where (parent_id is NULL or parent_id = '') and is_deleted = 0 order by `name` ASC";
        return City::getDb()->createCommand($sql, $param)->queryAll();

    }

    //获取城市
    public function getCityByName($keyword)
    {
        $param[":name"] = '%' . addcslashes(urldecode($keyword),'%_') . '%';
        $sql = "SELECT id,parent_id,`name`,longitudes,latitudes FROM city where `name` LIKE :name AND (parent_id is NULL or parent_id = '') and is_deleted = 0 order by `name` ASC";
        $db = City::getDb();
        //echo $db->createCommand($sql,$param)->getRawSql();die;
        $rs = $db->createCommand($sql,$param)->queryAll();
        foreach ($rs as $k => &$v) {
            $shi =  City::find()->select("id,name as treeText,longitudes,latitudes")->where([ 'parent_id' => $v['id']])->asArray()->all();
            $v['childNode'] = $shi;
        }

        return $rs;
    }

    //定位一级城市
    public function locCityByName($keyword)
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
        //echo $db->createCommand($sql,$param)->getRawSql();die;
        $rs = $db->createCommand($sql)->queryOne();
        if(!$rs){
            //若没有匹配到，默认取第一个城市
            $sql = "SELECT id,parent_id,`name`,longitudes,latitudes FROM city where (parent_id is NULL or parent_id = '') order by `name` ASC LIMIT 1";
            $rs = $db->createCommand($sql)->queryOne();
        }
        return $rs;
    }
}

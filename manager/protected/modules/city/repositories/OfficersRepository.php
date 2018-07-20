<?php
namespace app\modules\city\repositories;
use app\modules\RepositoryBase;
use app\entities\city\City;
use yii;
class OfficersRepository  extends RepositoryBase
{

    public function getMOfficersEntity($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        $SGoodsType = City::findOne($id);
        return $SGoodsType;
    }

    public function saveMOfficersInfo($MOfficers)
    {
        $data = $MOfficers->save();
        return $data;
    }

    public function getMOfficersInfo($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        $data = City::find()->where(['id' => $id])->select("id,name,content,longitudes,latitudes")->asArray()->one();
        return $data;
    }

    public function deleteMOfficersInfo($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        $param=[];
        $param[':id']=$id;
        $sql="select 1 from city where parent_id=:id and is_deleted=0 limit 1 ";
        $rst=yii::$app->db->createCommand($sql, $param)->queryAll();
        if(empty($rst)) {
            $data = City::deleteAll(['id'=>$id]);
            return $data;
        }else{
            return false;
        }
       
    }



    public function getshowList(){

        $res =  City::find()->select("id,name as treeText,is_default,code")->where('parent_id is null')->orderBy('name asc')->asArray()->all();
        foreach ($res as $k => &$v) {
            $shi =  City::find()->select("id,name as treeText")->where([ 'parent_id' => $v['id']])->asArray()->all();
            $v['childNode'] = $shi;
        }

        return $res;
    }



    public function findMaxCode(){

        $sql="select max(code) as code  from city  ";
        $data=yii::$app->db->createCommand($sql)->queryOne();
        return $data['code'];
    }


    public function findCity()
    {
        $sql=" select id, parent_id ,name from city where parent_id is null and is_deleted=0";
        $data=yii::$app->db->createCommand($sql)->queryAll();
        return $data;
    }

    public function findSon($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        $params[':id']=$id;
        $sql=" select id, parent_id ,name from city where parent_id=:id and is_deleted=0 order by code";
        $data=yii::$app->db->createCommand($sql, $params)->queryAll();
        return $data;
    }

    public function setDefaultCity($id){
        if(empty($id)){
            throw new \InvalidArgumentException('$id');
        }
        $sql=" update city set is_default=0";
        yii::$app->db->createCommand($sql)->execute();
        $params[':id']=$id;
        $sql2="update city set is_default=1 where id=:id";
        $rst=yii::$app->db->createCommand($sql2, $params)->execute();
        return $rst;
    }
}


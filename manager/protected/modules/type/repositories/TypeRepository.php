<?php
namespace app\modules\type\repositories;
use app\entities\merchant\SellerType;
use app\modules\RepositoryBase;
class TypeRepository extends RepositoryBase{

    public function getTypeList(){

            $res = SellerType::find()->select("id,name as treeText,code,is_display")->where(['parent_id' =>0,'is_set_type'=>1])->orderBy('orderby asc')->asArray()->all();
            foreach ($res as $k => &$v) {
                $shi = SellerType::find()->select("id,name as treeText")->where(['type' => 1, 'parent_id' => $v['id']])->asArray()->all();
                $v['childNode'] = $shi;
            }
            return $res;
        }



    public function saveType($stype){
        if (empty($stype)) {
            throw new \InvalidArgumentException('$stype');
        }
        return  $stype->save();

    }

    public function deleteType($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        $rst=$this->findSon($id);
        if($rst){
            return false;
        }else {
            $params[':id']=$id;
            $sql="delete from seller_type where id=:id";
            $db = SellerType::getDb();
            return $db->createCommand($sql,$params)->execute() ;

        }

    }

    public function findSellerType($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  SellerType::findOne($id);


    }

    public function getParentType()
    {
        $res = SellerType::find()->select("id,name,code")->where(['parent_id' => 0,'is_set_type'=>1])->andWhere("app_code != 'all'")->orderBy(['code' => SORT_ASC])->asArray()->all();
        return $res;
    }

    public function findSellerSon($id){
        $res = SellerType::find()->select("id,name")->where(['parent_id' =>$id])->asArray()->all();
        return $res;

    }

    public function findMaxCode(){
        $res = SellerType::find()->max("code ");
        return $res;
    }

    public function findSon($id){
        $res = SellerType::find()->select("id,name,code")->where(['parent_id' =>$id])->orderBy(['code' => SORT_ASC])->asArray()->all();
        return $res;
    }

    public function setDisplay($id,$is_display){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        $params[':id']=$id;
        $params[':is_display']=$is_display;
        $sql="update seller_type set is_display=:is_display  where id=:id";
        $db = SellerType::getDb();
        return $db->createCommand($sql,$params)->execute() ;
    }



}
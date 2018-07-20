<?php

namespace app\modules\basic\repositories;
use app\entities\TParameterValue;
use app\modules\RepositoryBase;
class TParameterValueRepository extends RepositoryBase
{
    //团购关闭时间展示
    public function getGroupTime($account_id,$code){
        if(empty($account_id)){
            throw new \InvalidArgumentException('$account_id');
        }
       return  TParameterValue::find()->where(['account_id'=>$account_id,'is_deleted'=>0,'code'=>$code])->select('id,value')->one();
    }

    public function getParameter($id,$account_id){
        if(empty($id)){
            throw new \InvalidArgumentException('$id');
        }
        if(empty($account_id)){
            throw new \InvalidArgumentException('$account_id');
        }
        return TParameterValue::find()->where(['id'=>$id,'account_id'=>$account_id,'is_deleted'=>0])->one();
    }

    public function save($tParameter){
        if(empty($tParameter)){
            throw new \InvalidArgumentException('$tParameter');
        }
        return $tParameter->save();
    }
}
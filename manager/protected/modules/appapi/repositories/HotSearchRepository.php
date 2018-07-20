<?php
namespace app\modules\appapi\repositories;
use app\entities\member\HotSearch;
use app\modules\RepositoryBase;


class HotSearchRepository extends RepositoryBase{


    public function saveHotSearch($keywords){

        $data=HotSearch::find()->where(['keyword'=>$keywords])->one();
        if(empty($data)){
            $hotSearch=New HotSearch();
            $hotSearch->keyword=$keywords;
            $hotSearch->total=1;
            $hotSearch->created_on=date('Y-m-d H:i:s');
            $hotSearch->save();

        }else{
            $params[':keyword']=$keywords;
            $sql="update p_hot_search set total=total+1 where keyword=:keyword ";
            $db = HotSearch::getDb();
             $db->createCommand($sql, $params)->execute();
        }

        return 1;

    }

    public function getHotSearch(){
       $sql="select id,keyword,total from p_hot_search order by total desc limit 10";
        $db = HotSearch::getDb();
       return  $db->createCommand($sql)->queryAll();
    }


}

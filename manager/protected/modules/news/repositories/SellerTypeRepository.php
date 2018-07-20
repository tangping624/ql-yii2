<?php
namespace app\modules\news\repositories;
use app\entities\merchant\SellerType;
use app\modules\RepositoryBase;
class SellerTypeRepository extends RepositoryBase
{
    public function getType(){

       // return SellerType::find()->select('id,name')->where(['type'=>0,'is_set_type'=>1,'app_code'=>'tour'])->asArray()->all();
        $sql="select id,name from seller_type where type=0 and is_set_type=1 and app_code !='all' order by code ";
        $db= SellerType::getDb();
        return $db->createCommand($sql)->queryAll();
    }

}

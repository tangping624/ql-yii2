<?php
namespace app\modules\news\repositories;
use app\entities\merchant\SellerType;
use app\modules\RepositoryBase;
class SellerTypeRepository extends RepositoryBase
{
    public function getType(){

        return SellerType::find()->select('id,name')->where(['type'=>0,'is_set_type'=>1])->asArray()->all();
    }

}

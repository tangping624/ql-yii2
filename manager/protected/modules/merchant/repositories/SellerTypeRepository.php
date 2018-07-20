<?php
namespace app\modules\merchant\repositories;
use app\entities\merchant\SellerType;
use app\framework\db\PageResult;
use app\modules\RepositoryBase;
class SellerTypeRepository extends RepositoryBase
{
    public function getmerchantPType($pid){

        return SellerType::find()->select('id,name,type')->where(['id'=>$pid])->asArray()->one();
    }

    public function getmerchantType($id){

        return SellerType::find()->select('id,name,type')->where(['id'=>$id])->asArray()->one();
    }
}

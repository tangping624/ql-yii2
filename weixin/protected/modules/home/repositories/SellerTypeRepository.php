<?php
namespace app\modules\home\repositories;
use app\entities\merchant\SellerType;
use app\framework\db\PageResult;
use app\modules\RepositoryBase;
class SellerTypeRepository extends RepositoryBase
{
    //获取分类
    public function getSellerType()
    {
        return SellerType::find()->where(['type'=>0,'is_display'=>1])->orderBy('orderby ASC')->asArray()->all();
    }


}

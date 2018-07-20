<?php 
namespace app\modules\home\repositories;
use app\entities\merchant\SellerType;
use app\modules\RepositoryBase;


class AllRepository extends RepositoryBase{


    //获取全部分类信息
    public function getTypeInfo()
    {
        $res = SellerType::find()->select("id,name as treeText,code,icon,app_code")->where(['parent_id' =>0,'is_set_type'=>1,])->andWhere("app_code <> 'all' ")->orderBy(['code' => SORT_ASC])->asArray()->all();
        foreach ($res as $k => &$v) {
            $shi = SellerType::find()->select("id,name as treeText,app_code")->where(['type' => 1, 'parent_id' => $v['id']])->asArray()->all();
            $v['childNode'] = $shi;
        }
        return $res;
    }


}

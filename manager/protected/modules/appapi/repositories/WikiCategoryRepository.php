<?php
namespace app\modules\appapi\repositories;
use app\entities\baike\MWikiCategory;
use app\modules\RepositoryBase;
class WikiCategoryRepository extends RepositoryBase{

    //获取百科分类
    public function getWikiType()
    {
        return MWikiCategory::find()->select('id,name')->orderBy('created_on ASC')->asArray()->all();
    }

}
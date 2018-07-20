<?php
namespace app\modules\wiki\repositories;
use app\entities\baike\MWikiCategory;
use app\modules\RepositoryBase;
use app\framework\db\PageResult;
use yii\helpers\VarDumper;
use app\framework\utils\DateTimeHelper;
class WikiCategoryRepository extends RepositoryBase{

    //获取百科分类
    public function getWikiType()
    {
        return MWikiCategory::find()->select('id,name')->orderBy('created_on ASC')->asArray()->all();
    }

}
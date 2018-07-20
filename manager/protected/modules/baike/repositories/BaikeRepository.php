<?php
namespace app\modules\baike\repositories;
use app\modules\RepositoryBase;
use app\entities\baike\MWikiCategory;
use app\entities\baike\MWikiInfo;
use yii;
class BaikeRepository  extends RepositoryBase
{

    public function getBaikeTYpe(){

      return MWikiCategory::find()->select('id,name,created_on')->orderBy('created_on desc')->all();

    }

    public function getEnitity($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return MWikiCategory::findOne($id);
    }

    public function saveType( $category){
        if (empty( $category)) {
            throw new \InvalidArgumentException(' $category');
        }
        return $category->save();
    }

    public function deleteType($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        $params[':id']=$id;
        $sql="delete from m_wiki_category where id=:id";
        $db =MWikiCategory::getDb();
        return $db->createCommand($sql,$params)->execute() ;
    }

}


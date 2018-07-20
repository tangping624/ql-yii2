<?php
namespace app\modules\wiki\repositories;
use app\entities\baike\MWikiInfo;
use app\modules\RepositoryBase;
use app\framework\db\PageResult;
use yii\helpers\VarDumper;
use app\framework\utils\DateTimeHelper;
class WikiInfoRepository extends RepositoryBase{

    //百科列表
    public function getWikiList($skip,$limit,$id,$keywords){
        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $where='';
        if(!empty($id)){
            $where.=" where a.wiki_category_id=:id";
            $params[':id'] = $id;
        }
        if(!empty($keywords)){
            $where.= " and  (a.title like CONCAT('%', :name, '%'))";
            $params[':name'] = $keywords;
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,a.wiki_category_id,a.title,a.content,a.created_on,a.logo,b.`name` FROM m_wiki_info a INNER JOIN m_wiki_category b ON a.wiki_category_id=b.id ".$where." ORDER BY created_on DESC  LIMIT :skip,:limit";
        $params[':skip']=$skip;
        $params[':limit'] = $limit;
        $pageResult = new PageResult();
        $db = MWikiInfo::getDb();
        $cmd = $db->createCommand($sql,$params) ;
        $pageResult->items = $cmd->queryAll();
        $sql = "SELECT FOUND_ROWS() AS count;";
        $rows = $db->createCommand($sql)->queryAll();
        $pageResult->total = $rows[0]["count"];
        return $pageResult;
    }

    //百科详情
    public function getDetails($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }

        return MWikiInfo::find()->select('id,wiki_category_id,title,content,created_on,logo')->where(['id'=>$id])->one();
    }
}
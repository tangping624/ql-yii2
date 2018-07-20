<?php
namespace app\modules\baike\repositories;
use app\modules\RepositoryBase;
use app\entities\baike\MWikiCategory;
use app\entities\baike\MWikiInfo;
use yii;
use app\framework\db\PageResult;
class ManageRepository  extends RepositoryBase
{


    //广告管理列表
    public function getBaikeList($skip, $limit,$keyword)
    {
        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $where='';
        if(!empty($keyword)){
            $where.=" where a.title like CONCAT('%', :keyword, '%')";
            $params[':keyword'] = $keyword;
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,a.title,a.content,a.logo,a.created_on,b.name from m_wiki_info a left join m_wiki_category b on a.wiki_category_id=b.id ". $where." ORDER BY a.created_on desc LIMIT :skip,:limit";
        $params[':skip'] = $skip;
        $params[':limit'] = $limit;
        $pageResult = new PageResult();
        $db = MWikiInfo::getDb();
        $cmd = $db->createCommand($sql, $params);
        $pageResult->items = $cmd->queryAll();
        $sql = "SELECT FOUND_ROWS() AS count;";
        $rows = $db->createCommand($sql)->queryAll();
        $pageResult->total = $rows[0]["count"];
        return $pageResult;

    }

    public function save($entiy)
    {
        if (!isset($entiy)) {
            throw new \InvalidArgumentException('$entiy');
        }
        return $entiy->save();
    }

    public function getWiki($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return MWikiInfo::findOne($id);
    }

    public function getCategory()
    {
        return MWikiCategory::find()->select('id,name')->orderBy('created_on')->all();
    }

    public function deleteWikiInfo($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return MWikiInfo::deleteAll(['id' => $id]);

    }

    //查找分类下是否存在信息
    public function getTypeWiki($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        $data=MWikiInfo::find()->select('id')->where(['wiki_category_id'=>$id])->all();
        if(!$data){
            return 1;
        }else{
            return 0;
        }

    }
}


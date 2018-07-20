<?php
namespace app\modules\appapi\repositories;
use app\entities\lobby\MNews;
use app\modules\RepositoryBase;
use app\framework\db\PageResult;
class NewsRepository extends RepositoryBase
{
    //新鲜事列表
    public function getNewsList($skip, $limit, $id)
    {
        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $where = '';
        if (!empty($id)) {
            $where .= " where type_id=:id";
            $params[':id'] = $id;
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS id,type_id,title,photo,source,member_id,content,created_on FROM m_news " . $where . " ORDER BY created_on DESC LIMIT :skip,:limit";
        $params[':skip'] = $skip;
        $params[':limit'] = $limit;
        $pageResult = new PageResult();
        $db = MNews::getDb();
        $cmd = $db->createCommand($sql, $params);
        $pageResult->items = $cmd->queryAll();
        $sql = "SELECT FOUND_ROWS() AS count;";
        $rows = $db->createCommand($sql)->queryAll();
        $pageResult->total = $rows[0]["count"];
        return $pageResult;
    }


    public function getNew($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        $params[':id'] = $id;
        $sql="select a.id,a.title,a.photo,a.content,a.created_on,b.name from m_news a left join seller_type b on a.type_id=b.id where a.id=:id";
        $db = MNews::getDb();
        return  $db->createCommand($sql, $params)->queryOne();

    }
}

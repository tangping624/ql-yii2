<?php
namespace app\modules\news\repositories;
use app\entities\lobby\MNews;
use app\modules\RepositoryBase;
use app\framework\db\PageResult;
class NewsRepository extends RepositoryBase
{
    //新鲜事列表
    public function getNewsList($skip, $limit,$keyword)
    {
        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $where='';
        if(!empty($keyword)){
            $where.=" where a.title like CONCAT('%', :keyword, '%') or c.name  like CONCAT('%', :keyword, '%')";
            $params[':keyword'] = $keyword;
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,a.title,a.content,a.photo,a.created_on,a.source,a.type_id,a.member_id,b.name,c.name AS typename,c.name AS typename,d.name AS uname from m_news a LEFT JOIN h_member b ON a.member_id=b.id  LEFT JOIN t_user d ON a.member_id=d.id INNER JOIN seller_type c ON a.type_id=c.id ".$where." ORDER BY a.created_on desc LIMIT :skip,:limit";
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

    public function save($entiy)
    {
        if (!isset($entiy)) {
            throw new \InvalidArgumentException('$entiy');
        }
        return $entiy->save();
    }

    public function getNew($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return MNews::findOne($id);
    }
    
    //新鲜事删除
    public function deleteNews($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return MNews::deleteAll(['id' => $id]);

    }
}
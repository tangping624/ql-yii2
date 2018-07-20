<?php
namespace app\modules\lobby\repositories;

use app\entities\lobby\MBlog;
use app\modules\RepositoryBase;
use app\framework\db\PageResult;
class LobbyRepository extends RepositoryBase
{

    //游说管理列表
    public function getBlogList($skip, $limit,$keyword)
    {
        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $where='';
        if(!empty($keyword)){
            $where.=" where a.title like CONCAT('%', :keyword, '%')";
            $params[':keyword'] = $keyword;
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,a.title,a.content,a.photo,a.source,a.created_on,b.name,c.name as cname from m_blog a left  join  h_member b on  a.member_id=b.id left join t_user c on a.member_id=c.id ". $where." ORDER BY created_on desc LIMIT :skip,:limit";
        $params[':skip'] = $skip;
        $params[':limit'] = $limit;
        $pageResult = new PageResult();
        $db = MBlog::getDb();
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
        return MBlog::findOne($id);
    }


    public function deleteWikiInfo($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return MBlog::deleteAll(['id' => $id]);

    }
}
<?php
namespace app\modules\member\repositories; 
use app\modules\RepositoryBase; 
use app\entities\member\HMember;
use app\framework\db\PageResult;
use app\framework\utils\DateTimeHelper;
class MemberRepository extends RepositoryBase
{
    //会员管理列表
    public function getMemberList($skip, $limit,$Keywords)
    {
        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $where = " ";
        $params = [];
        if(!empty($Keywords)) {
            $where .= " WHERE (name like CONCAT('%', :Keywords, '%') or mobile like CONCAT('%', :Keywords, '%')) " ;
            $params[':Keywords'] = $Keywords;
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS name,mobile,join_date,sex,headimg_url FROM h_member " .$where . "  ORDER BY created_on DESC limit :skip,:limit";
        $params[':skip']=$skip;
        $params[':limit'] = intval($limit);
        $pageResult = new PageResult();
        $db = HMember::getDb();
        $cmd = $db->createCommand($sql,$params) ; 
        $pageResult->items = $cmd->queryAll();
        $sql = "SELECT FOUND_ROWS() AS count;";
        $rows = $db->createCommand($sql)->queryAll();
        $pageResult->total = $rows[0]["count"];   
        return $pageResult;
    }


}

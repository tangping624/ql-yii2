<?php
namespace app\modules\appapi\repositories;
use app\entities\merchant\SellerType;
use app\entities\merchant\SMerchant;
use app\framework\db\EntityBase;
use app\modules\RepositoryBase;
use app\framework\db\PageResult;
class SMerchantRepository extends RepositoryBase
{
    //获取推荐商家
    public function getSellerRecommend($cityId)
    {
        $sql = "SELECT
                    a.id,
                    a.logo,
                    a.`name`,
                    a.summary,
                    b.app_code,
                    b.id as type_pid
                FROM
                    s_merchant a
                LEFT JOIN seller_type b ON a.type_pid = b.id

                where a.is_deleted = 0
                AND a.is_recommend = 1 ORDER BY a.sort ASC limit 20";
        //$param[':cityId'] = $cityId;
        //echo SMerchant::getDb()->createCommand($sql, $param)->getRawSql();die;
        $rs = SMerchant::getDb()->createCommand($sql)->queryAll();
        return $rs;
    }

    //获取商家列表(房产、购物、特产、旅游)
    public function getSellerList($cityPid='',$typePid='',$skip,$limit,$appcode)
    {
        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
      //  $params[':type_pid'] = $typePid;
       // $params[':city_pid'] = $cityPid;
        $params[':skip'] = intval($skip);
        $params[':limit'] = intval($limit);
        $params[':app_code'] = $appcode;

        $sql = "SELECT a.id, a.`name`, a.logo, a.summary,b.app_code,b.id as type_pid FROM s_merchant a
                LEFT JOIN seller_type b ON a.type_pid = b.id 
                where a.is_deleted = 0 and b.app_code=:app_code
                ORDER BY a.sort ASC LIMIT :skip,:limit";
        $pageResult = new PageResult();
        $db = EntityBase::getDb();
        //echo $db->createCommand($sql, $params)->getRawSql();die;
        $cmd = $db->createCommand($sql, $params);
        $pageResult->items = $cmd->queryAll();
        //$sql = "SELECT FOUND_ROWS() AS count;";
        //$rows = $db->createCommand($sql)->queryAll();
        //$pageResult->total = $rows[0]["count"];
        return $pageResult;
    }

    public function getSearchList($skip, $limit,$type,$keywords)
    {
        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $sql='';
        if($type==1){
            $params[':name'] = $keywords;
            $sql="select SQL_CALC_FOUND_ROWS id,app_code,name,seller_id,logo from s_goods where name like CONCAT('%', :name, '%') ORDER BY created_on desc LIMIT :skip,:limit";
        }
        if($type==2){
            $params[':name'] = $keywords;
            $sql="SELECT SQL_CALC_FOUND_ROWS a.name, a.logo, a.id, a.address,a.summary,a.type_pid, b.name AS nametype,b.app_code FROM s_merchant a LEFT JOIN seller_type b ON a.type_pid = b.id WHERE a.is_deleted = 0 and a.name like CONCAT('%', :name, '%') ORDER BY a.created_on desc LIMIT :skip,:limit";
        }
       if($type==3){
           $params[':title'] = $keywords;
            $sql="SELECT SQL_CALC_FOUND_ROWS a.id,a.title,a.photo,a.content,a.ll_sum,b.headimg_url,b.name from m_blog a left join h_member b on a.member_id=b.id where a.title like CONCAT('%', :title, '%') ORDER BY a.created_on desc LIMIT :skip,:limit";
       }
        $params[':skip'] = $skip;
        $params[':limit'] = $limit;
        $pageResult = new PageResult();
        $db = SMerchant::getDb();
        $re = $db->createCommand($sql, $params)->queryAll();
        $sql = "SELECT FOUND_ROWS() AS count;";
        $rows = $db->createCommand($sql)->queryAll();
        $pageResult->total = $rows[0]["count"];
        $pageResult->items = $re;
        return $pageResult;
    }
}
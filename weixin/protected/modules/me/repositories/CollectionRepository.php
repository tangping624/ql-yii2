<?php
namespace app\modules\me\repositories;
use app\entities\merchant\Collection;
use app\modules\RepositoryBase;
use app\framework\db\PageResult;
class CollectionRepository extends RepositoryBase
{

    public function getTrack( $skip,$limit,$memberId,$type){

        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $sql='';
        if($type==1){
            $sql="SELECT SQL_CALC_FOUND_ROWS a.id, a.created_on, a.seller_id, b. NAME, b.logo,b.summary,c.app_code FROM ( SELECT id, seller_id, created_on FROM collection WHERE member_id =:memberId AND type = 3 AND product_id IS NULL GROUP BY created_on ) a LEFT JOIN s_merchant b ON a.seller_id = b.id  LEFT JOIN seller_type c ON b.type_pid = c.id where b.is_deleted=0 GROUP BY a.created_on DESC LIMIT :skip,:limit";
        }
        if($type==2){
           $sql="SELECT SQL_CALC_FOUND_ROWS a.id, a.created_on, a.product_id, b. NAME, b.logo, b.summary, b.content,b.seller_id FROM ( SELECT id, product_id, created_on FROM collection WHERE member_id = :memberId AND type = 3 AND product_id IS NOT NULL GROUP BY created_on ) a LEFT JOIN s_goods b ON a.product_id = b.id GROUP BY a.created_on ORDER BY a.created_on DESC LIMIT :skip,:limit";
        }
        $params[':skip']=$skip;
        $params[':limit'] = (int)$limit;
        $params[':memberId']=$memberId;
        $pageResult = new PageResult();
        $db = Collection::getDb();
        $cmd = $db->createCommand($sql,$params) ;
        $pageResult->items = $cmd->queryAll();
        $sql = "SELECT FOUND_ROWS() AS count;";
        $rows = $db->createCommand($sql)->queryAll();
        $pageResult->total = $rows[0]["count"];
        return $pageResult;


    }


    public function getCollection( $skip,$limit,$memberId,$type){

        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $sql='';
        if($type==1){
            $sql="SELECT SQL_CALC_FOUND_ROWS a.id, a.created_on, a.seller_id, b. NAME, b.logo,b.summary,c.app_code FROM ( SELECT id, seller_id, created_on FROM collection WHERE member_id =:memberId AND type = 1 AND product_id IS NULL GROUP BY created_on ) a LEFT JOIN s_merchant b ON a.seller_id = b.id LEFT JOIN seller_type c ON b.type_pid = c.id  where b.is_deleted=0 GROUP BY a.created_on  DESC LIMIT :skip,:limit";
        }
        if($type==2){
            $sql="SELECT SQL_CALC_FOUND_ROWS a.id, a.created_on, a.product_id, b. NAME, b.logo, b.summary, b.content,b.seller_id FROM ( SELECT id, product_id, created_on FROM collection WHERE member_id = :memberId AND type = 1 AND product_id IS NOT NULL GROUP BY created_on ) a LEFT JOIN s_goods b ON a.product_id = b.id GROUP BY a.created_on ORDER BY a.created_on DESC LIMIT :skip,:limit";
        }
        if($type==3){
            $sql="SELECT SQL_CALC_FOUND_ROWS a.id as mid, a.title as NAME, a.photo as logo,a.content,b.id  FROM m_blog a inner JOIN ( SELECT id, seller_id, created_on FROM collection WHERE member_id = :memberId  AND type = 1 AND product_id IS NULL GROUP BY created_on ) b ON a.id = b.seller_id ORDER BY a.created_on DESC LIMIT :skip,:limit";
        }
        $params[':skip']=$skip;
        $params[':limit'] = $limit;
        $params[':memberId']=$memberId;
        $pageResult = new PageResult();
        $db = Collection::getDb();
        $cmd = $db->createCommand($sql,$params) ;
        $pageResult->items = $cmd->queryAll();
        $sql = "SELECT FOUND_ROWS() AS count;";
        $rows = $db->createCommand($sql)->queryAll();
        $pageResult->total = $rows[0]["count"];
        return $pageResult;


    }
    //type=1 为商家,type=2为商品,type=3为游说
    public function getPraise( $skip,$limit,$memberId,$type){

        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $sql='';
        if($type==1){
            $sql="SELECT SQL_CALC_FOUND_ROWS a.id, a.created_on, a.seller_id, b. NAME, b.logo,b.summary,c.app_code FROM ( SELECT id, seller_id, created_on FROM collection WHERE member_id =:memberId AND type = 2 AND product_id IS NULL GROUP BY created_on ) a LEFT JOIN s_merchant b ON a.seller_id = b.id LEFT JOIN seller_type c ON b.type_pid = c.id  where b.is_deleted=0 GROUP BY a.created_on DESC LIMIT :skip,:limit";
        }
        if($type==2){
            $sql="SELECT SQL_CALC_FOUND_ROWS a.id, a.created_on, a.product_id, b. NAME, b.logo, b.summary, b.content,b.seller_id FROM ( SELECT id, product_id, created_on FROM collection WHERE member_id = :memberId AND type = 2 AND product_id IS NOT NULL GROUP BY created_on ) a LEFT JOIN s_goods b ON a.product_id = b.id GROUP BY a.created_on ORDER BY a.created_on DESC LIMIT :skip,:limit";
        }
        if($type==3){
            $sql="SELECT SQL_CALC_FOUND_ROWS a.id as mid, a.title as NAME , a.photo as logo ,a.content,b.id  FROM m_blog a inner JOIN ( SELECT id, seller_id, created_on FROM collection WHERE member_id = :memberId  AND type = 2 AND product_id IS NULL GROUP BY created_on ) b ON a.id = b.seller_id ORDER BY a.created_on DESC LIMIT :skip,:limit";
        }
        $params[':skip']=$skip;
        $params[':limit'] = $limit;
        $params[':memberId']=$memberId;
        $pageResult = new PageResult();
        $db = Collection::getDb();
        $cmd = $db->createCommand($sql,$params) ;
        $pageResult->items = $cmd->queryAll();
        $sql = "SELECT FOUND_ROWS() AS count;";
        $rows = $db->createCommand($sql)->queryAll();
        $pageResult->total = $rows[0]["count"];
        return $pageResult;


    }

    public function deleteTrack($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return Collection::deleteAll(['id' => $id]);

    }
}
<?php
namespace app\modules\lobby\repositories;

use app\entities\lobby\MBlog;
use app\modules\RepositoryBase;
use app\framework\db\PageResult;
use app\entities\merchant\Collection;
class LobbyRepository extends RepositoryBase
{

    //游说管理列表
    public function getBlogList($skip, $limit)
    {
        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,a.title,a.photo,a.content,a.ll_sum,a.dz_num,a.sc_num,b.headimg_url,b.name from m_blog a left join h_member b on a.member_id=b.id  ORDER BY a.created_on desc LIMIT :skip,:limit";
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

    public function updateQuantity($id){
        $params[':id'] = $id;
        $sql="update m_blog set ll_sum=ll_sum+1 where id=:id";
        $db = MBlog::getDb();
        return $db->createCommand($sql, $params)->execute();
    }

    public function getDetails($id, $memberId)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        $params[':id'] = $id;
        $sql = "select a.id,a.title,a.photo,a.source,a.content,a.ll_sum,a.created_on,b.headimg_url,b.name from m_blog a left join h_member b on a.member_id=b.id where a.id=:id";
        $db = MBlog::getDb();
       $data= $db->createCommand($sql, $params)->queryOne();
        $arr1=Collection::find()->select('seller_id,type')->where(['seller_id'=>$id,'member_id'=> $memberId,'type'=>1,'product_id'=>null])->asArray()->one();
        $arr2=Collection::find()->select('seller_id,type')->where(['seller_id'=>$id,'member_id'=> $memberId,'type'=>2,'product_id'=>null])->asArray()->one();
        $data['collection']= $arr1['type'];
        $data['prise']= $arr2['type'];
        return $data;


    }

    public function clickPraise($id,$memberId,$type){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        $collection=new Collection();
        $collection->seller_id=$id;//游说id
        $collection->member_id=$memberId;
        $collection->type=$type;
        $collection->created_on=date('Y-m-d H:i:s');
        return   $collection->save();

    }

    public function saveBlog($blog){
        if (empty($blog)) {
            throw new \InvalidArgumentException('$blog');
        }
        return $blog->save();
    }

    public function getLobbyList($skip,$limit,$memberId){
        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $sql = "select id, title, photo,content from m_blog where member_id=:memberId  ORDER BY created_on DESC LIMIT :skip,:limit";
        $params[':skip'] = $skip;
        $params[':limit'] = $limit;
        $params[':memberId'] = $memberId;
        $pageResult = new PageResult();
        $db = MBlog::getDb();
        $cmd = $db->createCommand($sql, $params);
        $pageResult->items = $cmd->queryAll();
        $sql = "SELECT FOUND_ROWS() AS count;";
        $rows = $db->createCommand($sql)->queryAll();
        $pageResult->total = $rows[0]["count"];
        return $pageResult;
    }

    public function getLobby($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }

        return  MBlog::findOne($id);
    }

    public function deleteLobby($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return MBlog::deleteAll(['id' => $id]);

    }
}
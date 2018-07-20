<?php
namespace app\modules\appapi\repositories;

use app\entities\merchant\SMerchant;
use app\entities\merchant\SImages;
use app\modules\RepositoryBase;
use app\framework\db\PageResult;
use yii;
use app\entities\merchant\Collection;
class SellerRepository extends RepositoryBase
{

    //商家列表
    public function getBlogList($skip, $limit,$type_pid,$type_id,$city_id,$keyword,$city_pid)
    {
        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $params[':type_pid'] = $type_pid;
        $where='where is_deleted=0 and type_pid=:type_pid';
        $order='';
        if(!empty($type_id)){
            $where.=" and type_id=:type_id";
            $params[':type_id'] = $type_id;
        }
        //好评排序
        if($keyword==3){
            $order.=" dz_num desc, ";
        }
        //收藏排序
        if($keyword==4){
            $order.=" sc_num desc, ";
        }
       /* if(!empty($city_id)){
            $where.=" and city_id=:city_id";
            $params[':city_id'] = $city_id;
        }*/
        if(!empty($city_pid)){
            $where.=" and city_pid=:city_pid";
            $params[':city_pid'] = $city_pid;
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS id,  NAME,dz_num,sc_num,logo, summary, created_on,logo,type_id,type_pid  FROM s_merchant ".$where." ORDER BY ".$order." sort asc, created_on desc LIMIT :skip,:limit";
        $params[':skip']=$skip;
        $params[':limit'] = $limit;
        $pageResult = new PageResult();
        $db = SMerchant::getDb();
        $cmd = $db->createCommand($sql,$params) ;
        $pageResult->items = $cmd->queryAll();
        $sql = "SELECT FOUND_ROWS() AS count;";
        $rows = $db->createCommand($sql)->queryAll();
        $pageResult->total = $rows[0]["count"];
        return $pageResult;
    }


    public function getDetails($id, $memberId)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        $params[':id']=$id;
        $sql="SELECT id, NAME, linkman, linktel, address, latitudes, longitudes, remind, content, summary,mail,fax, dz_num,logo, '' as collection,'' as prise FROM s_merchant  WHERE id = :id";
        $db = SMerchant::getDb();
        $data = $db->createCommand($sql,$params)->queryOne() ;
        $arr1=Collection::find()->select('seller_id,type')->where(['seller_id'=>$id,'member_id'=> $memberId,'type'=>1,'product_id'=>null])->asArray()->one();
        $arr2=Collection::find()->select('seller_id,type')->where(['seller_id'=>$id,'member_id'=> $memberId,'type'=>2,'product_id'=>null])->asArray()->one();
        $data['collection']= $arr1['type'];
        $data['prise']= $arr2['type'];
        return $data;


    }

    public function getImages($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return SImages::find()->select('id,original_url,thumb_url')->where(['fid'=>$id])->all();
    }

    public function clickPraise($id,$memberId,$type,$product_id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        $collection=new Collection();
        $collection->seller_id=$id;
        $collection->member_id=$memberId;
        $collection->type=$type;
        $collection->created_on=date('Y-m-d H:i:s');
        if(!empty($product_id)){
            $collection->product_id=$product_id;
        }
        return   $collection->save();

    }

    public function IsPraise($id,$memberId,$type){

       $data= Collection::find()->where(['seller_id'=>$id,'member_id'=>$memberId,'type'=>$type])->one();
        if(!$data){
            return 1;
        }else{
            return 0;
        }
    }

    public function getCount($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        $params[':id'] = $id;
        $sql="select count(*) as quantity from collection where seller_id=:id  and product_id is null and type=2";
        $db = SMerchant::getDb();
        return  $db->createCommand($sql,$params)->queryOne() ;

    }

    //取消收藏商家
    public function cancelCollection($memberId,$id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }

        return  Collection::deleteAll(['member_id'=>$memberId,'seller_id'=>$id,'type'=>1]);
    }

    //取消收藏商品
    public function cancelCollectionGoods($memberId,$id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }

        return  Collection::deleteAll(['member_id'=>$memberId,'product_id'=>$id,'type'=>1]);
    }

    //点赞/收藏加1
    public function setAddOne($id,$type)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        $params=[];
        $params[':id']=$id;
        $sql='';
        if($type==1){
            $sql = "update s_merchant set sc_num=sc_num+1 WHERE id=:id AND is_deleted=0";
        }elseif ($type==2){
            $sql = "update s_merchant set dz_num=dz_num+1 WHERE id=:id AND is_deleted=0";
        }
        $data=Yii::$app->db->createCommand($sql,$params)->execute();
        return $data;
    }

    //收藏减1
    public function setSubtractOne($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        $data='';
        $model=SMerchant::find()->where(['is_deleted'=>0,'id'=>$id])->asArray()->one();
        if($model['sc_num']>0) {
            $params = [];
            $params[':id'] = $id;
            $sql = "update s_merchant set sc_num=sc_num-1 WHERE id=:id AND is_deleted=0";
            $data = Yii::$app->db->createCommand($sql, $params)->execute();
        }
        return $data;
    }

    public function getRoundMerchant($id,$type_pid){
        $params[':type_pid'] = $type_pid;
        $params[':id'] = $id;
        $sql="SELECT * FROM ( SELECT rand() AS rd, `name`, id, logo, summary, created_on, type_pid FROM s_merchant WHERE type_pid = :type_pid AND id != :id ) t WHERE rd BETWEEN 0 AND 1 ORDER BY t.rd ASC LIMIT 2";
        $data = Yii::$app->db->createCommand($sql, $params)->queryAll();
        return $data;
    }
}
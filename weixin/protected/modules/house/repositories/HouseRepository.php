<?php
namespace app\modules\house\repositories;
use app\entities\goods\SGoods;
use app\entities\merchant\EstateTag;
use app\modules\RepositoryBase;
use app\framework\db\PageResult;
use app\entities\merchant\SMerchant;
use app\entities\merchant\Collection;
use app\entities\city\City;
use yii;
class HouseRepository extends RepositoryBase
{
    //房产列表
    public function getHouseList($skip, $limit,$type_pid,$tag_id,$city_id,$keyword,$city_pid)
    {
        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $where=" WHERE is_deleted = 0 AND type_pid = :type_pid";
        $order='';
        if(!empty($city_id)){
            $params[':city_id'] = $city_id;
            $where.=" and city_id= :city_id";
        }
        if(!empty($city_pid)){
            $params[':city_pid'] = $city_pid;
            $where.=" and city_pid= :city_pid";
        }
        if(!empty($tag_id)){
            $params[':tag_id'] = $tag_id;
            $where.=" AND id IN ( SELECT seller_id FROM seller_to_tag WHERE estate_tag_id =:tag_id) ";
        }
        //好评排序
        if($keyword==3){
            $order.=" dz_num desc, ";
        }
        //收藏排序
        if($keyword==4){
            $order.=" sc_num desc, ";
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS id, NAME, logo, summary,sc_num,dz_num,created_on, logo, '' AS tags FROM s_merchant ".$where." ORDER BY ".$order." sort asc ,created_on desc LIMIT :skip,:limit";
        $params[':skip'] = $skip;
        $params[':limit'] = $limit;
        $params[':type_pid'] = $type_pid;
        $pageResult = new PageResult();
        $db = SMerchant::getDb();
        $re = $db->createCommand($sql, $params)->queryAll();

        $sql = "SELECT FOUND_ROWS() AS count;";
        $rows = $db->createCommand($sql)->queryAll();
        $pageResult->total = $rows[0]["count"];
        $sql2=" SELECT  a.seller_id, b. NAME FROM seller_to_tag a LEFT JOIN estate_tag b ON a.estate_tag_id = b.id order by seller_id";
        $arrTags=Yii::$app->db->createCommand($sql2)->queryAll();
        foreach ($re as $k=>$row){
            $seller_id= $row['id'];
            $row['tags'] =$this->getGoodsByArray($arrTags, $seller_id);
            $re[$k]=$row;
        }
        $pageResult->items = $re;
        return $pageResult;
    }

    
    //获取房产类别
    public function getHouseType()
    {
        return EstateTag::find()->orderBy('order_id ASC')->asArray()->all();
    }


    //产品列表
    public function getProductList($skip, $limit,$seller_id)
    {
        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS id, NAME, content, summary, created_on, logo FROM s_goods WHERE  seller_id =:seller_id ORDER BY created_on DESC LIMIT :skip,:limit";
        $params[':skip'] = $skip;
        $params[':limit'] = $limit;
        $params[':seller_id'] = $seller_id;
        $pageResult = new PageResult();
        $db = SGoods::getDb();
        $re = $db->createCommand($sql, $params)->queryAll();
        $pageResult->items = $re;
        $sql = "SELECT FOUND_ROWS() AS count;";
        $rows = $db->createCommand($sql)->queryAll();
        $pageResult->total = $rows[0]["count"];


        return $pageResult;
    }

    private function getGoodsByArray($arrTags, $seller_id){
        $arr = [];
        $sign = false;
        foreach ($arrTags as $row) {
            if ($row['seller_id'] == $seller_id) {
                $arr[] = $row;
                $sign = true;
            }
            if ($sign && $row['seller_id'] != $seller_id) {
                break;
            }
        }
        return $arr;
    }

    //产品详情
    public function getProductDetails($product_id,$memberId){
        if (empty($product_id)) {
            throw new \InvalidArgumentException('$product_id');
        }
        $params[':product_id']=$product_id;
        $sql="SELECT id, NAME,  content, summary, logo, '' as collection,'' as prise FROM s_goods  WHERE id = :product_id";
        $db = SMerchant::getDb();
        $data = $db->createCommand($sql,$params)->queryOne() ;
        $arr1=Collection::find()->select('product_id,type')->where(['product_id'=>$product_id,'member_id'=> $memberId,'type'=>1])->asArray()->one();
        $arr2=Collection::find()->select('product_id,type')->where(['product_id'=>$product_id,'member_id'=> $memberId,'type'=>2])->asArray()->one();
        $data['collection']= $arr1['type'];
        $data['prise']= $arr2['type'];
        return $data;

    }

    public function IsProductPraise($product_id,$memberId,$type){

        $data= Collection::find()->where(['product_id'=>$product_id,'member_id'=>$memberId,'type'=>$type])->one();
        if(!$data){
            return 1;
        }else{
            return 0;
        }
    }


}
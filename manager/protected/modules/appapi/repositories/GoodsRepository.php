<?php
namespace app\modules\appapi\repositories;
use app\entities\goods\SGoods;
use app\entities\merchant\SMerchant;
use app\modules\RepositoryBase;
use app\framework\db\PageResult;
use app\entities\merchant\Collection;
use app\entities\goods\GwhType;
class GoodsRepository extends RepositoryBase
{

    //获取购物惠分类
    public function getShopType($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        $params[':id'] = $id;
        $sql = "SELECT distinct b.id,b.name FROM s_goods a INNER JOIN gwh_type b ON a.type_id=b.id WHERE a.seller_id=:id";
        $db = GwhType::getDb();
        return $db->createCommand($sql, $params)->queryAll();
    }

    //商品列表
    public function getShopList($skip,$limit,$type_id,$seller_id){
        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $where='';
        if(!empty($type_id)){
            $where.=" and type_id=:type_id";
            $params[':type_id'] = $type_id;
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS id,name,logo from s_goods where seller_id=:seller_id ".$where." ORDER BY created_on DESC  LIMIT :skip,:limit";
        $params[':seller_id']=$seller_id;
        $params[':skip']=$skip;
        $params[':limit'] = $limit;
        $pageResult = new PageResult();
        $db = SGoods::getDb();
        $cmd = $db->createCommand($sql,$params) ;
        $pageResult->items = $cmd->queryAll();
        $sql = "SELECT FOUND_ROWS() AS count;";
        $rows = $db->createCommand($sql)->queryAll();
        $pageResult->total = $rows[0]["count"];
        return $pageResult;
    }

    //产品详情
    public function getProductDetails($product_id,$memberId){
        if (empty($product_id)) {
            throw new \InvalidArgumentException('$product_id');
        }
        $params[':product_id']=$product_id;
        $sql="SELECT id, name, seller_id, content, summary,app_code, logo, '' as collection,'' as prise FROM s_goods  WHERE id = :product_id";
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
<?php
namespace app\modules\merchant\repositories;
use app\entities\merchant\EstateTag;
use app\entities\merchant\SellerToTag;
use app\modules\RepositoryBase;
class SellerToTagRepository extends RepositoryBase
{

    public function saveSellerTag($seller_id, $tag_id)
    {
        if (empty($seller_id)) {
            throw new \InvalidArgumentException('$seller_id');
        }
        if (empty($tag_id)) {
            throw new \InvalidArgumentException('$tag_id');
        }
        $sellerTag = new SellerToTag();
        $sellerTag->seller_id=$seller_id;
        $sellerTag->estate_tag_id=$tag_id;
        return  $sellerTag ->save();

    }

    public function getTag(){
        return EstateTag::find()->select('*')->asArray()->all();
    }

    public function deleteByFid($fid) {
        if (empty($fid)) {
            return null;
        }
        return SellerToTag::deleteAll(['seller_id' => $fid]);
    }

    public function  findTagInfo($id){

        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        $params[':id']=$id;
        $sql="select a.estate_tag_id from seller_to_tag  a left join estate_tag b on b.id=a.estate_tag_id  where seller_id=:id";
        $db = SellerToTag::getDb();
       $data= $db->createCommand($sql,$params)->queryAll() ;
        $str='';
        foreach($data as $v){
            $str.=$v['estate_tag_id'].',';

        }
        $tsg=rtrim($str, ",");
        return $tsg;


    }
}
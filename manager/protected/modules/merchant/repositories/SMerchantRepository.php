<?php
namespace app\modules\merchant\repositories;
use app\entities\merchant\SellerType;
use app\entities\merchant\SMerchant;
use app\modules\RepositoryBase;
use app\framework\db\PageResult;
class SMerchantRepository extends RepositoryBase{

    //商家列表
    public function getSellerList($skip,$limit,$name){
        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $where='';
        if(!empty($name)){
            $where.=" and a.name like CONCAT('%', :name, '%')";
            $params[':name'] = $name;
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.name, a.linktel, a.linkman, a.id, a.address,a.sort, a.summary AS content, a.is_recommend, b.name AS nametype,b.app_code FROM s_merchant a LEFT JOIN seller_type b ON a.type_pid = b.id WHERE a.is_deleted = 0 ".$where." ORDER BY a.sort asc, created_on desc LIMIT :skip,:limit";
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

    //删除
    public function  setDeleted($id,$time,$userid){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        if (empty($userid)) {
            throw new \InvalidArgumentException('$userid');
        }
        return SMerchant::getDb()->createCommand()->update("s_merchant", ['is_deleted'=>1,'modified_on'=>$time,'modified_by'=>$userid], ['id' => $id])->execute();
    }


    public function  save( $entiy){
        if(!isset($entiy)){
            throw new \InvalidArgumentException('$entiy');
        }
        return $entiy->save();
    }

    public function changeGoodsIsShelves($id,$is_recommend,$userId,$time){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        if (empty($userId)) {
            throw new \InvalidArgumentException('$userId');
        }
        $re=SMerchant::getDb()->createCommand()->update("s_merchant", ['is_recommend'=>$is_recommend,'modified_by'=>$userId,'modified_on'=>$time], ['id' => $id])->execute();
        return $re;

    }

    public function getMerchantInfo($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
      return SMerchant::findOne($id);
    }

    //查找该分类下是否有商家
    public function getTypeSeller($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        $data=SMerchant::find()->select('id')->where(['type_id'=>$id,'is_deleted'=>0])->all();
        if(!$data){
            return 1;
        }else{

            return 0;
        }
    }
}
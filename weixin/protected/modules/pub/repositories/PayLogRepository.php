<?php
namespace app\modules\pub\repositories;
use app\repositories\RepositoryBase;
use yii\db\Query;
use app\entities\shop\SPayLog;
use \app\entities\order\SOrder;
class PayLogRepository extends RepositoryBase {
    /*
     * 保存日志
     */
    public function save(SPayLog $wxpaylog){
        if(is_null($wxpaylog) ){
            return false;
        } 
       return $wxpaylog->save();
    }
    /*
     *  获取日志信息 
     */
    public function getPayLog($id){
        if(empty($id) ){
          return  null;
        }
       return SPayLog::findOne(['id' =>$id]);
    }
     /**
     * @param $orderid
     * @return null|CPayLog
     */
    public function getPayLogByOrderId($orderid)
    {
        if (empty($orderid)) {
            return null;
        } 
        return SPayLog::findOne(['order_id' => $orderid]);
    } 
    
    public function updateOrderInfo($orderid,$paytime){
        if(empty($orderid)){
            throw new \InvalidArgumentException('$orderid');
        }
        if(empty($paytime)){
            throw new \InvalidArgumentException('$paytime');
        }
        $rst=SOrder::updateAll(array('status'=>1,'modified_on'=>$paytime),'id=:id',array(':id'=>$orderid));
        return $rst;
    }
}
<?php 
namespace app\modules\pub\controllers; 
use Yii; 
use app\modules\pub\services\PayLogService; 
use app\controllers\ControllerBase; 

 //第三方调用
class PayresultController extends ControllerBase
{
    public $enableCsrfValidation = false; 
    private $_payLogService;
    public function __construct($id,
                                $module,   PayLogService $payLogService,
                                $config = [])
    {
         
        $this->_payLogService=$payLogService;
        parent::__construct($id, $module, $config);
    } 

    /**
     * 支付完成后回调的入口
     * @return type
     */
    public function actionSuccess($public_id,$memberid)
    {
        try {
            $order_no = $_REQUEST['out_trade_no'];
            return $this->renderPartial('paycallback', ['order_no'=>$order_no,'public_id' =>$public_id,'memberid'=>$memberid]);
        } catch (Exception $ex) {
            \Yii::error("生成支付订单失败:".$ex->getMessage());
            return $this->json(['result'=>false,'code' => 500, 'msg' => $ex->getMessage()]);
        }
    }
    public function actionFirm(){
        try {
            $order_no = $_REQUEST['order_no'];
            $public_id = $_REQUEST['public_id'];
            $memberid=$_REQUEST['memberid'];
            // $accountId = $this->context->publicId;
            $result = $this->_payLogService->resultPayLogWx($order_no, $public_id,$memberid);
            $payLog = $this->_payLogService->getPayLog($order_no);
            return $this->renderPartial('payresult', ['ok' => $result['trade_state'] === 'SUCCESS','public_id'=>$payLog->account_id ,'openid'=>$payLog->openid,'orderid' => $payLog->order_id]);
        } catch (Exception $ex) {
            \Yii::error("生成支付订单失败:".$ex->getMessage());
            return $this->json(['result'=>false,'code' => 500, 'msg' => $ex->getMessage()]);
        }

    }

}
 

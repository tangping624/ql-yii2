<?php  
namespace app\modules\pub\services;
require_once(dirname(__FILE__) . '/../../../framework/3rd/wxpay/lib/WxPay.Api.php');
use app\entities\HMember;
use app\entities\member\HMemberScore;
use app\framework\biz\bizparam\models\TParameterValue;
use app\modules\member\repositories\MemberScoreRepository;
use WxPayApi;
use app\framework\utils\WebUtility; 
use app\framework\utils\DateTimeHelper;
use app\framework\utils\StringHelper;
use app\modules\pub\repositories\PayLogRepository;
use \app\entities\order\SOrder;
use app\entities\shop\SPayLog;
use app\modules\pub\services\WeixinPayService;
use app\modules\shop\repositories\TParameterValueRepository;
use app\modules\member\repositories\MemberRepository;

class PayLogService
{
    /**
     * 创建订单
     * @param type $orderid
     * @param type $money
     * @return type
     */
    public static function creatPayLog($orderid, $money, $memberid = '', $openid = '', $accountId)
    {
        $wxpaylog = new SPayLog();
        $wxpaylog->id = str_replace('-', '', StringHelper::uuid());
        $wxpaylog->trade_state = '';
        $wxpaylog->payed_fee = $money;
        $wxpaylog->order_id = $orderid;
        $wxpaylog->account_id = $accountId;
        $wxpaylog->openid = $openid;
        $wxpaylog->pay_result = '';
        $wxpaylog->pay_type = "1";
        $wxpaylog->fee_type = '1';
        $wxpaylog->time_end = DateTimeHelper::now();
        $wxpaylog->created_on = DateTimeHelper::now();
        $wxpaylog->modified_on = DateTimeHelper::now();
        $wxpaylog->save();
        return self::getPayUrl($wxpaylog, $openid, $accountId, $memberid);
    }

    /**
     * 获取订单URL
     * @param CPayLog $wxpaylog
     * @return string
     */
    public static function getPayUrl(SPayLog $wxpaylog, $openid, $accountId, $memberid)
    {
        if (is_null($wxpaylog)) {
            return "";
        }
        $body = "订单金额" . $wxpaylog->payed_fee;
        //微信支付
        //$wxpay = \Yii::$container->get('app\modules\pub\services\WeixinPayService');
        $wxpay = new WeixinPayService();
        $return_url = WebUtility::createBeautifiedUrl("pub/payresult/success", ['public_id' => $accountId, 'memberid' => $memberid]);
        $notify_url = WebUtility::createBeautifiedUrl("api/api/wx_paycallback", ['public_id' => $accountId]);
        return $url = $wxpay->generatePayUrl($wxpaylog->id, $wxpaylog->payed_fee, $body, $return_url, $notify_url, $accountId, $openid);

    }

    /*
     * 根据订单ID获取支付订单
     */
    public static function getPayLogByOrderid($orderid)
    {
        if (empty($orderid)) {
            return false;
        }
        $payLogRepository = new PayLogRepository();
        return $payLogRepository->getPayLogByOrderId($orderid);
    }

    /**
     * 获取支付订单
     * @param type $id
     * @return boolean
     */
    public static function getPayLog($id)
    {
        if (empty($id)) {
            return false;
        }
        $payLogRepository = new PayLogRepository();
        return $payLogRepository->getPayLog($id);
    }

    /*
     * 保存订单信息
     */
    public static function savePayLog(SPayLog $wxpaylog)
    {
        if (is_null($wxpaylog)) {
            return false;
        }
        $payLogRepository = new PayLogRepository();
        return $payLogRepository->save($wxpaylog);
    }

    /**
     * 微信对冲
     * @param type $id
     * @return boolean
     * @throws Exception
     */
    public static function resultPayLogWx($id, $accountId, $memberid)
    {
        if (empty($id)) {
            return false;
        }
        $payLogRepository = new PayLogRepository();
        $paylog = $payLogRepository->getPayLog($id);
        if (!isset($paylog)) {
            throw new Exception("交易订单不存在, 更新订单失败");
        }
        $result['orderid'] = $paylog->order_id;
        $result['paylogid'] = $paylog->id;
        $result['trade_state'] = $paylog->trade_state;
        //如果已经完成处理，则直接返回
        if ($paylog->trade_state === 'SUCCESS') {
            return $result;
        }
        //$wxpay = \Yii::$container->get('app\modules\pub\services\WeixinPayService');
        $wxpay = new WeixinPayService();
        $queryResult = $wxpay->queryWxOrderResult($id, $accountId);
        //支付失败
        if (!isset($queryResult)) {
            return $result;
        }
        return self::updateStatusWx($paylog, $queryResult, $memberid, $accountId);
    }

    /*
   * 支付完成更新状态
   */
    private static function updateStatusWx($paylog, $wxPayResult, $memberid, $accountId)
    {
        try {
            $result['trade_state'] = $wxPayResult->trade_state;
            $result['orderid'] = $paylog->order_id;
            $result['paylogid'] = $paylog->id;
            $payLogRepository = new PayLogRepository();
            if (empty($wxPayResult) || $wxPayResult->trade_state !== "SUCCESS") {
                //支付失败
                if (empty($wxPayResult)) {
                    return;
                }
                $paylog->trade_state = $wxPayResult->trade_state;
                if (!empty($wxPayResult->time_end)) {
                    $paylog->time_end = date("Y-m-d H:i:s", strtotime($wxPayResult->time_end));
                }
                $paylog->pay_result = json_encode($wxPayResult);
                $payLogRepository->save($paylog);

            } else {
                //支付成功 
                $wxPayResult->total_fee = number_format(($wxPayResult->total_fee / 100), 2, '.', '');
                if ($wxPayResult->trade_state === "SUCCESS" && $paylog->payed_fee == $wxPayResult->total_fee) {
                    $paylog->trade_state = $wxPayResult->trade_state;
                    $paylog->time_end = DateTimeHelper::now();
                    if (!empty($wxPayResult->time_end)) {
                        $paylog->time_end = date("Y-m-d H:i:s", strtotime($wxPayResult->time_end));
                    }
                    $paylog->pay_result = json_encode($wxPayResult);

                    self::updateOrder($paylog->order_id, $paylog->time_end);
                    $data=self::saveScore($accountId, $paylog->payed_fee, $memberid, $paylog->order_id);
                    if($data) {
                        self::updateMemberGrade($accountId, $memberid);
                    }
                    $payLogRepository->save($paylog);
                }
            }
            return $result;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 更新订单状态
     * @param type $orderid
     * @param type $ordertype
     * @param type $paytime
     * @return type
     * @throws \InvalidArgumentException
     */
    public static function updateOrder($orderid, $paytime)
    {
        if (!isset($orderid)) {
            throw new \InvalidArgumentException('$orderid');
        }
        if (!isset($paytime)) {
            throw new \InvalidArgumentException('$paytime');
        }
        $payLogRepository = new PayLogRepository();
        $rst = $payLogRepository->updateOrderInfo($orderid, $paytime);
        return $rst;
    }

    /**
     * 会员积分表插入积分
     */
    public static function saveScore($accountId, $money, $memberId, $orderId)
    {

        $value = self::findScoreRatio($accountId);
        $score = floor($money * $value);
        if (!empty($value&& $score>0)) {
            $memberScore = new HMemberScore();
            $memberScore->account_id = $accountId;
            $memberScore->member_id = $memberId;
            $memberScore->score_type = "消费";
            $memberScore->score = $score;
            $memberScore->order_id = $orderId;
            $memberScore->sy_score = $score;
            $memberScore->created_on = date('Y-m-d H:i:s', time());;
            $memberScore->created_by = $memberId;
            $memberScore->modified_on = date('Y-m-d H:i:s', time());;
            $memberScore->modified_by = $memberId;
            $memberScore->is_deleted = '0';
            $re = $memberScore->save();
            if ($re) {
                /*会员表积分更新*/
                $sql="UPDATE h_member SET score = ( SELECT sum(score) FROM h_member_score WHERE member_id = '$memberId' AND is_deleted = 0 ), sy_score = ( SELECT sum(sy_score) FROM h_member_score WHERE member_id = '$memberId' AND is_deleted = 0 ) WHERE id = '$memberId' AND is_deleted = 0";
                $db = HMember::getDb();
                $cmd = $db->createCommand($sql)->execute();
            }
            return $re;
        }else{
            return "";
        }
    }

    /*
     * 会员积分比列*/
    private static function findScoreRatio($accountId)
    {
        if (!isset($accountId)) {
            throw new \InvalidArgumentException('$accountId');
        }
        $member = new TParameterValueRepository();
        $rst = $member->findScoreRatio($accountId,$code='MemberRatio');
        return $rst;
    }

    /*重新核算会员等级*/
    public static function updateMemberGrade($accountId, $memberId)
    {
        if (!isset($accountId)) {
            throw new \InvalidArgumentException('$accountId');
        }
        if (!isset($memberId)) {
            throw new \InvalidArgumentException('$memberId');
        }
        //等级与积分是否挂钩
        $grade = new TParameterValueRepository();
        $res = $grade->getParameterValue($accountId, $code = 'MemberContact');
        if (empty($res) || (!empty($res) && $res['value'] == 1)) {
            //与积分挂钩更改会员等级
            $member = new MemberRepository();
            $memberScore=new MemberScoreRepository();
            $score = $memberScore->getScoreSum($accountId, $memberId);
            $level = $member->getLevel($score['score'],$accountId);
            $rst = $member->updateMemberLevel($memberId, $level);
            return $rst;
        }

    }

}

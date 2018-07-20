<?php 
namespace app\modules\api\controllers; 
use Yii;
use yii\web\Controller; 
use app\modules\pub\services\PayLogService;


//第三方调用
class ApiController extends Controller
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

    public function actionWx_paycallback()
    {

        $res = '';
        $postData = file_get_contents("php://input");
        parse_str($postData, $res);
        $orderNo = $res['out_trade_no'] ? $res['out_trade_no'] : $res['order_no'];
        if (empty($orderNo)) {
            $res = (array) simplexml_load_string($postData, 'SimpleXMLElement', LIBXML_NOCDATA);
            $res = json_decode(json_encode($res), true);
            $orderNo = $res['out_trade_no'] ? $res['out_trade_no'] : $res['order_no'];
            $public_id = $res['public_id'];
            $this->_payLogService->resultPayLogWx($orderNo, $public_id);
        }

        $data = [
            "Result" => "success",
            "Message" => ""
        ];

        Yii::$app->response->format = "json";
        Yii::$app->response->data = $data;

        return $data;
    } 

}
 

<?php


namespace app\framework\sms;

use Yii;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use app\framework\utils\DateTimeHelper;
use app\framework\utils\StringHelper;
use app\framework\webService\Curl;
use app\framework\sms\interfaces\SmsProviderInterface;

class HaoserviceSmsProvider implements SmsProviderInterface
{

    public $sendUrl;
    public $key; 
    public $mockMode;
    public $longSms = 1;

    /**
     * @var Curl
     */
    private $_curl;
    private $_sendErrors = [ 
         205401=>'错误的手机号码',
        205402=>'错误的短信模板ID',
        205403=>'网络错误,请重试', 
        205404=>'发送失败，具体原因请参考返回reason',
        205405=>'号码异常/同一号码发送次数过于频繁', 
        205406=>'不被支持的模板',
        201701=>'错误的手机号码',
        201702=>'错误的短信模板ID',
        201703=>'网络错误,请重试', 
        201704=>'发送失败',
        201705=>'参数错误', 
        201706=>'短信长度超过限制', 
        201707=>'错误的消息Id',
        201708=>'模板参数没有全部生效,短信内容不能包含特殊字符#,请检查参数重试',
        201709=>'发送内容和模板不匹配',
        201710=>'有效号码不足',
        10001=>'错误的请求KEY', 
        10002=>'该KEY无请求权限', 
        10003=>'KEY过期', 
        10004=>'错误的SDK KEY', 
        10005=>'应用未审核，请提交认证',
        10007=>'未知的请求源，（服务器没有获取到IP地址）', 
        10008=>'被禁止的IP',
        10009=>'被禁止的KEY', 
        10011=>'当前IP请求超过限制',
        10012=>'当前Key请求超过限制', 
        10013=>'测试KEY超过请求限制', 
         10014=>'系统内部异常',
        10020=>'接口维护', 
        10021=>'接口停用',
        10022=>'appKey按需剩余请求次数为零', 
        10023=>'请求IP无效',
        10024=>'网络错误',
        10025=>'没有查询到结果',
        10026=>'当前请求频率过高超过权限限制',
        10027=>'账号违规被冻结',
        10028=>'传递参数错误',
        10029=>'系统内部异常，请重试',
        10030=>'校验值sign错误', 
        10031=>'套餐产品编号不存在', 
        10032=>'虚拟账号余额不足'
    ];

    public function __construct()
    {
        $settingAccessor = Yii::$container->get('app\framework\settings\SettingsAccessor');
        $config = $settingAccessor->get('sms_config');

        if (!isset($config)) {
            throw new \Exception('缺少配置项 sms_config');
        }

        $config = json_decode($config);

        $this->sendUrl = $config->sendUrl;
        $this->key = $config->companyId;  
        $this->_curl = new Curl();
    }

    private function validate($phoneNums)
    {

        return true;
    }

    /**
     * 发送短信
     * @param string $phoneNums 接收手机号,多个号码可以使用;号分隔
     * @param $content
     * @param string $actionMark 功能点标识
     * @param string $tplId 模板ID
     * @internal param string $message 消息
     */
    public function sendMsg($phoneNums, $content,$actionMark = '',$tplId='')
    {
        if (!$this->validate($phoneNums)) {
            throw new \InvalidArgumentException('$phoneNums: ' . $phoneNums);
        }

        $sendResult =   $this->sendSms($phoneNums, $content,$tplId) ;
        $returnMessage=  json_decode($sendResult);
        $msgId=0;
        $code=201704;
        if (!isset($returnMessage)) {
             $code=201704;
        }
        $code = $returnMessage->error_code;
        $msgId =$returnMessage->result;
//        $code = is_int($sendResult[0] + 0) ? $sendResult[0] + 0 : -127;
//        $msgId = isset($sendResult[1]) ? $sendResult[1] : '';

        try {
            $log = new SmsSendLog();
            $log->id = StringHelper::uuid();
            $log->mobile = $phoneNums;
            $log->message = $content;
            $log->action_mark = $actionMark;
            $log->result = $code;
            $log->msg_ids = $msgId;
            $log->send_time = DateTimeHelper::now();
            $log->save();
        } catch (\Exception $ex) {
            \Yii::error($ex);
        }

        if ($code < 0) {
            throw new InvalidCallException($this->getSendErrorMsg($code));
        }
    }
    
    private function sendSms($receiveMobileTel, $message,$tpl_id='')
    {
        if ($this->mockMode) {
            return '1,' . rand(100000, 9999999);
        }

        $outCharset = 'GB2312';
        if (strcasecmp(Yii::$app->charset, $outCharset) !== 0) {
            $message = iconv(Yii::$app->charset, $outCharset . '//IGNORE', $message);
        }

        $postData = [
            'mobile' => $receiveMobileTel,
            'tpl_id' =>$tpl_id,
            'tpl_value' =>$message ,
            'key' => $this->key 
        ];

        $response = $this->_curl->setOption(CURLOPT_POSTFIELDS, http_build_query($postData))
            ->post($this->sendUrl);

        if ($this->_curl->responseCode != 200) {
            throw new InvalidCallException("调用短信服务失败");
        }

        return $response;
    }

    private function getSendErrorMsg($errorId)
    {
        if (!isset($this->_sendErrors[$errorId])) {
            return "未知错误";
        }

        return $this->_sendErrors[$errorId];
    }

}


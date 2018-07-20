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

class YunkeSmsProvider implements SmsProviderInterface
{

    public $sendUrl;
    public $companyId;
    public $loginName;
    public $password;
    public $mockMode = true;
    public $longSms = 1;

    /**
     * @var Curl
     */
    private $_curl;
    private $_sendErrors = [
        0 => "短信发送失败",
        -1 => "输入参数不完整",
        -2 => "非法来源IP地址或账号密码有误",
        -3 => "目标号码错误",
        -4 => "企业账号余额不足",
        -5 => "用户账号余额不足",
        -6 => "输入参数不完整",
        -7 => "短信服务连接数据库失败",
        -8 => "企业账号已被禁用",
        -9 => "短信内容含有过滤关键字",
    ];

    public function __construct()
    {
        $settingAccessor = Yii::$container->get('app\framework\settings\interfaces\SettingsAccessorInterface');
        $config = $settingAccessor->get('sms_config');

        if (!isset($config)) {
            throw new \Exception('缺少配置项 sms_config');
        }

        $config = json_decode($config);

        $this->sendUrl = $config->sendUrl;
        $this->companyId = $config->companyId;
        $this->loginName = $config->loginName;
        $this->password = $config->password;
        $this->mockMode = false;

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
     * @internal param string $message 消息
     */
    public function sendMsg($phoneNums, $content, $actionMark = '')
    {
        if (!$this->validate($phoneNums)) {
            throw new \InvalidArgumentException('$phoneNums: ' . $phoneNums);
        }

        $sendResult = explode(',', $this->sendSms($phoneNums, $content), 2);
        $code = is_int($sendResult[0] + 0) ? $sendResult[0] + 0 : -127;
        $msgId = isset($sendResult[1]) ? $sendResult[1] : '';

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

        if ($code <= 0) {
            throw new InvalidCallException($this->getSendErrorMsg($code));
        }
    }

    private function sendSms($receiveMobileTel, $message)
    {
        if ($this->mockMode) {
            return '1,' . rand(100000, 9999999);
        }

        $outCharset = 'GB2312';
        if (strcasecmp(Yii::$app->charset, $outCharset) !== 0) {
            $message = iconv(Yii::$app->charset, $outCharset . '//IGNORE', $message);
        }

        $postData = [
            'CorpID' => $this->companyId,
            'LoginName' => $this->loginName,
            'Passwd' => $this->password,
            'send_no' => $receiveMobileTel,
            'msg' => $message
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

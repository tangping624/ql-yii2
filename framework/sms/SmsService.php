<?php

namespace app\framework\sms;

use app\framework\sms\interfaces\SmsProviderInterface;
use app\framework\sms\interfaces\SmsServiceInterface;
use app\framework\validators\RegexCollection;

class SmsService implements SmsServiceInterface
{

    /**
     * 重新发送的最小间隔
     * @var int
     */
    public static $verify_duration_second = 60;
    /**
     * 验证码过期时长
     * @var int
     */
    public static $verify_expire_second = 300;

    /**
     * @var SmsProviderInterface
     */
    private $_apiProxy;

    public function __construct()
    {
        $this->_apiProxy = \Yii::$container->get('app\framework\sms\interfaces\SmsProviderInterface');
    }

    /**
     * 发送短信验证码
     * @param string $phoneNum
     * @return array
     */
    public function sendVerifyCode($phoneNum)
    {
        $checked = $this->_checkPhoneNum($phoneNum);
        if ($checked == false) {
            return [
                'result' => false,
                'code' => static::SMS_SEND_RESULT_INVALID_PHONE,
                'msg' => $phoneNum . "不是有效的手机号码，请输入正确格式的手机号码"
            ];
        }

        $now = time();
        $code = $this->generateCode();
        $verifyInfo = [
            'phone' => $phoneNum,
            'code' => $code,
            'sendTime' => $now,
        ];
        $cacheOpt = \Yii::$app->cache;
        $cacheKey = $this->_verifyCacheKey($phoneNum);

        if (!$cacheOpt->add($cacheKey, $verifyInfo, static::$verify_expire_second)) {
            $cachedVerifyInfo = $cacheOpt->get($cacheKey);
            if ($now - $cachedVerifyInfo['sendTime'] < static::$verify_duration_second) {
                return [
                    'result' => false,
                    'code' => static::SMS_SEND_RESULT_FAST,
                    'msg' => "操作太频繁，请稍后再试!"
                ];
            } else {
                //验证码未过期, 则重复发送之前的验证码.
                $verifyInfo['code'] = $cachedVerifyInfo['code'];
                $code = $verifyInfo['code'];
                $cacheOpt->set($cacheKey, $verifyInfo, static::$verify_expire_second);
            }
        }

        try {
            //$this->_apiProxy->sendMsg($phoneNum, "您的验证码为：" . $code . '，请勿泄漏。');
            $this->_apiProxy->sendMsg($phoneNum, "#code#=" . $code ,'',SMS_IDENTIFYING_CODE);
            return [
                'result' => true,
                'verifycode'=>$code,
                'code' => static::SMS_SEND_RESULT_SUCCESS,
                'msg' => "发送成功"
            ];
        } catch (\Exception $exc) {
            \Yii::error($exc);
            return [
                'result' => false,
                'code' => static::SMS_SEND_RESULT_ERROR,
                'msg' => "发送失败, 调用短信提供程序出错!"
            ];
        }

    }

    /**
     * 校验短信验证码
     * @param string $phoneNum
     * @param string $inputCode
     * @return array
     */
    public function verifyCode($phoneNum, $inputCode)
    {
        $checked = $this->_checkPhoneNum($phoneNum);
        if (!$checked) {
            return [
                'result' => false,
                'code' => static::SMS_VERIFY_RESULT_INVALID_PHONE,
                'msg' => $phoneNum . "不是有效的手机号码，请输入正确格式的手机号码"
            ];
        }

        $cacheKey = $this->_verifyCacheKey($phoneNum);
        $cacheOpt = \Yii::$app->cache;
        $verifyInfo = $cacheOpt->get($cacheKey);

        if ($verifyInfo) {
            if ($verifyInfo["code"] != $inputCode) {
                return [
                    'result' => false,
                    'code' => static::SMS_VERIFY_RESULT_INCORRECT,
                    'msg' => "验证码无效"
                ];
            } else {
                $cacheOpt->delete($cacheKey);
                return [
                    'result' => true,
                    'code' => static::SMS_VERIFY_RESULT_SUCCESS,
                    'msg' => "验证成功",
                    'phone' => $phoneNum
                ];
            }
        } else {
            return [
                'result' => false,
                'code' => static::SMS_VERIFY_RESULT_TIMEOUT,
                'msg' => "验证码已过期,请重新获取"
            ];
        }
    }

    private function generateCode($length = 4)
    {
        return rand(pow(10, ($length - 1)), pow(10, $length) - 1);
    }

    private function _verifyCacheKey($phoneNum)
    {
        return 'sendVerifyCode_' . $phoneNum;
    }

    private function _checkPhoneNum($phoneNum)
    {
        $result = preg_match(RegexCollection::MOBILE, $phoneNum);
        return $result == 1;
    }

    public function sendMessage($phoneNum, $message)
    {
        $checked = $this->_checkPhoneNum($phoneNum);
        if ($checked == false) {
            return [
                'result' => false,
                'code' => static::SMS_SEND_RESULT_INVALID_PHONE,
                'msg' => $phoneNum . "不是有效的手机号码，请输入正确格式的手机号码"
            ];
        }

        if (empty($message)) {
            return [
                'result' => false,
                'code' => static::SMS_SEND_RESULT_EMPTY_MESSAGE,
                'msg' => $phoneNum . "不能发送空内容"
            ];
        }

        $cacheKey = 'sendMessage:' . $phoneNum;
        if (\Yii::$app->cache->add($cacheKey, 1, 3)) {
            try {
                $this->_apiProxy->sendMsg($phoneNum, $message);
                return [
                    'result' => true,
                    'code' => static::SMS_SEND_RESULT_SUCCESS,
                    'msg' => "发送成功"
                ];
            } catch (\Exception $exc) {
                \Yii::error($exc);
                return [
                    'result' => false,
                    'code' => static::SMS_SEND_RESULT_ERROR,
                    'msg' => "发送失败, 调用短信提供程序出错!"
                ];
            }
        } else {
            return [
                'result' => false,
                'code' => static::SMS_SEND_RESULT_FAST,
                'msg' => $phoneNum . "发送太频繁"
            ];
        }

    }
}
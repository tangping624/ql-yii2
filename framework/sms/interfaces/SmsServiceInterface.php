<?php

namespace app\framework\sms\interfaces;

interface smsServiceInterface
{
    const SMS_SEND_RESULT_SUCCESS = 0;//短信发送成功
    const SMS_SEND_RESULT_INVALID_PHONE = 1;//无效手机号码
    const SMS_SEND_RESULT_SENDING = 2;//正在发送中
    const SMS_SEND_RESULT_FAST = 3;//发送太频繁
    const SMS_SEND_RESULT_ERROR = 4;//发送太频繁
    const SMS_SEND_RESULT_EMPTY_MESSAGE = 5;//短信内容不能为空
    const SMS_SEND_RESULT_CALL_FAILURE = 6;//调用短信接口失败

    const SMS_VERIFY_RESULT_SUCCESS = 0;//验证成功
    const SMS_VERIFY_RESULT_INVALID_PHONE = 1;//无效手机号码
    const SMS_VERIFY_RESULT_TIMEOUT = 2;//验证码过期
    const SMS_VERIFY_RESULT_INCORRECT = 3;//验证码不正确


    /**
     * 发送短信验证码
     * @param string $phoneNum
     * @return array ['result'=>, 'code'=>, 'msg' => '']
     */
    public function sendVerifyCode($phoneNum);

    /**
     * 验证验证码是否匹配
     * @param string $phoneNum
     * @param string $inputCode
     * @return array
     */
    public function verifyCode($phoneNum, $inputCode);

    /**
     * 发送短信
     * @param string $phoneNum
     * @param string $message
     * @return array array ['result'=>, 'code'=>, 'msg' => '']
     */
    public function sendMessage($phoneNum, $message);

}
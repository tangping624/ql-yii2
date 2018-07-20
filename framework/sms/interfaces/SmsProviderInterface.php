<?php

namespace app\framework\sms\interfaces;

interface SmsProviderInterface
{
    public function sendMsg($phoneNum, $content);
}
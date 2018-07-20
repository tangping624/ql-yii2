<?php

namespace app\framework\validators;

class RegexCollection
{
    /**
     * 手机号码
     */
    const MOBILE = '/1[0-9]{10}/';

    /**
     * 手机或座机
     */
    const MOBILE_AND_PHONE = '/(^(\d{3,4}-)?\d{7,8})$|(1[0-9]{10})/';

    /**
     * 身份证号码
     */
    const ID_CARD = '';
}
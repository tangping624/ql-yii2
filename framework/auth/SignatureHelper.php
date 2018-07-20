<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\framework\auth;

/**
 * Description of SignatureHelper
 *
 * @author Chenxy
 */
class SignatureHelper
{
    /**
     * 生成签名
     * @param array $data 要签名的数据
     * @param string $token 签名
     * @return string
     */
    public static function Create($data, $token)
    {
        $data['token'] = $token;
        sort($data, SORT_STRING);
        $strTmp = implode($data);
        $signature = sha1($strTmp);
        return $signature;
    }
    
    /**
     * 验证签名
     * @param array $data
     * @param string $token
     * @param string $signature
     * @return bool 
     */
    public static function Validate($data, $token, $signature)
    {
        $genSignatureValue = static::Create($data, $token);
        return $genSignatureValue == $signature;
    }
}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\framework\weixin\helper; 
class MsgSecretHelper
{
    /**
     * 验证签名 
     * @param type $encryptXml
     * @param type $token
     * @return type
     */
    public static function validataSignature($encryptXml, $token)
    {
        $data = self::extract($encryptXml);
        if (!key_exists("MsgSignature", $data)) {
            return false;
        }
        $expectSignature = self::createSignature($encryptXml, $token);
        return $expectSignature === $data["MsgSignature"];
    }
    
    /**
     * 生成签名
     * @param type $encryptXml
     * @param type $token
     * @return type
     * @throws \app\framework\weixin\WeixinException
     */
    public static function createSignature($encryptXml, $token)
    {
        $data = self::extract($encryptXml);
        
        // 验证格式
        if (!key_exists("Encrypt", $data) || !key_exists("TimeStamp", $data) || !key_exists("Nonce", $data)) {
            throw new \app\framework\weixin\WeixinException("消息包内容格式不正确:" . $encryptXml);
        }
        
        // 生成签名
        $encryptMsg = $data["Encrypt"];
        $timestamp = $data["TimeStamp"];
        $nonce = $data["Nonce"];
        $array = array($encryptMsg, $token, $timestamp, $nonce);
        sort($array, SORT_STRING);
        $str = implode($array);
        return sha1($str);
    }
    
    private static function extract($encryptXml)
    {
        $xmldata = new \SimpleXMLElement($encryptXml);

        // 转换成数组
        $data = [];
        foreach ($xmldata as $key => $value) {
            $data[$key] = strval($value);
        }
        
        if (!key_exists("TimeStamp", $data) && $_GET["timestamp"]) {
            $data["TimeStamp"] = $_GET["timestamp"];
        }

        if (!key_exists("Nonce", $data) && $_GET["nonce"]) {
            $data["Nonce"] = $_GET["nonce"];
        }
        
        if (!key_exists("MsgSignature", $data) && $_GET["msg_signature"]) {
            $data["MsgSignature"] = $_GET["msg_signature"];
        }
            
        return $data;
    }
}

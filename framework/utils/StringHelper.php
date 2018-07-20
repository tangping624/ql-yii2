<?php

namespace app\framework\utils;

class StringHelper
{
    /**
     * 36位GUID
     * @return string
     */
    public static function uuid()
    {
        list($usec, $sec) = explode(" ", microtime(false));
        $usec = (string)($usec * 10000000);
        $timestamp = bcadd(bcadd(bcmul($sec, "10000000"), (string)$usec), "621355968000000000");
        $ticks = bcdiv($timestamp, 10000);
        $maxUint = 4294967295;
        $high = bcdiv($ticks, $maxUint) + 0;
        $low = bcmod($ticks, $maxUint) - $high;
        $highBit = (pack("N*", $high));
        $lowBit = (pack("N*", $low));
        $guid = str_pad(dechex(ord($highBit[2])), 2, "0", STR_PAD_LEFT) . str_pad(dechex(ord($highBit[3])), 2, "0", STR_PAD_LEFT) . str_pad(dechex(ord($lowBit[0])), 2, "0", STR_PAD_LEFT) . str_pad(dechex(ord($lowBit[1])), 2, "0", STR_PAD_LEFT) . "-" . str_pad(dechex(ord($lowBit[2])), 2, "0", STR_PAD_LEFT) . str_pad(dechex(ord($lowBit[3])), 2, "0", STR_PAD_LEFT) . "-";
        $chars = "abcdef0123456789";
        for ($i = 0; $i < 4; $i++) {
            $guid .= $chars[mt_rand(0, 15)];
        }
        $guid .= "-";
        for ($i = 0; $i < 4; $i++) {
            $guid .= $chars[mt_rand(0, 15)];
        }
        $guid .= "-";
        for ($i = 0; $i < 12; $i++) {
            $guid .= $chars[mt_rand(0, 15)];
        }

        return $guid;
    }

    /**
     * 电话号码格式化：隐藏中间四位
     * @param $str
     * @return string
     */
    public static function maskMobile($str)
    {
        if ($str && strlen($str) > 4) {
            //反转从第5位取
            $str = strrev($str);
            $str = substr($str, 0, 4) . "****" . substr($str, 8);
            return strrev($str);
        }
        return $str;
    }

    /**
     * 隐藏身份证/军官证之类的：中间5位
     * @param $str
     * @return string
     */
    public static function maskIdentity($str)
    {
        if (!$str) {
            return '';
        }

        $len = strlen($str);

        switch ($len) {
            case 15:
                return substr($str, 0, 7) . "*****" . substr($str, 12, 3);
                break;
            case 18:
                return substr($str, 0, 9) . "*****" . substr($str, 14, 4);
                break;
            default:
                if ($len > 5) {
                    $index = ceil(($len - 5) / 2);
                    return substr($str, 0, $index) . "*****" . substr($str, $index + 5, $len - $index - 5);
                } else {
                    return $str;
                }
        }
    }

    /**
     * 获取数据库连接的dbname
     * @param string $connectionString
     * @return string
     */
    public static function getDbNameOfConnection($connectionString)
    {
        $curdb = explode('=', $connectionString);
        return $curdb[2];
    }

    /**
     * 字符串格式化函数， 如积分商城兑换码: 0000000000 -> 0000 0000 00
     * @param string $str 输入字符串
     * @param string $joiner 连接符
     * @param string $each 没each个字符分割用joiner连接
     * @param string $encoding 字符串输入输出编码
     * @return string
     */
    public static function formatCode($str, $joiner = '', $each = 1, $encoding = 'utf-8') {
        if (!is_numeric($str) && !is_string($str)) {
            return '';
        }
        $joiner = (string)$joiner;
        if ($joiner == '' && $each == 1) {
            return $str;
        }
        $start = 0;
        $each = max(0, $each);
        $ret = '';
        while ($substr = mb_substr($str, $each*$start++, $each, $encoding)) {
            $ret .= $substr . $joiner;
        }
        return trim($ret, $joiner);
    }
    
    /**
     * 格式化距离
     * @param type $distance
     * @return string
     */
    public static function format_distance($distance)
    {
        if ($distance <= 0) {
            return '';
        }
        
        $distance = ceil($distance);

        if ($distance <= 500) {
            $distance = '<500m';
        } elseif ($distance <= 1000) {
            $distance = '<1km';
        } else {
            $distance = '约' . number_format($distance / 1000, 1, '.', '') . 'km';
        }

        return $distance;
    }
    
    /**
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return 产生的随机字符串
     */
    public static function getNonceStr($length = 32) {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 一个更安全的随机字符
     * @param string $type alnum, alpha, hexdec, numeric, nozero, distinct
     * @param int $length
     * @return string
     */
    public static function random($type = 'alnum', $length=16)
    {
        switch ($type) {
            case 'alnum':
                $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 'alpha':
                $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 'hexdec':
                $pool = '0123456789abcdef';
                break;
            case 'numeric':
                $pool = '0123456789';
                break;
            case 'nozero':
                $pool = '123456789';
                break;
            case 'distinct':
                $pool = '2345679ACDEFHJKLMNPRSTUVWXYZ';
                break;
            default:
                $pool = (string)$type;
                break;
        }


        $crypto_rand_secure = function ($min, $max) {
            $range = $max - $min;
            if ($range < 0) return $min; // not so random...
            $log = log($range, 2);
            $bytes = (int)($log / 8) + 1; // length in bytes
            $bits = (int)$log + 1; // length in bits
            $filter = (int)(1 << $bits) - 1; // set all lower bits to 1
            do {
                $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
                $rnd = $rnd & $filter; // discard irrelevant bits
            } while ($rnd >= $range);
            return $min + $rnd;
        };

        $token = "";
        $max = strlen($pool);
        for ($i = 0; $i < $length; $i++) {
            $token .= $pool[$crypto_rand_secure(0, $max)];
        }
        return $token;
    }
    
    /**
     * 含中文的字符串截取
     * @param string $str
     * @param int $start
     * @param int $len
     * @return string
     */
    public static function substr($str, $start, $len = null) {
        $len = $len?:strlen($str);
        for($i = $start; $i < $len;) {
            // 汉字取3个字符
            if (ord(substr($str, $i, 1)) > 0xa0) {
                $tmpstr .= substr($str, $i, ($len - $i) >= 3 ? 3 : 0);
                $i=$i+3; // 变量自加3
            } else{
                $tmpstr .= substr($str, $i, 1); 
                $i++;
            }
        }
        return $tmpstr;
    }

}
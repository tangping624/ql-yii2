<?php

namespace app\framework\utils;

use Yii;

class RequestHelper
{
    public static function hostIp()
    {

        if (isset($_SERVER['SERVER_ADDR']))
            return $_SERVER['SERVER_ADDR'];

        if (isset($_SERVER['LOCAL_ADDR']))
            return $_SERVER['LOCAL_ADDR'];

        return 'unknown';
    }

    public static function requestUrl()
    {
        if (isset(Yii::$app)) {
            return Yii::$app->request->getAbsoluteUrl();
        }
    }

    public static function pid()
    {
        return getmypid();
    }

    /**
     * 异步post
     * @param string $url
     * @param array $params
     * @param int $port 端口
     */
    public static function postAsync($url, $params, $port = 80)
    {
        $post_string = http_build_query($params);
        $parts = parse_url($url);
        $fp = fsockopen($parts['host'],
            isset($parts['port']) ? $parts['port'] : $port,
            $errno, $errstr, 30);

        $out = "POST " . $parts['path'] . (isset($parts['query']) ? ('?' . $parts['query']) : '') . " HTTP/1.1\r\n";
        $out .= "Host: " . $parts['host'] . "\r\n";
        $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out .= "Content-Length: " . strlen($post_string) . "\r\n";
        $out .= "Connection: Close\r\n\r\n";
        if (isset($post_string)) $out .= $post_string;

        fwrite($fp, $out);
        fclose($fp);
    }


    /**
     * 判断是否微信浏览器访问
     * @return bool
     */
    public static function isWeixinAgent()
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($useragent, 'MicroMessenger') == false && strpos($useragent, 'Windows Phone') == false) {
            return false;
        }
        return true;
    }

    public static function isApi()
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        return $useragent == 'api';
    }

    /**
     * 获取当前url不带queryString
     * @return string
     */
    public static function getPurUrlWithoutArgs()
    {
        return strtok($_SERVER["REQUEST_URI"], '?');
    }

}
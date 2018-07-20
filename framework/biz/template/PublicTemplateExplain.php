<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/3/27
 * Time: 18:04
 */

namespace app\framework\biz\template;


use app\framework\cache\CachePackageManger;

class PublicTemplateExplain
{

    public static function GetUrl()
    {
        $_url = \Yii::$app->params['public_resourcesUrl'];
        if ($_url) {
            if ((strlen($_url) - 1) <> '/') {
                $_url .= "/";
            }
        }
        return $_url;
    }

    /**
     * 返回头文件的HTML
     * @return string
     */
    public static function getHeaderHtml()
    {
        $url = self::GetUrl() . "auth/header";
        return self::getRequestURLHtml($url);
    }

    /**
     * 返回头文件的HTML
     * @return string
     */
    public static function getBottomHtml()
    {
        $url = self::GetUrl() . "auth/bottom";
        return self::getRequestURLHtml($url);
    }

    /**
     * 返回头引用的文件脚本
     * @return null|object|string
     */
    public static function getBegin_Page()
    {
        $url = self::GetUrl() . "auth/beginpage";
        return self::getRequestURLHtml($url);
    }

    /**
     * 返回引用的脚本文件
     * @return null|object|string
     */
    public static function getEnd_Page()
    {
        $url = self::GetUrl() . "auth/endbody";

        return self::getRequestURLHtml($url);
    }


    /**
     * 返回请求URL的HTML
     * @param $url
     * @return null|object|string
     * @throws \Exception
     */
    private static function getRequestURLHtml($url)
    {

        $cacheKey = sha1($url);
        $cacheObject = CachePackageManger::instance($cacheKey);
        $response = $cacheObject->get();
        if (!isset($response)) {
            //初始化
            $ch = curl_init();
            //设置选项，包括URL
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            //执行并获取HTML文档内容
            $response = curl_exec($ch);
            //释放curl句柄
            curl_close($ch);
            $cacheObject->set($response);
        }

        return $response;
    }
}

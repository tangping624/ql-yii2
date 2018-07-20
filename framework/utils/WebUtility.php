<?php

namespace app\framework\utils;
use app\framework\biz\tenant\TenantReaderInterface;
use Yii;

class WebUtility
{
    const SEC_KEY_SALT = 'jak23hfks239y3dsj23l1!@&*df';
    /*获取客户端IP*/
    public static function getClientIP()
    {
        if (@$_SERVER["HTTP_CLIENT_IP"])
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        else if (@$_SERVER["REMOTE_ADDR"])
            $ip = $_SERVER["REMOTE_ADDR"];
        else if (@getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (@getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (@getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else
            $ip = "";
        return $ip;
    }

    /**
     * get root domain
     * @return string
     */
    public static function getBaseUrl()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        return $protocol . $_SERVER['HTTP_HOST'];
    }

    /**
     * 创建 URL
     * @param mixed $route 路由
     * @param mixed $params 参数
     * @param bool $appendUnit 是否添加租户代码
     * @param bool $appendAppCode 是否添加AppCode参数
     * @param bool $appendFuncCode 是否添加FunctionCode参数
     * @return mixed
     */
    public static function createUrl($route, $params = [],  $appendAppCode = true, $appendFuncCode = true)
    {
        $anchor = isset($params['#']) ? '#' . $params['#'] : '';
        unset($params['#'], $params['r']);
        $route = trim($route, '/');
        $url = '/';
        

        $url .= $route;

        if (!empty($params) && ($query = http_build_query($params)) !== '') {
            $url .= '?' . $query;
        }

        $url .= $anchor;
        if ($appendFuncCode) {
            $url = static::appendFunctionCodeToUrl($url);
        }
        if ($appendAppCode) {
            return static::appendAppCodeToUrl($url);
        } else {
            return $url;
        }
    }

    /**
     * 创建绝对路径 URL
     * @param mixed $route 路由
     * @param mixed $params 参数
     * @param bool $appendAppCode 是否添加AppCode参数
     * @param bool $appendFuncCode 是否添加FunctionCode参数
     * @return mixed
     */
    public static function createAbsoluteUrl($route, $params = [], $appendAppCode = true, $appendFuncCode = true)
    {
        $params = (array)$params;
        $params[0] = $route;
       
        $url = Yii::$app->getUrlManager()->createAbsoluteUrl($params);
        if ($appendFuncCode) {
            $url = static::appendFunctionCodeToUrl($url);
        }
        if ($appendAppCode) {
            return static::appendAppCodeToUrl($url);
        } else {
            return $url;
        }
    }

    /**
     * 创建美化后的绝对路径 URL
     * @param mixed $route 路由
     * @param mixed $params 参数
     * @return mixed
     */
    public static function createBeautifiedUrl($route, $params = [])
    {
        $params = (array)$params; 
        $head =   ""  ; 
        $querystring = http_build_query($params);

        if (!empty($querystring)) {
            $querystring = "?" . $querystring;
        }

        return Yii::$app->getUrlManager()->getHostInfo() . $head . "/" . $route . $querystring;
    }

    

 



    public static function getToken()
    {
        return empty($_REQUEST['token']) ? '' : $_REQUEST['token'];
    }

    /**
     * 获取当前使用的AppCode
     * @return string
     */
    public static function getCurrentAppCode()
    {
        $appCode = static::getQueryAppCode();
        return empty($appCode) ? Yii::$app->id : $appCode;
    }

    /**
     * 获取URL中AppCode参数(_ac)，公共模块请求的URL地址中会传递该参数
     * @return string
     */
    public static function getQueryAppCode()
    {
        return empty($_REQUEST['_ac']) ? '' : $_REQUEST['_ac'];
    }

    /**
     * 将AppCode参数(_ac)添加到URL参数中
     * @param $url
     * @return string
     */
    public static function appendAppCodeToUrl($url)
    {
        $appCode = static::getQueryAppCode();
        if (!empty($appCode)) {
            $url = static::buildQueryUrl($url, ('_ac=' . $appCode));
        }
        return $url;
    }

    /**
     * 获取URL中FunctionCode参数(_fc)，同一个Controller不同的权限校验会传递该参数
     * @return string
     */
    public static function getQueryFunctionCode()
    {
        return empty($_REQUEST['_fc']) ? '' : $_REQUEST['_fc'];
    }

    /**
     * 将FunctionCode参数(_fc)添加到URL参数中
     * @param $url
     * @return string
     */
    public static function appendFunctionCodeToUrl($url)
    {
        $funcCode = static::getQueryFunctionCode();
        if (empty($funcCode) === false && strpos($url, '_fc') === false) {
            $url = static::buildQueryUrl($url, ('_fc=' . $funcCode));
        }
        return $url;
    }


    public static function getDefaultDsn()
    {
        $arr = explode('=', Yii::$app->db->dsn);
        $dbname = $arr[2];
        $arr = explode(';', $arr[1]);
        $host = $arr[0];
        return ['host' => $host, 'dbname' => $dbname];
    }


    /**
     * This method is used to append query parameters to an url. Since the url
     * might already contain parameter it has to be detected and to build a proper
     * URL
     *
     * @param string $url base url to add the query params to
     * @param string $query params in query form with & separated eg: 'page=11'
     *
     * @return string url with query params
     */
    public static function buildQueryUrl($url, $query)
    {
        $url .= (strstr($url, '?') === false) ? '?' : '&';
        if (is_array($query)) {
            $url .= http_build_query($query);
        } else {
            $url .= $query;
        }
        return $url;
    }


    public static function bind($field, $default = null)
    {
        if (isset($field)) {
            return $field;
        }

        if ($default == null) {
            if (is_array($field)) {
                return [];
            }
            return '';
        }

        return $default;
    }

    /**
     * 去url中的某个参数，一般用于移除openid/memberid之类的url参数，并重定向
     */
    public static function unsetParam($param, $url)
    {
        return preg_replace(
            array("/{$param}=[^&]*/i", '/[&]+/', '/\?[&]+/', '/[?&]+$/',),
            array('', '&', '?', '',),
            $url
        );
    }

    /**
     * 替换url中某些以:开头的查询字符串（针对那些需要在具体使用中动态替换的）
     * @param $url
     * @param array $params 需要替换的query，至少需要替换：:openId,:time,:seckey，其中seckey由所有替换字符串生成
     */
    public static function replaceUrlParams($url,$params = [])
    {
        if (!$url || strpos($url, ':') === false) {
            return $url;
        }

        $arr = [];
        $url = strtolower($url);

        if (strpos($url, ':openid') !== false) {
            $openId = $params['openId'] ? $params['openId'] : \Yii::$app->context->openId;
            $arr[]  = $openId;
            $url    = str_replace(':openid',$openId,$url);
            unset($params['openId']);
        }
        if (strpos($url, ':time') !== false) {
            $time  = $params['time'] ? $params['time'] : time();
            $arr[] = $time;
            $url   = str_replace(':time',$time,$url);
            unset($params['time']);
        }
        if (!empty($params)) {
            foreach ($params as $k=>$v) {
                $url = str_replace(":$k",$v,$url);
                $arr[] = $v;
            }
        }
        if (strpos($url, ':seckey') !== false) {
            $url = str_replace(':seckey',self::getSecKey($arr),$url);
        }

        return $url;
    }

    /**
     * 给url加上:openId,:time,:secKey
     * @param $url
     */
    public static function buildUrlWithSecKey($url)
    {
        if(empty($url)) {
            return $url;
        }

        $url = strtolower($url);
        if (strpos($url,'?') === false) {
            $url .= '?';
        } else {
            $url .= '&';
        }

        if (strpos($url,':openid') === false) {
            $url .= 'openid=:openid&';
        }
        if (strpos($url,':time') === false) {
            $url .= 'time=:time&';
        }
        if (strpos($url,':seckey') === false) {
            $url .= 'seckey=:seckey&';
        }

        $url = rtrim($url,'&');

        return $url;
    }

    public static function getSecKey($params) {
        if (empty($params))
            return '';
        sort($params, SORT_STRING);
        return md5(implode('--',array_filter($params)) . self::SEC_KEY_SALT);
    }
}
?>

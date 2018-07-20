<?php

namespace app\framework\oauth2;


class ClientHelper
{
    public static function getRequest($tenantCode)
    {
        if (empty($tenantCode)) {
            $tenantReader = \Yii::$container->get('app\framework\biz\tenant\TenantReaderInterface');
            $tenantCode = $tenantReader->getCurrentTenantCode();
        }
        if (empty($tenantCode)) {
            throw new \InvalidArgumentException('租户代码不能为空!');
        }

        $request = null;
        if (\Yii::$container->has('app\framework\oauth2\ClientRequest')) {
            $request = \Yii::$container->get('app\framework\oauth2\ClientRequest', $tenantCode);
        } else {
            $request = new ClientRequest($tenantCode);
        }

        return $request;
    }

    /**
     * @param bool $cache
     * @param null|array $appKeyPair ['id'=>, 'secret'=>]
     * @param string $accessTokenUrl
     * @param string $tenantCode
     * @return null|object
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     */
    public static function getToken($cache = true, $appKeyPair=null, $accessTokenUrl='', $tenantCode='')
    {
        $request = static::getRequest($tenantCode);
        return $request->getToken($appKeyPair);
    }


    /**
     * @param $url
     * @param array $params url的参数
     * @param array|null $appKeyPair 指定appId, secret dict: ['id'=>'', 'secret' =>'']
     * @param string $accessTokenUrl 不再使用了
     * @param bool|true $autoGetToken 是否自动获取access_token
     * @param string $tenantCode
     * @return mixed
     * @throws \Exception
     */
    public static function post($url, $params, $appKeyPair=null, $accessTokenUrl='', $autoGetToken=true, $tenantCode='')
    {
        $request = static::getRequest($tenantCode);
        return $request->request($url, $params, 'POST', $appKeyPair, $autoGetToken);
    }

    /**
     * @param $url
     * @param array $params url的参数
     * @param array|null $appKeyPair 指定appId, secret dict: ['id'=>'', 'secret' =>'']
     * @param bool|true $autoGetToken 是否自动获取access_token
     * @param string $tenantCode
     * @return mixed
     * @throws \Exception
     */
    public static function get($url, $params=[], $appKeyPair=null, $autoGetToken=true, $tenantCode='')
    {
        $request = static::getRequest($tenantCode);
        return $request->request($url, $params, 'GET', $appKeyPair, $autoGetToken);
    }
}
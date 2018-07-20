<?php

namespace app\framework\auth;

use yii\web\Cookie;

use app\framework\auth\interfaces\TokenAccessorInterface;

class CookieTokenAccessor implements TokenAccessorInterface
{
    /**
     * @inheritdoc
     */
    public function getToken()
    {
        $token = isset($_GET[Configs::TOKEN_KEY]) ? $_GET[Configs::TOKEN_KEY] : '';
        //api在同一个接口设置token，并获取token需要以下逻辑
        return $token;
    }

    /**
     * @inheritdoc
     */
    public function setToken($token)
    {
        if (empty($token)) {
            throw new \InvalidArgumentException('$token');
        }
        return $token;
    }

    /**
     * @inheritdoc
     */
    public function removeToken()
    {
        return;
    }
 
 
 

}

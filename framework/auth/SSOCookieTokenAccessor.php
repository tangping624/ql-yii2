<?php

namespace app\framework\auth;
 
use yii\base\NotSupportedException;  
use app\framework\auth\interfaces\TokenAccessorInterface;

class SSOCookieTokenAccessor implements TokenAccessorInterface
{
    /**
     * @inheritdoc
     */
    public function getToken()
    {
        \Yii::trace('gettoken');
        $token = isset($_GET[Configs::TOKEN_KEY]) ? $_GET[Configs::TOKEN_KEY] : '';
        if (empty($token)) {
            if (!\Yii::$app->session->isActive) {
                \Yii::$app->session->open();
            }
            $token = \Yii::$app->session->id;
        }
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

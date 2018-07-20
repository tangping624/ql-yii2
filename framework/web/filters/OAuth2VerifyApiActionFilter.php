<?php

namespace app\framework\web\filters;
 

use yii\base\ActionFilter;

class OAuth2VerifyApiActionFilter extends ActionFilter
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $token = isset($_GET['access_token']) ? $_GET['access_token'] : '';

        if (empty($token)) {
            echo json_encode(['errcode' => 40013, 'msg' => 'access_token is empty']);
            return false;
        } else {
             
            $cacheKey = 'oauth2:access_token:'  . $token;
            $tokenCache = \Yii::$app->cache->get($cacheKey);

            if ($tokenCache === false) {
                $result = $this->_validTokenFromOAuthServerToken($token);
                if ($result == false) {
                    \Yii::$app->cache->set($cacheKey, 0, 60);
                    echo json_encode(['errcode' => 40013, 'errmsg' => 'invalid access_token']);
                    return false;
                } else {
                    \Yii::$app->cache->set($cacheKey, $token, 3600);
                }

            } elseif ($tokenCache != $token) {
                echo json_encode(['errcode' => 40013, 'errmsg' => 'invalid access_token']);
                return false;
            }

        }

        return parent::beforeAction($action);
    }

    private function _validTokenFromOAuthServerToken($token)
    { 
      $oauthServerUrl = \yii::$app->request->hostInfo . '/oauth2/valid_token?access_token='.$token ; 
        $result = file_get_contents($oauthServerUrl);
        if ($result == false) {
            return false;
        }
        $result = json_decode($result);
        if ($result->errcode == 0) {
            return true;
        } else {
            return false;
        }

    }
}

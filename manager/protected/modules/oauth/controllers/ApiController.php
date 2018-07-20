<?php

namespace app\modules\oauth\controllers;

use app\modules\oauth\Module;

class ApiController extends \yii\rest\Controller
{
    public function actionToken()
    {
        /** @var Module $module */
        $module = $this->module;
        $server = $module->getServer();
        $request = $module->getRequest();
        $response = $server->handleTokenRequest($request);
        $result = $response->getParameters();
        if (array_key_exists('token_type', $result)) {
            unset($result['token_type']);
        }
        if (array_key_exists('scope', $result)) {
            unset($result['scope']);
        }

        if (isset($result['error'])) {
            return ['errcode' => 40013, 'errmsg' => $result['error']];
        } else {
            return $result;
        }
    }

    public function actionValidToken()
    {
        /** @var Module $module */
        $module = $this->module;
        $oauthServer = $module->getServer();
        $oauthRequest = $module->getRequest();
        try {
            $result = $oauthServer->verifyResourceRequest($oauthRequest);
            if ($result == false) {
                return ['errcode' => 40013, 'msg' => 'invalid token'];
            } else {
                return ['errcode' => 0];
            }
        } catch (\HttpException $httpEx) {
            \Yii::warning($httpEx->getMessage() . ', with:' . \Yii::$app->request->absoluteUrl);
            return ['errcode' => 40013, 'msg' => 'invalid token'];
        } catch (\Exception $ex) {
            \Yii::error($ex);
            return ['errcode' => 40013, 'msg' => '服务器繁忙，请稍候再试'];
        }


    }
}

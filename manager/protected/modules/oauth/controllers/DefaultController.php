<?php

namespace app\modules\oauth\controllers;

use Yii;

use app\modules\oauth\Module;

class DefaultController extends \yii\rest\Controller
{

    /**
     * http://10.5.24.22:9900/mysoft/oauth2/auth?response_type=code&client_id=testclient&state=321&redirect_uri=http:// 
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionAuth()
    {
        /** @var Module $module */
        $module = $this->module;
        $server = $module->getServer();
        $request = $module->getRequest();
        $responseInstance = $module->getResponse();
        $result = $server->validateAuthorizeRequest($request, $responseInstance);
        $response = $server->getResponse();

        if($result == false) {
            return ['errcode' => 40013, 'errmsg' => $response->getParameters()];
        }
        $authHandleResponse = $server->handleAuthorizeRequest($request, $response, $result);
        $returnUrl = $authHandleResponse->getHttpHeader('Location');
        $this->redirect($returnUrl);
    }
}
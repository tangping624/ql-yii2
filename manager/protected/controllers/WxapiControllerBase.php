<?php

namespace app\controllers;

use app\framework\web\extension\Controller;

class WxapiControllerBase extends Controller
{
    
    public function __construct($id, $module, $config = [])
    { 
        parent::__construct($id, $module, $config);
    }
    public function behaviors()
    {
        return [
            [
                'class' => 'app\framework\web\filters\OAuth2VerifyApiActionFilter',
            ]
        ];
    }
}

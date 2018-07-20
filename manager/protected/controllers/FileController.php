<?php

namespace app\controllers;

use yii\web\Controller;

class FileController extends Controller
{
    public $enableCsrfValidation = false;
    public function actionUpfile()
    {
        require(__DIR__ . '/../../web/frontend/3rd/ueditor/php/controller.php');
    }
}
<?php
namespace app\modules\ctrip\controllers;
use app\controllers\ControllerBase;
use app\modules\ctrip\services\CtripService;


class CtripController extends ControllerBase {
    private $_ctripService;
    public function __construct($id, $module, CtripService $ctripService, $config = [])
    {
        $this->_ctripService = $ctripService;
        parent::__construct($id, $module, $config);
    }


    public function actionIndex()
    {

        return $this->render('index');
    }


}

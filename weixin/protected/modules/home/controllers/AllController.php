<?php
namespace app\modules\home\controllers;
use app\controllers\ControllerBase;
use app\modules\home\services\AllService;
class AllController extends ControllerBase
{
    private $_allService;

    public function __construct($id, $module, AllService $allService, $config = [])
    {
        $this->_allService = $allService;
        parent::__construct($id, $module, $config);
    }

    //首页
    public function actionIndex()
    {

        return $this->render('index');
    }


    public function actionAjaxGetType()
    {
        $citys = $this->_allService->getTypeInfo();
        return $this->json($citys);
    }


}

<?php
namespace app\modules\appapi\controllers;
use app\controllers\ControllerBase;
use app\modules\appapi\services\AllService;
class AllController extends ControllerBase
{
    private $_allService;

    public function __construct($id, $module, AllService $allService, $config = [])
    {
        $this->_allService = $allService;
        parent::__construct($id, $module, $config);
    }

    public function actionAjaxGetType()
    {
        $citys = $this->_allService->getTypeInfo();
        return $this->json($citys);
    }


}

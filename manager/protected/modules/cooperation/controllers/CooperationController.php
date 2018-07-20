<?php
namespace app\modules\cooperation\controllers;
use app\modules\ControllerBase;
use app\modules\cooperation\services\CooperationService;
use app\modules\pub\models\ListForm;
class CooperationController extends ControllerBase{
    private $_cooperationService;
    public function __construct($id, $module,CooperationService $cooperationService, $config = [])
    {
        $this->_cooperationService = $cooperationService;
        parent::__construct($id, $module, $config);
    }


    public function actionShopType(){
        return $this->render('@app/modules/shop/views/default/type/index');
    }

    public function actionCooperationShop(){
        return $this->render('@app/modules/shop/views/default/shop/index');
    }

    
}

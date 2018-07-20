<?php
namespace app\modules\repast\controllers;
use app\modules\ControllerBase;
use app\modules\repast\services\RepastService;
use app\modules\pub\models\ListForm;
class RepastController extends ControllerBase{
    private $_repastService;
    public function __construct($id, $module,RepastService $repastService, $config = [])
    {
        $this->_repastService = $repastService;
        parent::__construct($id, $module, $config);
    }


    public function actionShopType(){
        return $this->render('@app/modules/shop/views/default/type/index');
    }

    public function actionRepastShop(){
        return $this->render('@app/modules/shop/views/default/shop/index');
    }

    
}

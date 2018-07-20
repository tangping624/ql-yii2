<?php
namespace app\modules\invest\controllers;
use app\modules\ControllerBase;
use app\modules\invest\services\InvestService;
use app\modules\pub\models\ListForm;
class InvestController extends ControllerBase{
    private $_investService;
    public function __construct($id, $module,InvestService $investService, $config = [])
    {
        $this->_investService = $investService;
        parent::__construct($id, $module, $config);
    }
    
    public function actionShopType(){
        return $this->render('@app/modules/shop/views/default/type/index');
    }

    public function actionInvestShop(){
        return $this->render('@app/modules/shop/views/default/shop/index');
    }
    
}

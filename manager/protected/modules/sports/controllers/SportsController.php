<?php
namespace app\modules\sports\controllers;
use app\modules\ControllerBase;
use app\modules\sports\services\SportsService;
use app\modules\pub\models\ListForm;
class SportsController extends ControllerBase{
    private $_sportsService;
    public function __construct($id, $module,SportsService $sportsService, $config = [])
    {
        $this->_sportsService = $sportsService;
        parent::__construct($id, $module, $config);
    }


    public function actionShopType(){
        return $this->render('@app/modules/shop/views/default/type/index');
    }

    public function actionSportsShop(){
        return $this->render('@app/modules/shop/views/default/shop/index');
    }

    
}

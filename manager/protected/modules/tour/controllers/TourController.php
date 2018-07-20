<?php
namespace app\modules\tour\controllers;
use app\modules\ControllerBase;
use app\modules\tour\services\TourService;
use app\modules\pub\models\ListForm;
class TourController extends ControllerBase{
    private $_tourService;
    public function __construct($id, $module,TourService $tourService, $config = [])
    {
        $this->_tourService = $tourService; 
        parent::__construct($id, $module, $config);
    }


     public function actionIndex()
     {
         return $this->render('index');
    }

    public function actionShopType(){
        return $this->render('@app/modules/shop/views/default/type/index');
    }

    public function actionTourShop(){
        return $this->render('@app/modules/shop/views/default/shop/index');
    }
}

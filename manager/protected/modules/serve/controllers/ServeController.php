<?php
namespace app\modules\serve\controllers;
use app\modules\ControllerBase;
use app\modules\serve\services\ServeService;
use app\modules\pub\models\ListForm;
class ServeController extends ControllerBase{
    private $_serveService;
    public function __construct($id, $module,ServeService $serveService, $config = [])
    {
        $this->_serveService = $serveService;
        parent::__construct($id, $module, $config);
    }


    public function actionShopType(){
        return $this->render('@app/modules/shop/views/default/type/index');
    }

    public function actionServeShop(){
        return $this->render('@app/modules/shop/views/default/shop/index');
    }

    
}

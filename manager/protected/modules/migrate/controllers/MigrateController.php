<?php
namespace app\modules\migrate\controllers;
use app\modules\ControllerBase;
use app\modules\migrate\services\MigrateService;
use app\modules\pub\models\ListForm;
class MigrateController extends ControllerBase{
    private $_migrateService;
    public function __construct($id, $module,MigrateService $migrateService, $config = [])
    {
        $this->_migrateService = $migrateService;
        parent::__construct($id, $module, $config);
    }


    public function actionShopType(){
        return $this->render('@app/modules/shop/views/default/type/index');
    }

    public function actionMigrateShop(){
        return $this->render('@app/modules/shop/views/default/shop/index');
    }

    
}

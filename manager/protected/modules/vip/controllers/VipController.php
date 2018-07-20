<?php
namespace app\modules\vip\controllers;
use app\modules\ControllerBase;
use app\modules\vip\services\VipService;
use app\modules\pub\models\ListForm;
class VipController extends ControllerBase{
    private $_vipService;
    public function __construct($id, $module,VipService $vipService, $config = [])
    {
        $this->_vipService = $vipService;
        parent::__construct($id, $module, $config);
    }


    public function actionShopType(){
        return $this->render('@app/modules/shop/views/default/type/index');
    }

    public function actionVipShop(){
        return $this->render('@app/modules/shop/views/default/shop/index');
    }

    
}

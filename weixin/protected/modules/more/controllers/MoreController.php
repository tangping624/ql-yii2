<?php
namespace app\modules\more\controllers;
use app\controllers\ControllerBase;
use app\modules\more\services\MoreService;
use app\modules\pub\models\ListForm;
class MoreController extends ControllerBase {
    private $_moreService;
    public function __construct($id, $module, MoreService $moreService, $config = [])
    {
        $this->_moreService = $moreService;
        parent::__construct($id, $module, $config);
    }


    public function actionIndex()
    {
        $memu['navigation_list']= $this->getNavigation('home');
        $memu['menu']='more/more/index';
        $memu['public_id'] = $this->context->publicId;
        return  $this->render('index',['menu'=>$memu]);
    }

}

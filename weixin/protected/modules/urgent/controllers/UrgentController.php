<?php
namespace app\modules\urgent\controllers;
use app\controllers\ControllerBase;
use app\modules\urgent\services\UrgentService;
use app\modules\pub\models\ListForm;
class UrgentController extends ControllerBase{
    private $_urgentService;
    public function __construct($id, $module,UrgentService $urgentService, $config = [])
    {
        $this->_urgentService = $urgentService;
        parent::__construct($id, $module, $config);
    }


     public function actionIndex()
     {
         return  $this->render('index');
    }

    public function actionAjaxIndex($pagesize=10 , $page =1) {
        $listResult = $this->_urgentService->getEmergencyList((int)$pagesize,(int)$page);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pagesize;
        return $this->json($model);
    }

    public function actionDetails($id='1'){
        $data=$this->_urgentService->getDetails($id);

        return $this->render('detail',['details'=>$data]);

    }

}

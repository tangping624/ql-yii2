<?php
namespace app\modules\news\controllers;
use app\controllers\ControllerBase;
use app\modules\pub\models\ListForm;
use app\modules\news\services\NewsService;
use app\framework\utils\StringHelper;
use app\modules\news\models\MNewsForm;
class NewsController  extends ControllerBase{
    private $_newsService; 
    public function __construct($id, $module,NewsService $newsService, $config = [])
    {
        $this->_newsService = $newsService; 
        parent::__construct($id, $module, $config);
    }
   
    //新鲜事
     public function actionIndex(){
        return $this->render('index');
    }

    //新鲜事列表
    public function actionAjaxNewsList($pageSize=10 , $page =1,$id='') {
        $listResult = $this->_newsService->getNewsList((int)$pageSize,(int)$page,$id);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pageSize;
        return $this->json($model);
    }


    //新鲜事新增+编辑
    public function actionDetails($id=''){

       $data = $this->_newsService->getNew($id);
       return $this->render('details', ['data'=>$data]);
    }


}

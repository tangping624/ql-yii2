<?php
namespace app\modules\appapi\controllers;
use app\controllers\ControllerBase;
use app\modules\pub\models\ListForm;
use app\modules\appapi\services\NewsService;
class NewsController  extends ControllerBase{
    private $_newsService; 
    public function __construct($id, $module,NewsService $newsService, $config = [])
    {
        $this->_newsService = $newsService; 
        parent::__construct($id, $module, $config);
    }

    //新鲜事列表
    public function actionAjaxNewsList() {
        $page=empty($_REQUEST['page'])?1:$_REQUEST['page'];
        $pageSize=empty($_REQUEST['pageSize'])?10:$_REQUEST['pageSize'];
        $id=empty($_REQUEST['id'])?'':$_REQUEST['id'];
        $listResult = $this->_newsService->getNewsList((int)$pageSize,(int)$page,$id);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pageSize;
        return $this->json($model);
    }


    //新鲜事详情
    public function actionDetails($id=''){

       $data = $this->_newsService->getNew($id);
       return $this->json($data);
    }


}

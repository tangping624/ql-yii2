<?php
namespace app\modules\appapi\controllers;
use app\controllers\ControllerBase;
use app\modules\appapi\services\WikiService;
use app\modules\pub\models\ListForm;
use app\modules\appapi\utils\WebUtils;
class WikiController extends ControllerBase {
    private $_wikiService;
    public function __construct($id, $module, WikiService $wikiService, $config = [])
    {
        $this->_wikiService = $wikiService;
        parent::__construct($id, $module, $config);
    }

    //百科
    public function actionIndex()
    {
        $advert=$this->_wikiService->getAdvert($appcode='wiki');//获取百科广告
        $type=$this->_wikiService->getWikiType();//获取百科分类

        $data = [
            'advert' => $advert,
            'type' => $type,

        ];
        return  $this->json($data);
    }

    //百科列表
    public function actionAjaxWiki()
    {

        $page=empty($_REQUEST['page'])?1:$_REQUEST['page'];
        $pageSize=empty($_REQUEST['pageSize'])?10:$_REQUEST['pageSize'];
        $keywords = empty($_REQUEST['keywords'])?'':$_REQUEST['keywords'];
        $id=empty($_REQUEST['id'])?'':$_REQUEST['id'];
        $listResult = $this->_wikiService->getWikiList((int)$pageSize,(int)$page,$id,$keywords);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = (int)$page;
        $model->pageSize = (int)$pageSize;
        return $this->json($model);
    }

    //百科详情
    public function actionDetails()
    {
        if (!WebUtils::IsRequestParam('id')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提百科ID[id]']);
        }
        $id= $_REQUEST['id'];
        $data=$this->_wikiService->getDetails($id);
        return $this->json($data);

    }
/*
    //百科搜索

    public function actionSearch(){
        return  $this->render('search');
    }

    public function actionSearchIndex(){
        return  $this->render('search-list');
    }*/


}

<?php
namespace app\modules\wiki\controllers;
use app\controllers\ControllerBase;
use app\modules\wiki\services\WikiService;
use app\modules\pub\models\ListForm;
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
        return  $this->render('index',['advert'=>$advert,'type'=>$type]);
    }

    //百科列表
    public function actionAjaxWiki($pageSize=10 , $page =1,$id='',$keywords='')
    {
        $listResult = $this->_wikiService->getWikiList((int)$pageSize,(int)$page,$id,$keywords);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = (int)$page;
        $model->pageSize = (int)$pageSize;
        return $this->json($model);
    }

    //百科详情
    public function actionDetails($id='')
    {
        $data=$this->_wikiService->getDetails($id);
        return $this->render('detail',['details'=>$data]);

    }

    //百科搜索

    public function actionSearch(){
        return  $this->render('search');
    }

    public function actionSearchIndex(){
        return  $this->render('search-list');
    }


}

<?php
namespace app\modules\tour\controllers;
use app\controllers\ControllerBase;
use app\modules\tour\services\TourService;
use app\modules\pub\models\ListForm;
use app\modules\wiki\services\WikiService;
use app\modules\pub\services\PublicService;
class TourController extends ControllerBase{
    private $_tourService;
    private $_wikiService;
    private $_publicService;
    public function __construct($id, $module,TourService $tourService,WikiService $wikiService ,PublicService $publicService, $config = [])
    {
        $this->_tourService = $tourService;
        $this->_wikiService = $wikiService;
        $this->_publicService = $publicService;
        parent::__construct($id, $module, $config);
    }


    public function actionIndex($id='',$appcode='')
    {

        $advert=$this->_wikiService->getAdvert($appcode);//获取移民广告
        $type= $this->_publicService->getType($id);//获取百科分类
        $news= $this->_publicService->getNews($id);//获取百科新鲜事
        $city= $this->_publicService->getCity();//获取区域城市

        return  $this->render('index',['advert'=>$advert,'type'=>$type,'news'=>$news,'city'=>$city]);
    }

    //旅游列表
    public function actionAjaxIndex($pagesize=10 , $page =1,$id='',$type_id='',$city_id='',$keyword='',$city_pid='') {
        $listResult = $this->_tourService->getTourList((int)$pagesize,(int)$page,$id,$type_id,$city_id,$keyword,$city_pid);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pagesize;
        return $this->json($model);
    }




}

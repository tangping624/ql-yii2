<?php
namespace app\modules\house\controllers;
use app\controllers\ControllerBase;
use app\modules\pub\models\ListForm;
use app\modules\house\services\HouseService;
use app\modules\pub\services\PublicService;
use app\modules\pub\services\SellerService;
use app\framework\utils\StringHelper;
class HouseController  extends ControllerBase{
    private $_houseService;
    private $_publicService;
    private $_sellerService;
    public function __construct($id, $module,HouseService $houseService,PublicService $publicService,SellerService $sellerService, $config = [])
    {
        $this->_houseService = $houseService;
        $this->_publicService = $publicService;
        $this->_sellerService = $sellerService;
        parent::__construct($id, $module, $config);
    }
   
    //房产
     public function actionIndex($id='')
     {
         $advert = $this->_houseService->getAdvert($appcode ='house');//获取房产广告
         $news= $this->_publicService->getNews($id);//获取房产新鲜事
         $type=$this->_houseService->getHouseType();//获取房产类别
         $city = $this->_publicService->getCity();//获取城市
         return $this->render('index',['advert'=>$advert,'type'=>$type,'news'=>$news,'city'=>$city]);
    }

    //房产商家列表
    public function actionAjaxHouseList($pageSize=10 , $page =1,$id='',$tag_id='',$city_id='',$keyword='',$city_pid='') {
        $listResult = $this->_houseService->getHouseList((int)$pageSize,(int)$page,$id,$tag_id,$city_id,$keyword,$city_pid);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pageSize;
        return $this->json($model);
    }

    //分类页面
    public function actionTypeIndex($id=''){
        $advert = $this->_houseService->getAdvert($appcode ='house');//获取房产广告
        $type=$this->_houseService->getHouseType();//获取房产类别
        $city = $this->_publicService->getCity();//获取城市
        return $this->render('type-index',['advert'=>$advert,'type'=>$type,'city'=>$city]);
    }



}

<?php
namespace app\modules\appapi\controllers;
use app\controllers\ControllerBase;
use app\modules\appapi\services\HomeService;
use app\modules\appapi\services\CityService;
use app\framework\utils\Security;
use app\framework\sms\SmsService;
use app\modules\pub\models\ListForm;
class HomeController extends AppControllerBase {
    private $_homeService;
    private $_cityService;
    public function __construct($id, $module, HomeService $homeService,CityService $cityService, $config = [])
    {
        $this->_homeService = $homeService;
        $this->_cityService = $cityService;
        parent::__construct($id, $module, $config);
    }

    //首页
    public function actionIndex()
    {
        $memu['navigation_list'] = $this->getNavigation('home');
        $memu['menu'] = '/home/home/index';
       // $memu['public_id'] = $this->context->publicId;
        $type = $this->_homeService->getSellerType();//获取分类
        $seller = [];
        $advert = $this->_homeService->getHomeAdvert($appcode = 'home');//获取首页广告
        $otherAdvert = $this->_homeService->getOtherHomeAdvert($appcode = 'home');//获取首页广告
        $newData = [];
        foreach ($otherAdvert as $value) {
            $newData[$value['grouporder']] = $value;
        }
        //print_r($newData);die;

        $data = [
            'menu' => $memu,
            'type' => $type,
            'seller' => $seller,
            'advert' => $advert,
            'otherAdvert'=>$newData,
            'city_id' => \Yii::$app->request->get("city_id")
        ];
        return $this->json($data);
    }

    //ajax-get-recommend-seller
    public function actionAjaxGetRecommendSeller(){
        $cityPid = empty($_REQUEST['cityPid'])?'':$_REQUEST['cityPid'];
        $seller = $this->_homeService->getSellerRecommend($cityPid);
        return $this->json($seller);
    }

    //ajax-get-seller-list
    public function actionAjaxGetSellerList()
    {
        $page=empty($_REQUEST['page'])?1:$_REQUEST['page'];
        $pagesize=empty($_REQUEST['pagesize'])?10:$_REQUEST['pagesize'];
        $cityPid = empty($_REQUEST['cityPid'])?'':$_REQUEST['cityPid'];
        $typePid=empty($_REQUEST['typePid'])?'':$_REQUEST['typePid'];
        $appcode=empty($_REQUEST['appcode'])?'':$_REQUEST['appcode'];

        $seller = $this->_homeService->getSellerList($cityPid, $typePid, $page, $pagesize,$appcode);
        return $this->json($seller);
    }

    //搜索
    public function actionSearch(){
        $data= $this->_homeService->getHotSearch();
        return $this->json($data);
    }


    public function actionAjaxGetSearchList(){

        $page=empty($_REQUEST['page'])?1:$_REQUEST['page'];
        $pagesize=empty($_REQUEST['pagesize'])?10:$_REQUEST['pagesize'];
        $keywords = empty($_REQUEST['keywords'])?'':$_REQUEST['keywords'];
        $type=empty($_REQUEST['type'])?'':$_REQUEST['type'];
        if(!empty($keywords)&&!ctype_space($keywords)){
            $this->_homeService->saveHotSearch($keywords);
        }
        $listResult = $this->_homeService->getSearchList((int)$pagesize,(int)$page,$type,$keywords);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pagesize;
        return $this->json($model);
    }

    public function  getNavigation($appcode){
        if(empty($appcode)){
            throw new \InvalidArgumentException('$appcode对象不存在');
        }
        $brannerService =  \Yii::$container->get('app\modules\pub\services\BrannerService');
        return $brannerService->getNavigation($appcode );
    }

    //默认城市
    //ajax-loc-city
    public function actionAjaxLocCity()
    {
        $citys = $this->_cityService->locCityByName();
        return $this->json($citys);
    }
}

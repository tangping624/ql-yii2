<?php 
namespace app\modules\home\controllers;
use app\controllers\ControllerBase;
use app\modules\home\services\HomeService;
use app\framework\utils\Security;
use app\framework\sms\SmsService;
use app\modules\pub\models\ListForm;
class HomeController extends ControllerBase {
     private $_homeService;
     public function __construct($id, $module, HomeService $homeService, $config = [])
    {
        $this->_homeService = $homeService;
        parent::__construct($id, $module, $config);
    }

    //首页
    public function actionIndex()
    {
        //$user = $this->context->user;
        //$this->context->memberId;
        $public_id = $this->context->publicId;
        $url = \Yii::$app->request->absoluteUrl;
        $wxjsdk = [];//$this->_homeService->getJssdksign($public_id, urldecode($url));

        $memu['navigation_list'] = $this->getNavigation('home');
        $memu['menu'] = 'home/home/index';
        $memu['public_id'] = $this->context->publicId;
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
            'wxjsdk' => $wxjsdk,
            'city_id' => \Yii::$app->request->get("city_id")
        ];
        return $this->render('index', $data);
    }

    //ajax-get-recommend-seller
    public function actionAjaxGetRecommendSeller($cityPid=''){
        $seller = $this->_homeService->getSellerRecommend($cityPid);
        return $this->json($seller);
    }

    //ajax-get-seller-list
    public function actionAjaxGetSellerList($cityPid='',$typePid='',$pagesize=10 , $page=1,$appcode='')
    {
        $seller = $this->_homeService->getSellerList($cityPid, $typePid, $page, $pagesize,$appcode);
        return $this->json($seller);
    }

    //搜索
    public function actionSearch(){
        $data= $this->_homeService->getHotSearch();
        return $this->render('search',['data'=>$data] );
    }

    public function actionSearchList(){
        return $this->render('search-list' );
    }

    public function actionAjaxGetSearchList($pagesize=10 , $page =1,$type='',$keywords=''){
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

    public function actionNotice(){
        return $this->render('notice');
    }
}

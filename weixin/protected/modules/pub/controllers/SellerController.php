<?php
namespace app\modules\pub\controllers;
use app\controllers\ControllerBase;
use app\modules\pub\services\SellerService;
use app\modules\pub\models\ListForm;
use app\modules\wiki\services\WikiService;
use app\modules\pub\services\PublicService;
class SellerController extends ControllerBase{
    private $_sellerService;
    private $_wikiService;
    private $_publicService;
    public function __construct($id, $module,SellerService $sellerService, WikiService $wikiService ,PublicService $publicService,$config = [])
    {
        $this->_sellerService = $sellerService;
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

    //商家列表
    public function actionAjaxIndex($pagesize=10 , $page =1,$id='',$type_id='',$city_id='',$keyword='',$city_pid='') {
        $listResult = $this->_sellerService->getBlogList((int)$pagesize,(int)$page,$id,$type_id,$city_id,$keyword,$city_pid);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pagesize;
        return $this->json($model);
    }

    public function actionDetails($id='',$product_id=''){
        $cookie='';
        if(!empty($_COOKIE['u'])){
            $cookie=$_COOKIE['u'];
        }
        $memberId=$this->context->memberId;
      /*  $public_id = $this->context->publicId;
        $url = \Yii::$app->request->absoluteUrl;
        $wxjsdk = $this->_sellerService->getJssdksign($public_id, urldecode($url));*/
        $data=$this->_sellerService->getDetails($id,$memberId);
        $images=$this->_sellerService->getImages($id);
       // $count=$this->_sellerService->getCount($id);

        //保存浏览
        if(!empty($memberId)) {
            $re = $this->_sellerService->IsPraise($id, $memberId, $type = 3);
            if ($re) {
                $this->_sellerService->clickPraise($id, $memberId, $type = 3, $product_id);
            }
        }
        return $this->render('details',['details'=>$data,'images'=>$images,/*'count'=>$count,*/ /*'wxjsdk' => $wxjsdk,*/'cookie'=> $cookie]);
    }

    //商家点赞/收藏
    public function actionPraise($id='',$type=1,$product_id=''){
        $memberId=$this->context->memberId;
        if(empty($memberId)){
            $this->redirect("/me/me/login-index");
        }
        $rst = $this->_sellerService->clickPraise($id, $memberId, $type,$product_id);
        if ($rst) {
            $data = $this->_sellerService->setAddOne($id,$type);
             return $this->json(['result' => true, 'msg' => '操作成功']);
        } else {
             return $this->json(['result' => false, 'code' => 200, 'msg' => '操作失败']);
            }

    }


    //取消收藏商家
    public function actionCancel($id=''){
        $memberId=$this->context->memberId;
        $re=$this->_sellerService->cancelCollection($memberId,$id);
        if ($re) {
            $data = $this->_sellerService->setSubtractOne($id);
            return $this->json(['result' => true, 'msg' => '取消收藏成功']);
        } else {
            return $this->json(['result' => false, 'code' => 200, 'msg' => '取消收藏失败']);
        }
    }

    //取消收藏商品
    public function actionCancelGoods($id=''){
        $memberId=$this->context->memberId;
        $re=$this->_sellerService->cancelCollectionGoods($memberId,$id);
        if ($re) {
            return $this->json(['result' => true, 'msg' => '取消收藏成功']);
        } else {
            return $this->json(['result' => false, 'code' => 200, 'msg' => '取消收藏失败']);
        }
    }

    public function actionTypeIndex($id='',$appcode='')
    {
        $advert=$this->_wikiService->getAdvert($appcode);//获取移民广告
        $type= $this->_publicService->getType($id);//获取百科分类
        $news= $this->_publicService->getNews($id);//获取百科新鲜事
        $city= $this->_publicService->getCity();//获取区域城市

        return  $this->render('list',['advert'=>$advert,'type'=>$type,'news'=>$news,'city'=>$city]);
    }

    //显示随机商家
    public function actionRoundMerchant($id,$type_pid=''){
        $data=$this->_sellerService->getRoundMerchant($id,$type_pid);
        return $this->json($data);

    }



}

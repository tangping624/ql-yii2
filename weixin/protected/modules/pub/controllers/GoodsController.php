<?php
namespace app\modules\pub\controllers;
use app\controllers\ControllerBase;
use app\modules\pub\services\GoodsService;
use app\modules\pub\models\ListForm;
use app\modules\wiki\services\WikiService;
use app\modules\pub\services\PublicService;
use app\modules\pub\services\SellerService;
class GoodsController extends ControllerBase{
    private $_goodsService;
    private $_wikiService;
    private $_publicService;
    private $_sellerService;
    public function __construct($id, $module,SellerService $sellerService,GoodsService $goodsService, WikiService $wikiService ,PublicService $publicService,$config = [])
    {
        $this->_goodsService =$goodsService;
        $this->_wikiService = $wikiService;
        $this->_publicService = $publicService;
        $this->_sellerService = $sellerService;
        parent::__construct($id, $module, $config);
    }

    //商品列表
    public function actionProductIndex($appcode='',$seller_id='')
    {
        $advert = $this->_publicService->getAdvert($appcode);//获取房产广告
        $type='';
       //if($appcode=='shop'){
            $type=$this->_goodsService->getShopType($seller_id);//获取购物惠分类

       // }
        return $this->render('product-index',['advert'=>$advert,'type'=>$type,'appcode'=>$appcode]);
    }
    
    //商品列表
    public function actionAjaxProductList($pageSize=10 , $page =1,$type_id='',$seller_id='')
    {
        $listResult = $this->_goodsService->getShopList((int)$pageSize,(int)$page,$type_id,$seller_id);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = (int)$page;
        $model->pageSize = (int)$pageSize;
        return $this->json($model);
    }

    //商品详情
    public function actionProductDetails($seller_id='',$product_id=''){

        $cookie='';
        if(!empty($_COOKIE['u'])){
            $cookie=$_COOKIE['u'];
        }
        $memberId=$this->context->memberId;
        $data=$this->_goodsService->getProductDetails($product_id,$memberId);
        if(!empty($memberId)) {
            //保存浏览
            $re = $this->_goodsService->IsProductPraise($product_id, $memberId, $type = 3);
            if ($re) {
                $this->_sellerService->clickPraise($seller_id, $memberId, $type = 3, $product_id);
            }
        }
        return $this->render('details',['details'=>$data,'cookie'=> $cookie]);

    }
    
}

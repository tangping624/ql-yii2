<?php
namespace app\modules\appapi\controllers;
use app\controllers\ControllerBase;
use app\modules\appapi\services\GoodsService;
use app\modules\pub\models\ListForm;
use app\modules\appapi\services\WikiService;
use app\modules\appapi\services\PublicService;
use app\modules\appapi\services\SellerService;
use app\modules\appapi\utils\WebUtils;
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
    public function actionProductIndex()
    {
        if (!WebUtils::IsRequestParam('appcode')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提分类AppCode[appcode]']);
        }
        if (!WebUtils::IsRequestParam('seller_id')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提商家ID[seller_id]']);
        }
        $appcode=$_REQUEST['appcode'];
        $seller_id=$_REQUEST['seller_id'];
        $advert = $this->_publicService->getAdvert($appcode);//获取房产广告
        $type=$this->_goodsService->getShopType($seller_id);//获取购物惠分类
        $data=[
            'advert'=>$advert,
            'type'=>$type,
            'appcode'=>$appcode
        ];
        return $this->json($data);
    }
    
    //商品列表
    public function actionAjaxProductList()
    {
        $page=empty($_REQUEST['page'])?1:$_REQUEST['page'];
        $pageSize=empty($_REQUEST['pageSize'])?10:$_REQUEST['pageSize'];
        $type_id = empty($_REQUEST['type_id'])?'':$_REQUEST['type_id'];
        $seller_id=empty($_REQUEST['seller_id'])?'':$_REQUEST['seller_id'];
        $listResult = $this->_goodsService->getShopList((int)$pageSize,(int)$page,$type_id,$seller_id);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = (int)$page;
        $model->pageSize = (int)$pageSize;
        return $this->json($model);
    }

    //商品详情
    public function actionProductDetails(){

        $product_id = empty($_REQUEST['product_id'])?'':$_REQUEST['product_id'];
        $seller_id=empty($_REQUEST['seller_id'])?'':$_REQUEST['seller_id'];
        $memberId=empty($_REQUEST['member_id'])?'':$_REQUEST['member_id'];
        $data=$this->_goodsService->getProductDetails($product_id,$memberId);
        if(!empty($memberId)) {
            //保存浏览
            $re = $this->_goodsService->IsProductPraise($product_id, $memberId, $type = 3);
            if ($re) {
                $this->_sellerService->clickPraise($seller_id, $memberId, $type = 3, $product_id);
            }
        }
        return $this->json($data);
    }
    
}

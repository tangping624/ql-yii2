<?php
namespace app\modules\appapi\controllers;
use app\controllers\ControllerBase;
use app\modules\appapi\services\SellerService;
use app\modules\pub\models\ListForm;
use app\modules\appapi\services\WikiService;
use app\modules\appapi\services\PublicService;
use app\modules\appapi\utils\WebUtils;
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


     public function actionIndex()
     {
         if (!WebUtils::IsRequestParam('id')) {
             return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提分类ID[id]']);
         }
         if (!WebUtils::IsRequestParam('appcode')) {
             return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提分类appcode[appcode]']);
         }
         $id= $_REQUEST['id'];
         $appcode=$_REQUEST['appcode'];
         $advert=$this->_wikiService->getAdvert($appcode);//获取移民广告
         $type= $this->_publicService->getType($id);//获取百科分类
         $news= $this->_publicService->getNews($id);//获取百科新鲜事
         $city= $this->_publicService->getCity();//获取区域城市
         $data=[
             'advert'=>$advert,
             'type'=>$type,
             'news'=>$news,
             'city'=>$city,
         ];
         return  $this->json($data);
    }

    //商家列表
    public function actionAjaxIndex() {
        $page=empty($_REQUEST['page'])?1:$_REQUEST['page'];
        $pagesize=empty($_REQUEST['pagesize'])?10:$_REQUEST['pagesize'];
        $city_pid = empty($_REQUEST['city_pid'])?'':$_REQUEST['city_pid'];
        $type_id=empty($_REQUEST['type_id'])?'':$_REQUEST['type_id'];
        $city_id = empty($_REQUEST['city_id'])?'':$_REQUEST['city_id'];
        $keyword = empty($_REQUEST['keyword'])?'':$_REQUEST['keyword'];
        $id=empty($_REQUEST['id'])?'':$_REQUEST['id'];
        $listResult = $this->_sellerService->getBlogList((int)$pagesize,(int)$page,$id,$type_id,$city_id,$keyword,$city_pid);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pagesize;
        return $this->json($model);
    }

    public function actionDetails(){
        $memberId=empty($_REQUEST['member_id'])?'':$_REQUEST['member_id'];
        $id=empty($_REQUEST['id'])?'':$_REQUEST['id'];
        $product_id=empty($_REQUEST['product_id'])?'':$_REQUEST['product_id'];
        $data=$this->_sellerService->getDetails($id,$memberId);
        $images=$this->_sellerService->getImages($id);
        //保存浏览
        if(!empty($memberId)) {
            $re = $this->_sellerService->IsPraise($id, $memberId, $type = 3);
            if ($re) {
                $this->_sellerService->clickPraise($id, $memberId, $type = 3, $product_id);
            }
        }
        $data=[
            'details'=>$data,
            'images'=>$images
        ];
        return $this->json($data);
    }

    //商家点赞/收藏
    public function actionPraise(){
        if (!WebUtils::IsRequestParam('id')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提商家ID[id]']);
        }
        if (!WebUtils::IsRequestParam('member_id')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提会员ID[member_id]']);
        }
        if (!WebUtils::IsRequestParam('type')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提类型[type]']);
        }

        $id= $_REQUEST['id'];
        $type= $_REQUEST['type'];
        $memberId= $_REQUEST['member_id'];
        $product_id=empty($_REQUEST['product_id'])?'':$_REQUEST['product_id'];
        $rst = $this->_sellerService->clickPraise($id, $memberId, $type,$product_id);
        if ($rst) {
            $data = $this->_sellerService->setAddOne($id,$type);
             return $this->json(['result' => true, 'msg' => '操作成功']);
        } else {
             return $this->json(['result' => false, 'code' => 200, 'msg' => '操作失败']);
            }

    }


    //取消收藏商家
    public function actionCancel(){
        if (!WebUtils::IsRequestParam('id')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提商家ID[id]']);
        }
        if (!WebUtils::IsRequestParam('member_id')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提会员ID[member_id]']);
        }
        $id= $_REQUEST['id'];
        $memberId= $_REQUEST['member_id'];
        $re=$this->_sellerService->cancelCollection($memberId,$id);
        if ($re) {
            $data = $this->_sellerService->setSubtractOne($id);
            return $this->json(['result' => true, 'msg' => '取消收藏成功']);
        } else {
            return $this->json(['result' => false, 'code' => 200, 'msg' => '取消收藏失败']);
        }
    }

    //取消收藏商品
    public function actionCancelGoods(){
        if (!WebUtils::IsRequestParam('id')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提产品ID[id]']);
        }
        if (!WebUtils::IsRequestParam('member_id')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提会员ID[member_id]']);
        }
        $id= $_REQUEST['id'];
        $memberId= $_REQUEST['member_id'];
        $re=$this->_sellerService->cancelCollectionGoods($memberId,$id);
        if ($re) {
            return $this->json(['result' => true, 'msg' => '取消收藏成功']);
        } else {
            return $this->json(['result' => false, 'code' => 200, 'msg' => '取消收藏失败']);
        }
    }

    public function actionTypeIndex()
    {
        if (!WebUtils::IsRequestParam('id')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提分类ID[id]']);
        }
        if (!WebUtils::IsRequestParam('appcode')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提分类appcode[appcode]']);
        }
        $id= $_REQUEST['id'];
        $appcode=$_REQUEST['appcode'];
        $advert=$this->_wikiService->getAdvert($appcode);//获取移民广告
        $type= $this->_publicService->getType($id);//获取百科分类
        $news= $this->_publicService->getNews($id);//获取百科新鲜事
        $city= $this->_publicService->getCity();//获取区域城市
        $data=[
            'advert'=>$advert,
            'type'=>$type,
            'news'=>$news,
            'city'=>$city,
        ];
        return  $this->json($data);
    }

    //显示随机商家
    public function actionRoundMerchant(){
        if (!WebUtils::IsRequestParam('id')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提分类ID[id]']);
        }
        if (!WebUtils::IsRequestParam('type_pid')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提分类ID[type_pid]']);
        }
        $id= $_REQUEST['id'];
        $type_pid=$_REQUEST['type_pid'];
        $data=$this->_sellerService->getRoundMerchant($id,$type_pid);
        return $this->json($data);

    }



}

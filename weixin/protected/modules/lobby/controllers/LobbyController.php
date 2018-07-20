<?php
namespace app\modules\lobby\controllers;
use app\controllers\ControllerBase;
use app\modules\lobby\services\LobbyService;
use app\modules\pub\services\SellerService;
use app\modules\pub\models\ListForm;
class LobbyController extends ControllerBase{
    private $_lobbyService;
    private $_sellerService;

    public function __construct($id, $module,LobbyService $lobbyService,SellerService $sellerService, $config = [])
    {
        $this->_lobbyService = $lobbyService;
        $this->_sellerService = $sellerService;

        parent::__construct($id, $module, $config);
    }


     public function actionIndex()
     {
         return $this->render('index');
    }

    //游说列表
    public function actionAjaxIndex($pagesize=10 , $page =1) {
        $listResult = $this->_lobbyService->getBlogList((int)$pagesize,(int)$page);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pagesize;
        return $this->json($model);
    }

    public function actionDetails($id=''){
        $cookie='';
        if(!empty($_COOKIE['u'])){
            $cookie=$_COOKIE['u'];
        }
        //改变浏览数量
        $memberId=$this->context->memberId;
        $this->_lobbyService->updateQuantity($id);
        $data=$this->_lobbyService->getDetails($id, $memberId);
        return $this->render('details',['details'=>$data,'cookie'=> $cookie]);
    }

    //游说点赞/收藏
    public function actionPraise($id='',$type=1){
        $memberId=$this->context->memberId;
        if(empty($memberId)){
            $this->redirect("/me/me/login-index");
        }
        $rst = $this->_lobbyService->clickPraise($id, $memberId, $type);
        if ($rst) {
            $data = $this->_sellerService->setAddOne($id,$type);
            return $this->json(['result' => true, 'msg' => '操作成功']);
        } else {
            return $this->json(['result' => false, 'code' => 200, 'msg' => '操作失败']);
        }

    }
    //取消游说收藏
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
}

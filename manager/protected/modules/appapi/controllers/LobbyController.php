<?php
namespace app\modules\appapi\controllers;
use app\controllers\ControllerBase;
use app\modules\appapi\services\LobbyService;
use app\modules\appapi\services\SellerService;
use app\modules\pub\models\ListForm;
use app\modules\appapi\utils\WebUtils;
class LobbyController extends ControllerBase{
    private $_lobbyService;
    private $_sellerService;

    public function __construct($id, $module,LobbyService $lobbyService,SellerService $sellerService, $config = [])
    {
        $this->_lobbyService = $lobbyService;
        $this->_sellerService = $sellerService;

        parent::__construct($id, $module, $config);
    }

    //游说列表
    public function actionAjaxIndex() {
        $page=empty($_REQUEST['page'])?1:$_REQUEST['page'];
        $pagesize=empty($_REQUEST['pagesize'])?10:$_REQUEST['pagesize'];
        $listResult = $this->_lobbyService->getBlogList((int)$pagesize,(int)$page);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pagesize;
        return $this->json($model);
    }

    public function actionDetails(){
        if (!WebUtils::IsRequestParam('id')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提游说ID[id]']);
        }
        $id= $_REQUEST['id'];
        $memberId=empty($_REQUEST['member_id'])?'':$_REQUEST['member_id'];
       /* $cookie='';
        if(!empty($_COOKIE['u'])){
            $cookie=$_COOKIE['u'];
        }*/
        $this->_lobbyService->updateQuantity($id);
        $data=$this->_lobbyService->getDetails($id, $memberId);
        $data=[
            'data'=>$data,
           // 'cookie'=>$cookie
        ];
        return $this->json($data);
    }

    //游说点赞/收藏
    public function actionPraise(){
        if (!WebUtils::IsRequestParam('id')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提游说ID[id]']);
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
        $rst = $this->_lobbyService->clickPraise($id, $memberId, $type);
        if ($rst) {
            $data = $this->_sellerService->setAddOne($id,$type);
            return $this->json(['result' => true, 'msg' => '操作成功']);
        } else {
            return $this->json(['result' => false, 'code' => 200, 'msg' => '操作失败']);
        }

    }
    //取消游说收藏
    public function actionCancel(){
        if (!WebUtils::IsRequestParam('id')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提游说ID[id]']);
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
}

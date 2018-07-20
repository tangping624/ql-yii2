<?php
namespace app\modules\appapi\controllers;
use app\modules\appapi\services\MemberApiService;
use app\modules\appapi\utils\WebUtils;
use app\framework\sms\SmsService;
use app\framework\utils\StringHelper;
use app\modules\pub\models\ListForm;
use app\entities\lobby\MBlog;
class MemberController extends AppControllerBase{
    
    private $_memberApiService;
    private $_smsService;

    public function __construct($id, $module,MemberApiService $memberApiService,SmsService $smsService,   $config = [])
    {
        $this->_memberApiService = $memberApiService;
        $this->_smsService = $smsService;
        parent::__construct($id, $module, $config);
    }

    //我的足迹
    public function actionAjaxMyTrack()
    {
        if (!WebUtils::IsRequestParam('memberId')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提用户Id[memberId]']);
        }
        $memberId = $_REQUEST['memberId'];
        if (!WebUtils::IsRequestParam('type')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提分类[type]']);
        }
        $type = $_REQUEST['type'];
        $pagesize ='';
        if(WebUtils::IsRequestParam('pagesize')) {
            $pagesize = $_REQUEST['pagesize'];
        }
        $page ='';
        if(WebUtils::IsRequestParam('page')) {
            $page = $_REQUEST['page'];
        }
        if(empty($pagesize)){
            $pagesize=10;
        }
        if(empty($page)){
            $page=1;
        }
        $listResult=   $this->_memberApiService->getTrack((int)$pagesize,(int)$page, $memberId,$type);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pagesize;
        return $this->json($model);
    }


    //我的收藏
    public function actionAjaxMyCollection()
    {
        if (!WebUtils::IsRequestParam('memberId')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提用户Id[memberId]']);
        }
        $memberId = $_REQUEST['memberId'];
        if (!WebUtils::IsRequestParam('type')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提分类[type]']);
        }
        $type = $_REQUEST['type'];
        $pagesize ='';
        if(WebUtils::IsRequestParam('pagesize')) {
            $pagesize = $_REQUEST['pagesize'];
        }
        $page ='';
        if(WebUtils::IsRequestParam('page')) {
            $page = $_REQUEST['page'];
        }
        if(empty($pagesize)){
            $pagesize=10;
        }
        if(empty($page)){
            $page=1;
        }
        $listResult=$this->_memberApiService->getCollection((int)$pagesize,(int)$page, $memberId,$type);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pagesize;
        return $this->json($model);
    }



    //删除
    public function actionDelete()
    {
        if (!WebUtils::IsRequestParam('id')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提id[id]']);
        }
        $id = $_REQUEST['id']; 
        $rst=$this->_memberApiService->deleteTrack($id);
        if ($rst) {
            return $this->json(['result' => true, 'code' => 200, 'msg' => '删除成功']);
        } else {
            return $this->json(['result' => false, 'code' => 500, 'msg' => '删除失败']);
        }

    }

    //我的点赞
    public function actionAjaxMyPraise()
    {
        if (!WebUtils::IsRequestParam('memberId')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提用户Id[memberId]']);
        }
        $memberId = $_REQUEST['memberId'];
        if (!WebUtils::IsRequestParam('type')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提分类[type]']);
        }
        $type = $_REQUEST['type'];
        $pagesize ='';
        if(WebUtils::IsRequestParam('pagesize')) {
            $pagesize = $_REQUEST['pagesize'];
        }
        $page ='';
        if(WebUtils::IsRequestParam('page')) {
            $page = $_REQUEST['page'];
        }
        if(empty($pagesize)){
            $pagesize=10;
        }
        if(empty($page)){
            $page=1;
        }
        $listResult=$this->_memberApiService->getPraise((int)$pagesize,(int)$page, $memberId,$type);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pagesize;
        return $this->json($model);
    }

    
    //游说列表
    public function actionAjaxLobbyIndex()
    {
        if (!WebUtils::IsRequestParam('memberId')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提用户Id[memberId]']);
        }
        $memberId = $_REQUEST['memberId'];
        $pagesize ='';
        if(WebUtils::IsRequestParam('pagesize')) {
            $pagesize = $_REQUEST['pagesize'];
        }
        $page ='';
        if(WebUtils::IsRequestParam('page')) {
            $page = $_REQUEST['page'];
        }
        if(empty($pagesize)){
            $pagesize=10;
        }
        if(empty($page)){
            $page=1;
        }
        $listResult = $this->_memberApiService->getLobbyList((int)$pagesize,(int)$page, $memberId);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pagesize;
        return $this->json($model);
    }

    //游说保存
    public function actionSave($id=''){
        try {
            $id=isset($_POST['id'])?$_POST['id']:'';
            $memberId=$this->context->memberId;
            $title=$_POST['title'];
            $photo=$_POST['photo'];
            $content=$_POST['content'];
            if(empty($id)) {
                $blog=new MBlog();
                $blog->id = StringHelper::uuid();
                $blog->member_id = $memberId;
            }else{
                $blog= $this->_memberApiService->getLobby($id);
            }
            $blog->title = $title;
            $blog->photo = $photo;
            $blog->content = $content;
            $check= $this->_memberApiService->saveBlog($blog, $memberId);
            if($check){
                return $this->json(['result' => true, 'code' => 200,'id' => $blog->id]);
            }
            else{
                return $this->json(['result' => false, 'code' => 200, 'msg' =>'保存失败']);
            }
        } catch (\Exception $ex) {
            return $this->json(['result' => false, 'code' => 500, 'msg' => $ex->getMessage()]);
        }
    }

    //新增/编辑
    public function actionLobbyAdd()
    {
        $id='';
        $data='';
        if(WebUtils::IsRequestParam('id')) {
            $id = $_REQUEST['id'];
        }
        if(!empty($id)){
            $data= $this->_memberApiService->getLobby($id);
        }
        return $this->json($data);
    }

    //删除游说
    public function actionDeleteLobby()
    {
        if (!WebUtils::IsRequestParam('id')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提id[id]']);
        }
        $id = $_REQUEST['id'];
        $rst=$this->_memberApiService->deleteLobby($id);
        if ($rst) {
            return $this->json(['result' => true, 'code' => 200, 'msg' => '删除成功']);
        } else {
            return $this->json(['result' => false, 'code' => 500, 'msg' => '删除失败']);
        }
    }

}

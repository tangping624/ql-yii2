<?php
namespace app\modules\me\controllers;
use app\controllers\ControllerBase;
use app\modules\me\services\MemberService;
use yii\web\Cookie;
use app\framework\sms\SmsService;
use app\modules\pub\models\ListForm;
use app\entities\lobby\MBlog;
use app\framework\utils\StringHelper;
class MemberController extends ControllerBase {
    private $_memberService;

    public function __construct($id, $module, MemberService $memberService, $config = [])
    {
        $this->_memberService = $memberService;
        parent::__construct($id, $module, $config);
    }

    //我的足迹
    public function actionMyTrack(){
        return  $this->render('track');
    }

    public function actionAjaxMyTrack($pagesize=10 , $page =1,$type=''){
        $memberId=$this->context->memberId;
        $listResult=   $this->_memberService->getTrack((int)$pagesize,(int)$page, $memberId,$type);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pagesize;
        return $this->json($model);
    }


    //我的收藏
    public function actionMyCollection(){
        return  $this->render('collection');
    }

    public function actionAjaxMyCollection($pagesize=10 , $page =1,$type=''){
        $memberId=$this->context->memberId;
        $listResult=$this->_memberService->getCollection((int)$pagesize,(int)$page, $memberId,$type);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pagesize;
        return $this->json($model);
    }



    //删除
    public function actionDelete($id=''){
        $rst=$this->_memberService->deleteTrack($id);
        if ($rst) {
            return $this->json(['result' => true, 'code' => 200, 'msg' => '删除成功']);
        } else {
            return $this->json(['result' => false, 'code' => 500, 'msg' => '删除失败']);
        }

    }

    //我的点赞
    public function actionPraise(){
        return  $this->render('praise');
    }


    public function actionAjaxMyPraise($pagesize=10 , $page =1,$type=''){
        $memberId=$this->context->memberId;
        $listResult=$this->_memberService->getPraise((int)$pagesize,(int)$page, $memberId,$type);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pagesize;
        return $this->json($model);
    }

    //我的游说
    public function actionMyBlog(){
        return  $this->render('blog');
    }


    //游说列表
    public function actionAjaxLobbyIndex($pagesize=10 , $page =1) {
        $memberId=$this->context->memberId;
        $listResult = $this->_memberService->getLobbyList((int)$pagesize,(int)$page, $memberId);
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
                $blog= $this->_memberService->getLobby($id);
            }
            $blog->title = $title;
            $blog->photo = $photo;
            $blog->content = $content;
            $check= $this->_memberService->saveBlog($blog, $memberId);
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
    public function actionLobbyAdd($id='')
    {
        $data='';
        if(!empty($id)){
            $data= $this->_memberService->getLobby($id);
        }
        return $this->render('add',['data'=>$data]);
    }

    //删除游说
    public function actionDeleteLobby($id){
        $rst=$this->_memberService->deleteLobby($id);
        if ($rst) {
            return $this->json(['result' => true, 'code' => 200, 'msg' => '删除成功']);
        } else {
            return $this->json(['result' => false, 'code' => 500, 'msg' => '删除失败']);
        }
    }

}

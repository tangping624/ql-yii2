<?php
namespace app\modules\lobby\controllers;
use app\modules\ControllerBase;
use app\modules\lobby\services\LobbyService;
use app\modules\pub\models\ListForm;
use app\framework\utils\StringHelper;
use app\modules\lobby\models\MBlogForm;
class LobbyController extends ControllerBase{
    private $_lobbyService;
    public function __construct($id, $module,LobbyService $lobbyService, $config = [])
    {
        $this->_lobbyService = $lobbyService;
        parent::__construct($id, $module, $config);
    }


     public function actionIndex()
     {
         return $this->render('index');
    }

    //游说列表
    public function actionAjaxIndex($pagesize=10 , $page =1,$keywords='') {
        $listResult = $this->_lobbyService->getBlogList($pagesize,$page,$keywords);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pagesize;
        return $this->json($model);
    }

    public function actionSave($id=''){
        try {
            $userId = $this->getUser()->user_id;
            $isNew = empty($id);
            $blogForm = $this->getAdvertFormByPost();
            if (!$isNew) {
                $blog = $this->_lobbyService->getWiki($id);
                $blogEntity =   $blogForm->convertToEntity( $blog);
            } else {
                $blogEntity =   $blogForm->convertToEntity();
                $blogEntity->id = StringHelper::uuid();
            }

            $check= $this->_lobbyService->saveBlog($blogEntity, $userId, $isNew);
            if($check){
                return $this->json(['result' => true, 'id' => $blogEntity->id]);
            }
            else{
                return $this->json(['result' => false, 'code' => 200, 'msg' =>'保存失败']);
            }
        } catch (\Exception $ex) {
            return $this->json(['result' => false, 'code' => 500, 'msg' => $ex->getMessage()]);
        }
    }

    private function getAdvertFormByPost($isValid = true) {
        $blogForm = new MBlogForm();
        $blogForm->setAttributes($this->request->post(), false);

        if ($isValid && !$blogForm->validate()) {
            throw new \yii\base\InvalidValueException('MemberForm必填项校验未通过');
        }
        return   $blogForm;
    }


    //编辑
    public function actionAdd($id=''){
        $model=NULL;
        if (!empty($id)) {
            $model = $this->_lobbyService->getWiki($id);

        }

        return $this->render('add', ['model' => $model]);
    }


    //删除
    public function actionDeleted($id) {
        try{
            $rst = $this->_lobbyService->deleteWikiInfo($id);
            if ($rst) {
                return $this->json(['result' => true, 'code' => 200, 'msg' => '删除成功']);
            } else {
                return $this->json(['result' => false, 'code' => 500, 'msg' => '删除失败']);
            }
        } catch (\Exception $ex) {
            return $this->json(['result' => false, 'code' => 500, 'msg' => $ex->getMessage()]);
        }
    }


}

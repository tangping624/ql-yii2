<?php
namespace app\modules\news\controllers;
use app\modules\ControllerBase;
use app\modules\pub\models\ListForm;
use app\modules\news\services\NewsService;
use app\framework\utils\StringHelper;
use app\modules\news\models\MNewsForm;
class NewsController  extends ControllerBase{
    private $_newsService; 
    public function __construct($id, $module,NewsService $newsService, $config = [])
    {
        $this->_newsService = $newsService; 
        parent::__construct($id, $module, $config);
    }
   
    //新鲜事
     public function actionIndex(){
        return $this->render('index');
    }

    //新鲜事列表
    public function actionAjaxNewsList($pageSize=10 , $page =1,$keywords='') {
        $listResult = $this->_newsService->getNewsList((int)$pageSize,(int)$page,$keywords);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pageSize;
        return $this->json($model);
    }

    //新鲜事保存
    public function actionSave($id=''){
        try {
            $userId = $this->getUser()->user_id;
            $isNew = empty($id);
            $newsForm = $this->getAdvertFormByPost();
            if (!$isNew) {
                $blog = $this->_newsService->getNew($id);
                $newsEntity =   $newsForm->convertToEntity( $blog);
            } else {
                $newsEntity =   $newsForm->convertToEntity();
                $newsEntity->id = StringHelper::uuid();
            }

            $check= $this->_newsService->saveNews($newsEntity, $userId, $isNew);
            if($check){
                return $this->json(['result' => true, 'msg' =>'保存成功','id' => $newsEntity->id]);
            }
            else{
                return $this->json(['result' => false, 'code' => 200, 'msg' =>'保存失败']);
            }
        } catch (\Exception $ex) {
            return $this->json(['result' => false, 'code' => 500, 'msg' => $ex->getMessage()]);
        }
    }

    private function getAdvertFormByPost($isValid = true) {
        $blogForm = new MNewsForm();
        $blogForm->setAttributes($this->request->post(), false);

        if ($isValid && !$blogForm->validate()) {
            throw new \yii\base\InvalidValueException('MemberForm必填项校验未通过');
        }
        return   $blogForm;
    }


    //新鲜事新增+编辑
    public function actionAdd($id=''){
        $model=NULL;
        if (!empty($id)) {
            $model = $this->_newsService->getNew($id);
        }
        $data=$this->_newsService->getType();//获取分类
        return $this->render('add', ['model' => $model,'data'=>$data]);
    }


    //新鲜事删除
    public function actionDeleted($id) {
        try{
            $rst = $this->_newsService->deleteNews($id);
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

<?php
namespace app\modules\baike\controllers;
use app\modules\ControllerBase;
use app\modules\baike\services\ManageService;
use app\modules\baike\models\WikiInfoForm;
use app\modules\pub\models\ListForm;
use app\entities\baike\MWikiCategory;
use app\framework\utils\StringHelper;
use app\framework\utils\DateTimeHelper;
class ManageController extends ControllerBase{
    private $_manageService;
    public function __construct($id, $module,ManageService $manageService, $config = [])
    {
        $this->_manageService = $manageService;
        parent::__construct($id, $module, $config);
    }


    public function actionIndex()
    {
        return $this->render('index');
    }

    //百科列表
    public function actionAjaxIndex($pagesize=10 , $page =1,$keywords='') {
        $listResult = $this->_manageService->getBaikeList($pagesize,$page,$keywords);
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
            $wikiForm = $this->getAdvertFormByPost();
            if (!$isNew) {
                $wiki = $this->_manageService->getWiki($id);
                $wikiEntity = $wikiForm->convertToEntity( $wiki);
            } else {
                $wikiEntity = $wikiForm->convertToEntity();
                $wikiEntity->id = StringHelper::uuid();
            }

           // $images = $advertForm->getImageEntities($advertEntity->id, $userId);
            $check= $this->_manageService->saveAdvert($wikiEntity, $userId, $isNew);
            if($check){
                return $this->json(['result' => true, 'id' => $wikiEntity->id]);
            }
            else{
                return $this->json(['result' => false, 'code' => 200, 'msg' =>'保存失败']);
            }
        } catch (\Exception $ex) {
            return $this->json(['result' => false, 'code' => 500, 'msg' => $ex->getMessage()]);
        }
    }

    private function getAdvertFormByPost($isValid = true) {
        $wikiForm = new WikiInfoForm();
        $wikiForm->setAttributes($this->request->post(), false);

        if ($isValid && !$wikiForm->validate()) {
            throw new \yii\base\InvalidValueException('MemberForm必填项校验未通过');
        }
        return $wikiForm;
    }


    //编辑
    public function actionAdd($id=''){
        $model=NULL;
        if (!empty($id)) {
            $model = $this->_manageService->getWiki($id);

        }
        //百科分类
        $category = $this->_manageService->getCategory();
        return $this->render('add', ['model' => $model,'category'=>$category]);
    }


    //删除
    public function actionDelete($id) {
        try{
            $rst = $this->_manageService->deleteWikiInfo($id);
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

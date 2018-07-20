<?php
namespace app\modules\baike\controllers;
use app\modules\ControllerBase;
use app\modules\baike\services\EmergencyService;
use app\modules\baike\models\MEmergencyForm;
use app\modules\pub\models\ListForm;
use app\entities\baike\MEmergency;
use app\framework\utils\StringHelper;
use app\framework\utils\DateTimeHelper;
class EmergencyController extends ControllerBase{
    private $_emergencyService;
    public function __construct($id, $module,EmergencyService $emergencyService, $config = [])
    {
        $this->_emergencyService = $emergencyService;
        parent::__construct($id, $module, $config);
    }


    public function actionIndex()
    {
        return $this->render('@app/modules/shop/views/default/shop/index');
    }

    public function actionShopType(){
        return $this->render('@app/modules/shop/views/default/type/index');
    }

    //紧急列表
    public function actionAjaxIndex($keywords='',$pagesize=10 , $page =1) {
        $listResult = $this->_emergencyService->getBaikeList($pagesize,$page,$keywords);
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
            $emergencyForm = $this->getAdvertFormByPost();
            if (!$isNew) {
                $emergency = $this->_emergencyService->getWiki($id);
                $emergencyEntity = $emergencyForm->convertToEntity(  $emergency);
            } else {
                $emergencyEntity = $emergencyForm->convertToEntity();
                $emergencyEntity->id = StringHelper::uuid();
            }

           // $images = $advertForm->getImageEntities($advertEntity->id, $userId);
            $check= $this->_emergencyService->saveEmergency($emergencyEntity, $userId,$isNew);
            if($check){
                return $this->json(['result' => true, 'id' =>$emergencyEntity->id]);
            }
            else{
                return $this->json(['result' => false, 'code' => 200, 'msg' =>'保存失败']);
            }
        } catch (\Exception $ex) {
            return $this->json(['result' => false, 'code' => 500, 'msg' => $ex->getMessage()]);
        }
    }

    private function getAdvertFormByPost($isValid = true) {
        $emergencyForm = new MEmergencyForm();
        $emergencyForm->setAttributes($this->request->post(), false);

        if ($isValid && ! $emergencyForm->validate()) {
            throw new \yii\base\InvalidValueException('MemberForm必填项校验未通过');
        }
        return  $emergencyForm;
    }


    //编辑
    public function actionAdd($id=''){
        $model=NULL;
        if (!empty($id)) {
            $model = $this->_emergencyService->getWiki($id);

        }

        return $this->render('add', ['model' => $model]);
    }


    //删除
    public function actionDelete($id) {
        try{
            $rst = $this->_emergencyService->deleteWikiInfo($id);
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

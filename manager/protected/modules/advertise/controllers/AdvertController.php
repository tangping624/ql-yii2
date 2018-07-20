<?php
namespace app\modules\advertise\controllers;
use app\modules\ControllerBase;
use app\modules\pub\models\ListForm;
use app\modules\advertise\services\AdvertService;
use app\modules\advertise\models\AdvertForm;
use app\framework\utils\StringHelper;
class AdvertController  extends ControllerBase{
    private $_advertService; 
    public function __construct($id, $module,AdvertService $advertService, $config = [])
    {
        $this->_advertService = $advertService; 
        parent::__construct($id, $module, $config);
    }
   
    //广告管理列表
     public function actionIndex(){
        return $this->render('index');
    }

    //广告管理列表
    public function actionAjaxAdverts($pagesize=10 , $page =1) {
        $listResult = $this->_advertService->getAdvertList($pagesize,$page);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pagesize;
        return $this->json($model);
    }

    //编辑
    public function actionAdd($id=''){
        $model=NULL;
        if (!empty($id)) {
            $model = $this->_advertService->getAdvertDetails($id);
            if (!isset($model['advert'])) {
                $this->notFound();
            }
        }
        $adsenses = $this->_advertService->getAdsense();
        return $this->render('add', ['model' => $model['advert'],'images'=>$model['images'],'adsenses'=>$adsenses]);
    }
    

    //删除
    public function actionDeleted($id) {
        try{
            $return = $this->_advertService->setDeleted($id);
            return $this->json(['result' => true, 'id' => $id]);
        } catch (\Exception $ex) {
            return $this->json(['result' => false, 'code' => 500, 'msg' => $ex->getMessage()]);
        }
    }

    //保存
    public function actionSave($id=''){
        try {
            $user_id = $this->getUserId();
            $isNew = empty($id);
            $advertForm = $this->getAdvertFormByPost();
            if (!$isNew) {
                $advert = $this->_advertService->getAdvert($id);
                $advertEntity = $advertForm->convertToEntity($advert);
            } else {
                $advertEntity = $advertForm->convertToEntity();
                $advertEntity->id = StringHelper::uuid();
            }

            $images = $advertForm->getImageEntities($advertEntity->id,$user_id);
            $check= $this->_advertService->saveAdvert($advertEntity, $isNew,$images,$user_id);
            if($check->getIsSuccess()){
                return $this->json(['result' => true, 'id' => $advertEntity->id]);
            }
            else{
                return $this->json(['result' => false, 'code' => 200, 'msg' =>$check->getMsg()]);
            }
        } catch (\Exception $ex) {
            return $this->json(['result' => false, 'code' => 500, 'msg' => $ex->getMessage()]);
        }
    }

    private function getAdvertFormByPost($isValid = true) {
        $advertForm = new AdvertForm();
        $advertForm->setAttributes($this->request->post(), false);

        if ($isValid && !$advertForm->validate()) {
            throw new \yii\base\InvalidValueException('MemberForm必填项校验未通过');
        }
        return $advertForm;
    }

    private function getUserId() {
        $userSessionAccessor = \Yii::$container->get('app\framework\auth\UserSessionAccessor');
        $userId = '';
        if (isset($userSessionAccessor)) {
            $userSession = $userSessionAccessor->getUserSession();
            if (isset($userSession)) {
                $userId = $userSession->user_id;
            }
        }
        return $userId;
    }


}

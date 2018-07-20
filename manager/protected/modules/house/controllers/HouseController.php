<?php
namespace app\modules\house\controllers;
use app\modules\ControllerBase;
use app\modules\pub\models\ListForm;
use app\modules\house\services\HouseService;
use app\framework\utils\StringHelper;
use app\modules\house\models\HouseForm;
class HouseController  extends ControllerBase{
    private $_houseService; 
    public function __construct($id, $module,HouseService $houseService, $config = [])
    {
        $this->_houseService = $houseService; 
        parent::__construct($id, $module, $config);
    }
   
    //房产
     public function actionIndex(){
        //return $this->render('index');
         return $this->render('@app/modules/shop/views/default/shop/index');
    }

    public function actionShopType(){
        return $this->render('@app/modules/shop/views/default/type/index');
    }

    //房产列表
    public function actionAjaxHouseList($pageSize=10 , $page =1,$keywords='') {
        $listResult = $this->_houseService->getHouseList((int)$pageSize,(int)$page,$keywords);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pageSize;
        return $this->json($model);
    }

    //房产保存
    public function actionSave($id=''){
        try {
            $isNew = empty($id);
            $houseForm = $this->getAdvertFormByPost();
            if (!$isNew) {
                $blog = $this->_houseService->getHouse($id);
                $houseEntity =   $houseForm->convertToEntity( $blog);
            } else {
                $houseEntity =   $houseForm->convertToEntity();
                $houseEntity->id = StringHelper::uuid();
                $houseEntity->created_on = date('Y-m-d H:i:s', time());
                $houseEntity->app_code='house';
            }
            $check= $this->_houseService->saveHouse($houseEntity);
            if($check){
                return $this->json(['result' => true, 'msg' =>'保存成功','id' => $houseEntity->id]);
            }
            else{
                return $this->json(['result' => false, 'code' => 200, 'msg' =>'保存失败']);
            }
        } catch (\Exception $ex) {
            return $this->json(['result' => false, 'code' => 500, 'msg' => $ex->getMessage()]);
        }
    }

    private function getAdvertFormByPost($isValid = true) {
        $blogForm = new HouseForm();
        $blogForm->setAttributes($this->request->post(), false);

        if ($isValid && !$blogForm->validate()) {
            throw new \yii\base\InvalidValueException('MemberForm必填项校验未通过');
        }
        return   $blogForm;
    }


    //房产新增+编辑
    public function actionAdd($id=''){
        $model=NULL;
        if (!empty($id)) {
            $model = $this->_houseService->getHouseDetails($id);
        }
        return $this->render('add', ['model' => $model]);
    }

    //编辑获取房产的商家
    public function actionAjaxSellerList($pageSize=10 , $page =1,$keywords=''){
        $listResult = $this->_houseService->getSellerList((int)$pageSize,(int)$page,$keywords);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pageSize;
        return $this->json($model);
    }

    //编辑获取房产类别
    public function actionAjaxHouseType($id=''){
        $model = $this->_houseService->getHouseType($id);
        return $this->json($model);
    }


    //房产删除
    public function actionDeleted($id) {
        try{
            $rst = $this->_houseService->deleteHouse($id);
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

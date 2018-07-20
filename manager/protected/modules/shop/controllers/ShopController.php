<?php
namespace app\modules\shop\controllers;
use app\modules\ControllerBase;
use app\modules\pub\models\ListForm;
use app\modules\shop\services\ShopService;
use app\framework\utils\StringHelper;
use app\modules\shop\models\ShopForm;
class ShopController  extends ControllerBase{
    private $_shopService; 
    public function __construct($id, $module,ShopService $shopService, $config = [])
    {
        $this->_shopService = $shopService; 
        parent::__construct($id, $module, $config);
    }

    //购物惠
    public function actionIndex(){
        //return $this->render('index');
        return $this->render('@app/modules/shop/views/default/shop/index');
    }

    //购物惠列表
    public function actionAjaxShopList($pageSize=10 , $page =1,$keywords='',$app_code='shop') {
        $listResult = $this->_shopService->getShopList((int)$pageSize,(int)$page,$keywords,$app_code);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pageSize;
        return $this->json($model);
    }

    //购物惠保存
    public function actionSave($id=''){
        try {
            $isNew = empty($id);
            $app_code=$_POST['app_code'];
            $houseForm = $this->getAdvertFormByPost();
            if (!$isNew) {
                $blog = $this->_shopService->getShop($id);
                $houseEntity =   $houseForm->convertToEntity( $blog);
            } else {
                $houseEntity =   $houseForm->convertToEntity();
                $houseEntity->id = StringHelper::uuid();
                $houseEntity->created_on = date('Y-m-d H:i:s', time());
                $houseEntity->app_code=$app_code;
            }
            $check= $this->_shopService->saveShop($houseEntity);
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
        $blogForm = new ShopForm();
        $blogForm->setAttributes($this->request->post(), false);
        if ($isValid && !$blogForm->validate()) {
            throw new \yii\base\InvalidValueException('MemberForm必填项校验未通过');
        }
        return $blogForm;
    }


    //购物惠新增+编辑
    public function actionAdd($id=''){
        $model=NULL;
        if (!empty($id)) {
            $model = $this->_shopService->getShopDetails($id);
        }
        return $this->render('add', ['model' => $model]);
    }

    //编辑获取购物惠的商家
    public function actionAjaxSellerList($pageSize=10 , $page =1,$keywords='',$app_code='shop'){
        $listResult = $this->_shopService->getSellerList((int)$pageSize,(int)$page,$keywords,$app_code);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pageSize;
        return $this->json($model);
    }


    //购物惠删除
    public function actionDeleted($id) {
        try{
            $rst = $this->_shopService->deleteShop($id);
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

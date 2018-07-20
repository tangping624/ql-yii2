<?php
namespace app\modules\common\controllers;
use app\modules\ControllerBase;
use app\modules\pub\models\ListForm;
use app\modules\common\services\CommonService;
use app\framework\utils\StringHelper;
use app\modules\common\models\CommonForm;
class CommonController  extends ControllerBase{
    private $_commonService; 
    public function __construct($id, $module,CommonService $commonService, $config = [])
    {
        $this->_commonService = $commonService; 
        parent::__construct($id, $module, $config);
    }

    //旅游&投资&合作交流
     /*public function actionIndex(){
        return $this->render('index');
    }*/

    /*public function actionCooperationShop(){
        return $this->render('@app/modules/common/views/default/common/index');
    }

    public function actionInvestShop(){
        return $this->render('@app/modules/common/views/default/common/index');
    }*/

    //旅游&投资&合作交流列表
    public function actionAjaxShopList($pageSize=10 , $page =1,$keywords='',$app_code='shop') {
        $listResult = $this->_commonService->getShopList((int)$pageSize,(int)$page,$keywords,$app_code);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pageSize;
        return $this->json($model);
    }

    //旅游&投资&合作交流保存
    public function actionSave($id=''){
        try {
            $isNew = empty($id);
            $houseForm = $this->getAdvertFormByPost();
            if (!$isNew) {
                $blog = $this->_commonService->getShop($id);
                $houseEntity =   $houseForm->convertToEntity($blog);
            } else {
                $houseEntity =   $houseForm->convertToEntity();
                $houseEntity->id = StringHelper::uuid();
            }
            $houseEntity->created_on = date('Y-m-d H:i:s', time());
            $check= $this->_commonService->saveShop($houseEntity);
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
        $blogForm = new CommonForm();
        $blogForm->setAttributes($this->request->post(), false);
        if ($isValid && !$blogForm->validate()) {
            throw new \yii\base\InvalidValueException('MemberForm必填项校验未通过');
        }
        return $blogForm;
    }


    //旅游&投资&合作交流新增+编辑
    public function actionAdd($id=''){
        $model=NULL;
        if (!empty($id)) {
            $model = $this->_commonService->getShop($id);
        }
        return $this->render('add', ['model' => $model]);
    }

    //编辑获取旅游&投资&合作交流新增的商家（新增vip服务、服务、移民、教育培训。休闲娱乐、特产商家）
    public function actionAjaxSellerList($pageSize=10 , $page =1,$keywords='',$app_code='shop'){
        $listResult = $this->_commonService->getSellerList((int)$pageSize,(int)$page,$keywords,$app_code);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pageSize;
        return $this->json($model);
    }


    //旅游&投资&合作交流删除
    public function actionDeleted($id) {
        try{
            $rst = $this->_commonService->deleteShop($id);
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

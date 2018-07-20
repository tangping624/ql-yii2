<?php
namespace app\modules\shop\controllers;
use app\entities\goods\GwhType;
use app\modules\ControllerBase;
use app\modules\pub\models\ListForm;
use app\modules\shop\services\TypeService;
use app\framework\utils\StringHelper;
class TypeController  extends ControllerBase{
    private $_typeService; 
    public function __construct($id, $module,TypeService $typeService, $config = [])
    {
        $this->_typeService = $typeService; 
        parent::__construct($id, $module, $config);
    }
   
    //购物惠分类
    public function actionIndex(){
        //return $this->render('index');
        return $this->render('@app/modules/shop/views/default/type/index');
    }

    //购物惠分类列表
    public function actionAjaxTypeList($pageSize=10 , $page =1,$keywords='',$app_code='shop') {
        $listResult = $this->_typeService->getTypeList((int)$pageSize,(int)$page,$keywords,$app_code);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pageSize;
        return $this->json($model);
    }

    //购物惠分类保存
    public function actionSave(){
        try {
            $id=empty($_POST['id'])?"":$_POST['id'];
            $name=$_POST['name'];
            $app_code=$_POST['app_code'];
            if (!empty($id)) {
                $type = $this->_typeService->getType($id);
                $type->name=$name;
            } else {
                $type = new GwhType();
                $type->id = StringHelper::uuid();
                $type->name=$name;
                $type->app_code=$app_code;
                $type->created_on = date('Y-m-d H:i:s', time());
            }
            $check= $this->_typeService->saveType($type);
            if($check){
                return $this->json(['result' => true, 'msg' =>'保存成功','id' => $type->id]);
            }
            else{
                return $this->json(['result' => false, 'code' => 200, 'msg' =>'保存失败']);
            }
        } catch (\Exception $ex) {
            return $this->json(['result' => false, 'code' => 500, 'msg' => $ex->getMessage()]);
        }
    }


    //购物惠分类新增+编辑
    public function actionAdd($id=''){
        $model=NULL;
        if (!empty($id)) {
            $model = $this->_typeService->getType($id);
        }
        return $this->render('add', ['model' => $model]);
    }

    //购物惠分类删除
    public function actionDeleted($id) {
        try{
            $model=$this->_typeService->getTypeGoods($id);//判断分类下有没有商品
            if(!empty($model))
            {
                return $this->json(['result' => false, 'code' => 500, 'msg' => '该分类下有商品,无法删除']);
            }
            $rst = $this->_typeService->deleteType($id);
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

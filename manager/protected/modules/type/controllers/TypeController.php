<?php
namespace app\modules\type\controllers;
use app\modules\ControllerBase;
use app\modules\type\services\TypeService;
use app\entities\merchant\SellerType;
use app\framework\utils\Security;
use app\framework\utils\StringHelper;

class TypeController extends ControllerBase
{
    private $_typeService;
    private $_popupLayout = '../../../../views/layouts/popup.php';

    public function __construct($id, $module, TypeService $typeService, $config = [])
    {
        $this->_typeService = $typeService;
        parent::__construct($id, $module, $config);
    }


    //人员管理列表
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionAjaxList(){

        $data=$this->_typeService->getTypeList();
       return $this->json($data);
    }

    //添加子级

    public function actionSave($parent_id='',$name='',$icon='',$id='',$orderby=''){
        $code=$this->_typeService->findMaxCode();
        if(empty($id)) {
            $stype=new SellerType();
            $stype->id = StringHelper::uuid();
            $stype->parent_id = $parent_id;
            $stype->name = $name;
            $stype->type = 1;
            $stype->code = '0'.($code+1);
            $stype->icon = $icon;
            $stype->is_display = 1;
        }else{

            $stype=$this->_typeService->findSellerType($id);
            $stype->name = $name;
            $stype->icon = $icon;
            $stype->orderby = $orderby;

        }
        $rst=$this->_typeService->saveType($stype);
        $id= $stype->attributes['id'];
        if ($rst) {
            return $this->json(['result' => true, 'code' => 200, 'msg' => '保存成功','id'=>$id]);
        } else {
            return $this->json(['result' => false, 'code' => 500, 'msg' => '保存失败']);
        }
    }

    //删除

    public function actionDelete($id){
        //判断该分类下是否有商家

        $re=$this->_typeService->getTypeSeller($id);
        if($re) {
            $rst = $this->_typeService->deleteType($id);
            if ($rst) {
                return $this->json(['result' => true, 'code' => 200, 'msg' => '删除成功']);
            } else {
                return $this->json(['result' => false, 'code' => 500, 'msg' => '删除失败']);
            }
        }else{
            return $this->json(['result' => false, 'code' => 500, 'msg' => '该分类下存在商家，无法删除']);
        }
    }

    //编辑
    public function actionAdd($id='',$status='')
    {
        $this->layout = $this->_popupLayout;
        $data=[];
        if(!empty($id)){
            $data = $this->_typeService->findSellerType($id);
        }
        return $this->render('add',['data'=>$data,'status'=>$status]);
    }

    public function actionShow($id){
        $data = $this->_typeService->findSellerType($id);
        return $this->json($data);
    }

    //设置是否显示
    public function actionSetDisplay($id,$is_display=''){
       $rst = $this->_typeService->setDisplay($id,$is_display);
       if ($rst) {
           return $this->json(['result' => true, 'code' => 200, 'msg' => '设置成功']);
        } else {
           return $this->json(['result' => false, 'code' => 500, 'msg' => '设置失败']);
         }

    }


}

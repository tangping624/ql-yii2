<?php
namespace app\modules\baike\controllers;
use app\modules\ControllerBase;
use app\modules\baike\services\BaiKeService;
use app\modules\pub\models\ListForm;
use app\entities\baike\MWikiCategory;
use app\framework\utils\StringHelper;
use app\framework\utils\DateTimeHelper;
class BaiKeController extends ControllerBase{
    private $_baiKeService;
    public function __construct($id, $module,BaiKeService $baiKeService, $config = [])
    {
        $this->_baiKeService = $baiKeService; 
        parent::__construct($id, $module, $config);
    }


     public function actionIndex()
     {
         return $this->render('index');
    }

    public function actionAjaxIndex(){
     $data= $this->_baiKeService->getBaikeTYpe();
     return $this->json($data);

    }

    public function actionSaveType($id='',$name=''){
        if(empty($id)) {
            $userId = $this->getUser()->user_id;
            $category = new MWikiCategory();
            $category->id = StringHelper::uuid();
            $category->name = $name;
            $category->created_on = DateTimeHelper::now();
            $category->created_by = $userId;
            $category->modified_on = DateTimeHelper::now();
            $category->modified_by = $userId;
        }else{
            $category=$this->_baiKeService->getEnitity($id);
            $category->name = $name;
            $category->modified_on = DateTimeHelper::now();
        }
        $rst=$this->_baiKeService->saveType( $category);
        if ( $rst) {
            return $this->json(['result' => true, 'code' => 200,'msg'=>'保存成功']);

        } else {
            return $this->json(['result' => false, 'code' => 500,'msg'=>'保存不通过']);
        }

    }

    //编辑
    public function actionAdd($id='')
    {
        $data=[];
        if(!empty($id)){
            $data = $this->_baiKeService->getEnitity($id);
        }
        return $this->render('add',['data'=>$data,]);
    }

    //删除

    public function actionDelete($id){
        // //查找该分类下是否存在信息
        $re= $this->_baiKeService->getTypeWiki($id);
        if($re) {
            $rst = $this->_baiKeService->deleteType($id);
            if ($rst) {
                return $this->json(['result' => true, 'code' => 200, 'msg' => '删除成功']);
            } else {
                return $this->json(['result' => false, 'code' => 500, 'msg' => '删除失败']);
            }
        }else{
            return $this->json(['result' => false, 'code' => 500, 'msg' => '该分类下有百科信息，无法删除']);
        }
    }
    
}

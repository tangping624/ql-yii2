<?php
 namespace app\modules\city\controllers;
use app\modules\ControllerBase;
use app\modules\city\services\OfficersService;
use app\entities\city\City;
use  app\modules\city\models\CityForm;
use app\framework\utils\Security;

class CityController extends ControllerBase
{
    private $_officersService;
    private $_popupLayout = '../../../../views/layouts/popup.php';

    public function __construct($id, $module, OfficersService $officersService, $config = [])
    {
        $this->_officersService = $officersService;
        parent::__construct($id, $module, $config);
    }


    //人员管理列表
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionAdd($id='')
    {
        $this->layout = $this->_popupLayout;
        $data=[];
        if(!empty($id)){
            $data = $this->_officersService->getMOfficersInfo($id);
        }
        return $this->render('add',['data'=>$data]);
    }


    public function actionSave($id='')
    {
        try {
            $cityForm = $this->getAdvertFormByPost();
            $parent_id =empty($_POST['parent_id'])?'':$_POST['parent_id'];
            $code=$this->_officersService->findMaxCode();
            if(empty($code)){
                $code=1;
            }
            if (!empty($id)) {
                $MOfficers = $this->_officersService->getEntity($id);
                $cityEntity = $cityForm->convertToEntity($MOfficers);
            }
            if(empty($id)) {
                $cityEntity = $cityForm->convertToEntity( );
                if(!empty($parent_id)) {
                    $cityEntity->parent_id = $parent_id;
                }
                $cityEntity->is_deleted ="0";
                $cityEntity->is_default =0;
                $cityEntity->code =$code+1;
                $cityEntity->created_on=date("Y-m-d H:i:s");
            }
            $data = $this->_officersService->saveInfo( $cityEntity);
            $id= $cityEntity->attributes['id'];
            if ($data) {
                return $this->json(['result' => true, 'code' => 200, 'msg' => '保存成功','id'=>$id]);
            } else {
                return $this->json(['result' => false, 'code' => 500, 'msg' => '保存失败']);
            }
        } catch (\Exception $ex) {
            return $this->json(['result' => false, 'code' => 500, 'msg' => $ex->getMessage()]);
        }
    }


    //获取详情
    public function actionShow($id)
    {
        try {
            $data = $this->_officersService->getMOfficersInfo($id);
            return $this->json(['data' => $data]);
        } catch (\Exception $ex) {
            return $this->json(['result' => false, 'code' => 500, 'msg' => $ex->getMessage()]);
        }
    }

    //删除
    public function actionDelete($id)
    {
        try {
            $data = $this->_officersService->deleteMOfficersInfo($id);
            if ($data) {
                return $this->json(['result' => true, 'code' => 200, 'msg' => '删除成功', 'id' => $id]);
            } else {
                return $this->json(['result' => false, 'code' => 500, 'msg' => '删除失败']);
            }
        } catch (\Exception $ex) {
            return $this->json(['result' => false, 'code' => 500, 'msg' => $ex->getMessage()]);
        }

    }

    //列表
    public function actionShowList()
    {
        try {
            $data = $this->_officersService->getshowList();
            return $this->json(['data' => $data]);

        } catch (\Exception $ex) {
            return $this->json(['result' => false, 'code' => 500, 'msg' => $ex->getMessage()]);
        }
    }

    //地图
    public function actionMap()
    {
        return $this->renderPartial('map');
    }

    private function getAdvertFormByPost($isValid = true) {
        $cityForm= new CityForm();
        $cityForm->setAttributes($this->request->post(), false);

        if ($isValid && !$cityForm->validate()) {
            throw new \yii\base\InvalidValueException('MemberForm必填项校验未通过');
        }
        return   $cityForm;
    }

    //设置为默认城市

    public function actionSetDefaultCity($id=''){
        try {
            $data = $this->_officersService->setDefaultCity($id);
            if ($data) {
                return $this->json(['result' => true, 'code' => 200, 'msg' => '设置成功', 'id' => $id]);
            } else {
                return $this->json(['result' => false, 'code' => 500, 'msg' => '设置失败']);
            }
        } catch (\Exception $ex) {
            return $this->json(['result' => false, 'code' => 500, 'msg' => $ex->getMessage()]);
        }

    }

}

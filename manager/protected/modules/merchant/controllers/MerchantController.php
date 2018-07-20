<?php
namespace app\modules\merchant\controllers;
use app\modules\ControllerBase;
use app\modules\merchant\services\MerchantService;
use app\modules\type\services\TypeService;
use app\entities\merchant\SMerchant;
use app\entities\merchant\SImages;
use app\modules\merchant\models\SMerchantForm;
use app\framework\utils\DateTimeHelper;
use app\framework\utils\StringHelper;
use yii\web\Response;
use yii;
use app\modules\pub\models\ListForm;
class MerchantController extends ControllerBase
{
    private $_merchantService;
    private $_typeService;
    public function __construct($id, $module, MerchantService $merchantService,TypeService $typeService, $config = [])
    {
        $this->_merchantService = $merchantService;
        $this->_typeService = $typeService;
        parent::__construct($id, $module, $config);
    }

    //商家
    public function actionIndex()
    {
        return $this->render('index');
    }

    //商家列表
    public function actionAjaxSeller($pagesize=10 , $page =1,$name='')
    {
        $listResult = $this->_merchantService->getSellerList((int)$pagesize,(int)$page,$name);
        $model = new ListForm();
        $model->items = $listResult->items;
        $model->total = $listResult->total;
        $model->page = $page;
        $model->pageSize = $pagesize;
        return $this->json($model);
    }

    //删除
    public function actionDeleted($id) {
        $userId =$this->getUser()->user_id;
        try{
            $return = $this->_merchantService->setDeleted($id,$userId);
            return $this->json(['result' => true, 'id' => $id]);
        } catch (\Exception $ex) {
            return $this->json(['result' => false, 'code' => 500, 'msg' => $ex->getMessage()]);
        }
    }

    //编辑
    public function actionAdd($id='')
    {
        //商家分类
        $stype=$this->_typeService->getParentType();
        //城市选择
        $city=$this->_merchantService->findCity();
        //房产标签
        $tag=$this->_merchantService->getTag();
        $data=[];
        $photo='';
        $tsg='';
        if(!empty($id)){
            $data = $this->_merchantService->getMerchantInfo($id);

            //获取图片信息
            $photo= $this->_merchantService->getImageInfo($id);
            //获取房产标签
            $tsg= $this->_merchantService->findTagInfo($id);
        }
        return $this->render('add',['data'=>$data,'type'=>$stype,'city'=>$city,'tag'=>$tag,'photo'=>$photo,'tsg'=>$tsg]);
    }

    //商家保存
    public function actionSave(){
        try {

            $id=empty($_POST['id'])?'':$_POST['id'];
            $sort=empty($_POST['sort'])?'':$_POST['sort'];
            $isNew = empty($id);
            $typename=empty($_POST['typename'])?'':$_POST['typename'];
            $tag_id=empty($_POST['tag_id'])?'':$_POST['tag_id'];
            $userId = $this->getUser()->user_id;
            $goodsform = $this->getGoodsFormBypost();
            if (!$isNew) {
                $goods = $this->_merchantService->getMerchantInfo($id);
                $goodsEntity = $goodsform->convertToEntity($goods);
            } else {
                $goodsEntity = $goodsform->convertToEntity();
                $goodsEntity->id = StringHelper::uuid();

            }
            if(empty($sort)){
                $goodsEntity->sort = 1410065407;
            }
            $image = $goodsform->getImageEntities($goodsEntity->id, $userId, $userId);
            $res = $this->_merchantService->saveGoods($goodsEntity, $userId, $isNew, $image);
            /*if($typename=='房产') {
                if (!empty($tag_id)) {
                $arr = explode(',', $tag_id);
                    foreach ($arr as $row) {
                        $res = $this->_merchantService->saveSellerTag($goodsEntity->id, $row);
                    }
                }
            }*/
            if ($res) {
                return $this->json(['result' => true, 'code' => 200,'msg'=>$id]);

            } else {
                return $this->json(['result' => false, 'code' => 500,'msg'=>'保存不通过']);
            }
        } catch (\Exception $ex) {

            return $this->json(['result' => false, 'code' => 500, 'msg' => $ex->getMessage()]);
        }

    }

    //是否推荐商家
    public function actionChange($id,$is_recommend){
        $userId =$this->getUser()->user_id;
        try{
            $return = $this->_merchantService->changeGoods($id,$is_recommend,$userId);
            return $this->json(['result' => true, 'code' => 200, 'msg' =>"状态改变成功",'id' => $id]);
        } catch (\Exception $ex) {
            return $this->json(['result' => false, 'code' => 500, 'msg' => $ex->getMessage()]);
        }

    }

    private function getGoodsFormBypost($isValid=true){
        $goodsform=new SMerchantForm();
        $goodsform->setAttributes($this->request->post(),false);
        if($isValid && !$goodsform->validate()) {
            throw new \yii\base\InvalidValueException('必填项校验未通过');
        }
        return $goodsform;

    }

    //查找城市的下一级

    public function actionFindSon($id){
        $name=$this->_merchantService->findSon($id);
        return $this->json($name);
    }

    //查找商家子级

    public function actionFindSellerson($id){
    $seller=$this->_typeService->findSellerSon($id);
    return $this->json($seller);
}


}

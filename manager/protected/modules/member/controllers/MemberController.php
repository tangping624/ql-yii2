<?php
namespace app\modules\member\controllers;
use app\modules\ControllerBase;
use app\modules\member\services\MemberService;
use app\modules\pub\models\ListForm;
class MemberController extends ControllerBase{
    private $_memberService;
    public function __construct($id, $module,MemberService $memberService, $config = [])
    {
        $this->_memberService = $memberService; 
        parent::__construct($id, $module, $config);
    }


     public function actionIndex()
     {
         return $this->render('index');
    }

    //会员管理列表
    public function actionAjaxMemberList($page=1,$pageSize=10,$Keywords='')
    {
        try{
            $rst = $this->_memberService->getMemberList((int)$page,(int)$pageSize,$Keywords);
            $data = new ListForm();
            $data->items =$rst->items;
            $data->total = $rst->total;
            $data->page = (int)$page;
            $data->pageSize = (int)$pageSize;
            return $this->json(['result'=>true,'code' => 200, 'data' => $data]);
        } catch (\Exception $ex) {
            \Yii::error($ex->getMessage());
            return $this->json(['result'=>false,'code' => 500, 'data' =>$ex->getMessage()]);
        }
    }

}

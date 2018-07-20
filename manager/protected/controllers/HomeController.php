<?php 
namespace app\controllers; 
use app\framework\web\extension\ManagerController;

class HomeController extends ManagerController
{
    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    /**
     * 用于跳转有权限的页面
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionIndex()
    {  
        $this->redirect('/system/user/index');
    }
}

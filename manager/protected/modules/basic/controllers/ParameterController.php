<?php

namespace app\modules\basic\controllers;

use app\framework\biz\bizparam\BizParamAPI;
use app\framework\utils\WebUtility;
use app\modules\basic\services\ParameterService;
use app\modules\ControllerBase;
use yii\base\Exception; 
class ParameterController extends ControllerBase
{
    /**
     * @var ParameterService
     */
    private $_parameterService; 

    /**
     * 构造器
     * @param string $id actionID
     * @param \yii\base\Module $module 模块
     * @param array $config 配置信息
     * @throws \Exception 未知异常
     * @throws \yii\base\InvalidConfigException 抛出参数异常
     */
    public function __construct($id, $module, ParameterService $parameterService , $config = [])
    {
        $this->_parameterService = $parameterService; 
        parent::__construct($id, $module, $config);
    } 
   /**
     *  FunctionAction权限点校验对照关系配置
     * @return array
     */
    public function functionActionSetting()
    {
        return [
            'appcode'=>'account',
            'function' => 'parameter' 
        ];
    } 
    public function actionIndex()
    { 
        $appcode = \Yii::$app->request->get('_ac');  
        return $this->render('index', ['paramGroup' => $this->_parameterService->getAllParameterAndGroup()]);
    }

    public function actionEditAttentionUrl()
    {
        $id = $this->request->post("id");
        $url = $this->request->post("attention_url");

        try {
            $result = BizParamAPI::instance()->updateParameterTitle($id, $url);
            if ($result) {
                return $this->json(['result' => true, 'data' => $result]);
            } else {
                return $this->json(['result' => false, 'code' => 500, 'msg' => "修改失败"]);
            }
        } catch (\Exception $ex) {
            return $this->json(['result' => false, 'code' => 500, 'msg' => $ex->getMessage()]);
        }
    }
 
}




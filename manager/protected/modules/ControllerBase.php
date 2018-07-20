<?php 
namespace app\modules;

use app\framework\web\extension\ManagerController;
use app\framework\auth\interfaces\UserSessionAccessorInterface;

class ControllerBase extends ManagerController
{
    /**
     * @var 当前用户缓存信息
     */
    public $userSession;
    /**
     * @var UserSessionAccessorInterface
     */
    protected $userSessionAccessor; 
    /**
     * 构造器
     * @param string $id actionID
     * @param \yii\base\Module $module 模块
     * @param array $config 配置信息
     * @throws \Exception 未知异常
     * @throws \yii\base\InvalidConfigException 抛出参数异常
     */
    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config); 
    }
     
    
    
    /**
     * 返回JSON数据
     * @param $status bool 自定义状态
     * @param $message string 自定义消息
     * @param null $data object\array 自定义数据，不设置则不输出该属性
     * @return array JSON Data
     */
    public function jsonData($status, $message, $data = null)
    {
        $jsonObject = ['status' => $status, 'message' => $message];
        if (isset($data)) {
            $jsonObject['data'] = $data;
        }
        return $this->json($jsonObject);
    }
}

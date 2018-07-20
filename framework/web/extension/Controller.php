<?php
namespace app\framework\web\extension;

use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use app\framework\utils\WebUtility;

/**
 * @property \yii\web\Request|\yii\console\Request $request The request component. This property is read-only.
 * @property \yii\web\Response|\yii\console\Response $response The response component. This property is
 */
abstract class Controller extends \yii\web\Controller
{
    public $enableCsrfValidation = false;

    public $is_get;

    public function __construct($id,
                                $module,
                                $config = [])
    {
        $this->is_get = Yii::$app->request->isGet;
        $this->setClientUrlPrefix();
       if(  Yii::$app->params['IsTure']!='00000001'){
           $this->goError();
        } 
        parent::__construct($id, $module, $config);
    }
    protected function goError(){
        return $this->redirect('/site/notin'); 
    }
    protected function setClientUrlPrefix()
    {
        Yii::$app->response->getHeaders()->set('prefix', "");//WebUtility::getUrlPrefix()
    }

    public function getRequest()
    {
        return Yii::$app->request;
    }

    public function getResponse()
    {

        return Yii::$app->response;
    }

    public function goHome()
    {
        $defaultRoute = Yii::$app->defaultRoute;
        $url = $this->createUrl($defaultRoute);
        return $this->redirect($url);
    }

    protected function json($data)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $data;
    }

    protected function notFound()
    {
        throw new NotFoundHttpException('请求的资源' . $this->request->url . '不存在');
    }

    protected function forbid()
    {
        throw new ForbiddenHttpException('禁止访问');
    }

    public function redirect($url, $exit = true)
    {
        $result = Yii::$app->response->redirect($url);
        Yii::$app->end();
        return $result;
    }


    public function createUrl($route, $params = [], $appendUnit = true, $appendAppCode = true, $appendFuncCode = true)
    {
        return WebUtility::createUrl($route, $params, $appendUnit, $appendAppCode, $appendFuncCode);
    }

    public function getUser()
    {
        $user = \Yii::$app->user->getIdentity();
        return $user;
    }

    /**
     *
     * @return mixed
     */
    public function getGeneralUnit()
    {
        return WebUtility::getUrlPrefix();
    }


    /**
     * @param $name
     * @param bool $require 是否必须
     * @return string
     */
    public function block($name, $require = false)
    {
        if (empty($name))
            throw new \InvalidArgumentException('$name');

        $arr = $this->view->blocks;
        if (!$require) {
            if (isset($arr)) {
                return array_key_exists($name, $arr) ? $arr[$name] : '';
            }
            return '';
        }
        return $arr[$name];
    }

    public function createControllerUrl($actionName, $params = [])
    {
        $actionName = \Yii::$app->id . '/' . $this->id . '/' . $actionName;
        return static::createUrl($actionName, $params);
    }
/**
     * 获取当前FunctionCode
     * @return mixed|null
     */
    public function getCurAppCode()
    {
        $queryFuncCode = WebUtility::getQueryAppCode();
        if (!empty($queryFuncCode)) {
            return $queryFuncCode;
        }
        if (!method_exists($this, 'functionActionSetting')) {
            return null;
        }
        $actionSetting = $this->functionActionSetting();
        if (!isset($actionSetting)) {
            return null;
        }
        if (isset($actionSetting['appcode'])) {
            if (is_array($actionSetting['appcode'])) {
                return current($actionSetting['appcode']);
            }
            return $actionSetting['appcode'];
        } else {
            $defaultSetting = current($actionSetting);
            if (isset($defaultSetting['appcode'])) {
                if (is_array($defaultSetting['appcode'])) {
                    return current($defaultSetting['appcode']);
                } else {
                    return $defaultSetting['appcode'];
                }
            }
            return null;
        }
    }
    /**
     * 获取当前FunctionCode
     * @return mixed|null
     */
    public function getCurFunctionCode()
    {
        $queryFuncCode = WebUtility::getQueryFunctionCode();
        if (!empty($queryFuncCode)) {
            return $queryFuncCode;
        }
        if (!method_exists($this, 'functionActionSetting')) {
            return null;
        }
        $actionSetting = $this->functionActionSetting();
        if (!isset($actionSetting)) {
            return null;
        }
        if (isset($actionSetting['function'])) {
            if (is_array($actionSetting['function'])) {
                return current($actionSetting['function']);
            }
            return $actionSetting['function'];
        } else {
            $defaultSetting = current($actionSetting);
            if (isset($defaultSetting['function'])) {
                if (is_array($defaultSetting['function'])) {
                    return current($defaultSetting['function']);
                } else {
                    return $defaultSetting['function'];
                }
            }
            return null;
        }
    }
}

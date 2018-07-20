<?php
namespace app\framework\web\filters;

use Yii;
use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;
use app\framework\utils\RequestHelper;
use app\framework\biz\tenant\TenantReaderInterface;
use app\framework\biz\cache\OrganizationCacheManager;
use app\framework\settings\interfaces\SettingsAccessorInterface;
use app\framework\auth\UserSession;
use app\framework\biz\cache\models\NonceCache;
use app\framework\utils\WebUtility;
use app\framework\auth\interfaces\TokenAccessorInterface;
use app\framework\auth\interfaces\UserSessionAccessorInterface;

class AuthorizationFilter extends ActionFilter
{
    /**
     * @var TokenAccessorInterface
     */
    protected $tokenAccessor;

    /**
     * @var UserSessionAccessorInterface
     */
    protected $sessionAccessor;

    /**
     * @var SettingsAccessorInterface
     */
    protected $settingAccessor;

    public function __construct()
    {
        $this->tokenAccessor = \Yii::$container->get('app\framework\auth\interfaces\TokenAccessorInterface');
        $this->sessionAccessor = \Yii::$container->get('app\framework\auth\interfaces\UserSessionAccessorInterface');
        $this->settingAccessor = \Yii::$container->get('app\framework\settings\interfaces\SettingsAccessorInterface');

        if (!isset($this->tokenAccessor)) {
            throw new \Exception('没有注入 app\framework\auth\interfaces\TokenAccessorInterface 实例');
        }

        if (!isset($this->sessionAccessor)) {
            throw new \Exception('没有注入 app\framework\auth\interfaces\UserSessionAccessorInterface 实例');
        }

        if (!isset($this->settingAccessor)) {
            throw new \Exception('没有注入 app\framework\settings\interfaces\SettingsAccessorInterface 实例');
        }
    }

    public function beforeAction($action)
    { 
        if (parent::beforeAction($action)) { 
            return $this->checkLogin();
        } else {
            return false;
        }
    }


    /**
     * @param \yii\base\Action $action
     * @param mixed $result
     * @return mixed
     */
    public function afterAction($action, $result)
    {
        return parent::afterAction($action, $result);
    }
    
    protected function checkLogin()
    {
        $authorization = \Yii::$container->get('app\framework\auth\interfaces\AuthorizationInterface');
        $isAuthorized = $authorization->isAuthorized();

        if (!$isAuthorized) {
            $key = TenantReaderInterface::TENANT_QUERY_STRING_KEY;

            $route = \Yii::$app->controller->getRoute();

            $loginParams = [];
            if (!empty($_GET[$key])) {
                $loginParams[$key] = $_GET[$key];
            }

            $loginUrl = $this->getLoginUrl();
            if (\Yii::$app->request->isAjax || RequestHelper::isApi()) {
                echo json_encode(['result' => false, 'msg' => 'UnAuthorized', 'login_url' => $loginUrl]);
                \Yii::$app->response->setStatusCode(401, 'UnAuthorized');
            } else {
                if (empty($route)) {
                    Yii::$app->getResponse()->redirect($loginUrl);
                    Yii::$app->end();
                } else {
                    $loginUrl = WebUtility::buildQueryUrl($loginUrl, 'returnUrl=' . urlencode(Yii::$app->request->absoluteUrl));
                    Yii::$app->getResponse()->redirect($loginUrl);
                    Yii::$app->end();
                }
            }
            return false;
        }

        return true;
    }
 

    protected function getLoginUrl()
    { 
        return  '/auth/login'; 
    }
}

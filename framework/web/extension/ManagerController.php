<?php

namespace app\framework\web\extension;

use Yii;
use Exception; 
use app\framework\auth\UserSession;
use app\framework\auth\interfaces\AuthorizationInterface;
use app\framework\auth\interfaces\UserSessionAccessorInterface;
use app\framework\utils\WebUtility;

/**
 * ManagerController
 * @property UserSession $user
 * @property AuthorizationInterface $authorization
 * @property UserSessionAccessorInterface $sessionAccessor
 */
class ManagerController extends Controller
{

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) { 
           //$this->redirectToLogin(); 
            return true;
        }

        return false;
    }

    /**
     * Get current UserSession
     * @return UserSession
     */
    public function getUser()
    {
        return $this->sessionAccessor->getUserSession();
    }

    /**
     * Get AuthorizationInterface
     * @return AuthorizationInterface
     * @throws Exception
     */
    protected function getAuthorization()
    {
        $authorization = \Yii::$container->get('app\framework\auth\interfaces\AuthorizationInterface');
        return $authorization;
    }

    /**
     * Get UserSessionAccessorInterface
     * @return UserSessionAccessorInterface
     * @throws Exception
     */
    protected function getSessionAccessor()
    {
        $sessionAccessor = \Yii::$container->get('app\framework\auth\interfaces\UserSessionAccessorInterface');
        return $sessionAccessor;
    }

   

    public function getLoginUrl()
    { 
        return  '/auth/login';
    }

    public function redirectToLogin()
    {
        $loginUrl = $this->getLoginUrl();
        Yii::$app->getResponse()->redirect($loginUrl);
        Yii::$app->end();
    }

    public function behaviors()
    {
        return [
            [
                'class' => 'app\framework\web\filters\AuthorizationFilter',
            ] 
 

        ];
    }
}

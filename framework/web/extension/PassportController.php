<?php
namespace app\framework\web\extension;
use app\framework\auth\interfaces\TokenAccessorInterface;
use app\framework\auth\interfaces\UserSessionAccessorInterface;


class PassportController extends Controller
{
    /**
     * @var UserSessionAccessorInterface
     */
    protected $userSessionAccessor;

    /**
     * @var TokenAccessorInterface
     */
    protected $tokenAccessor;

    public function __construct($id, $module,
                                $config = [])
    {
        $this->userSessionAccessor = \Yii::$container->get('app\framework\auth\interfaces\UserSessionAccessorInterface');
        $this->tokenAccessor = \Yii::$container->get('app\framework\auth\interfaces\TokenAccessorInterface');

        parent::__construct($id, $module, $config);
    }

    public function logOutApp($token)
    {
        if (empty($token)) {
            return $this->json(['result' => false, 'msg' => 'sessionId is empty']);
        }

        try
        {
            $sessionAccessor = \Yii::$container->get('app\framework\auth\interfaces\UserSessionAccessorInterface');
            session_id($sessionAccessor->sessionId($token));
            session_start();
            session_destroy();
        }
        catch(\Exception $ex) {
            \Yii::warning($ex->getMessage());
        }
        return $this->json(['result' => true, 'msg' => 'success']);
    }

}

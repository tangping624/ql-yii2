<?php

namespace app\framework\web\extension;
 
use yii\base\Component;
use app\framework\db\EntityBase;
/**
 * 管理后台上下文对象 
 * @property \yii\db\Connection $tenantDb 当前租户DB连接
 * @property string $tenantCode This property is read-only.
 * @property \app\framework\auth\interfaces\UserSessionAccessorInterface $sessionAccessor This property is read-only.
 * @property \app\framework\auth\UserSession $user;
 */
class ManagerContext extends Component
{
    protected static $_user = null;
    protected static $_tenantDb = null;
    protected static $_tenantCode = null;

    /**
     * 当前租户是否有会员中心权限
     * @var bool
     */
    protected static $_hasMemberCenter = null;

    /**
     * @var array 租户的应用
     */
    protected static $_appCodeList = null;

    /**
     * 当前租户是否有会员中心权限
     * @return bool
     * @throws \Exception
     */
    public function getHasMemberCenter()
    {
        if(static::$_hasMemberCenter == null){

            $appCodeList = $this->getAppCodeList();
            if(empty($appCodeList)){
                static::$_hasMemberCenter = false;
                return false;
            }
            if(in_array('MemberCenter', $appCodeList)){
                static::$_hasMemberCenter = true;
            }else{
                static::$_hasMemberCenter = false;
            }
        }
        return static::$_hasMemberCenter;
    }
 

    /**
     * Get UserSessionAccessorInterface
     * @return \app\framework\auth\interfaces\UserSessionAccessorInterface
     */
    public function getSessionAccessor()
    {
        $sessionAccessor = \Yii::$container->get('app\framework\auth\interfaces\UserSessionAccessorInterface');
        return $sessionAccessor;
    }

    /**
     * @return \app\framework\auth\UserSession
     */
    public function getUser()
    {
        if(static::$_user == null){
            static::$_user = $this->getSessionAccessor()->getUserSession();
        }
        return static::$_user;
    }
 

    /**
     * 当前租户DB连接
     * @return \yii\db\Connection
     */
    public function getTenantDb()
    {
        if(static::$_tenantDb == null){
            static::$_tenantDb =EntityBase::getDb();
        }

        return static::$_tenantDb;
    }

    
}
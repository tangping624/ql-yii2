<?php 
namespace app\framework\biz\cache\models;

use app\framework\biz\cache\ApplicationCacheManager;
use Yii;
use yii\db\Query;
use app\framework\biz\cache\AccountSessionManager;
use app\framework\entities\TApp;
use app\framework\entities\TAppFunction; 
use app\framework\entities\TAccountApp;
use app\framework\cache\CacheObject;
use app\framework\utils\ArrayHelper;

/**
 * Class UserGroupRightsCacheObject 用户组权限缓存对象
 * @package app\framework\biz\cache\models
 */
class UserAccountRightsCacheObject extends CacheObject
{
    /**
     * @var string 用户所属组
     */
    private $_level;
    /**
     * 公众号ID
     * @var type 
     */
    private $_accountId;
     /**
     * 套餐类型
     * @var type 
     */
    private $_package_type;

    /**
     * @var array 用户有权限的模块列表
     */
    private $_funcList = [];

    /**
     * @var array 当前用户组的模块导航（有权限模块+业务逻辑）
     */
    private $_funcListForNav = [];

    /**
     * @var array 当前用户组有权限的应用
     */
    private $_appList = [];

    /**
     * @var array 当前用户组的应用导航（有权限应用+业务逻辑）
     */
    private $_appListForNav = [];

    /**
     * @param string $groupId
     * @param int $scope
     */
    public function __construct($accountId,$level,$package_type, $scope = 0)
    {
        $this->_level = $level;
        $this->_accountId = $accountId;
        $this->_package_type = $package_type;

        yii::info('UserAccountRightsCacheObject:init start:' .$accountId.'_'. $level);

        $this->init();

        //记录日志开始
        ob_start();
        print_r($this);
        $dumpString = ob_get_contents();
        ob_end_clean();
        yii::info(
            'UserAccountRightsCacheObject:init completed:'
             .$accountId.'_'. $level . '$this:' . $dumpString
        );
        //记录日志结束

        parent::__construct($accountId.'_'. $level, $scope);
    }

    /**
     * 初始化
     */
    private function init()
    {
        //初始化app列表
        $this->initAppList();

        //初始化所有app的funcAction列表
        $appCodeList = ArrayHelper::getColumn($this->_appList, 'app_code');
//        $this->initFuncActionList($appCodeList);

        //初始化所有app的func列表
        $this->initFuncList($appCodeList);

        //初始化func列表（导航）
        $this->initFuncListForNav();

        //初始化app列表（导航）
        $this->initAppListForNav();
    }

    /**
     * 初始化-获取有权限应用列表
     */
    private function initAppList()
    {
        //有权限的应用Code列表
        $appCodeList = TAccountApp::find()
            ->where(['account_id' => $this->_accountId, 'is_deleted' => 0])
            ->select('app_code')
            ->column();

        if (!empty($appCodeList)) {
            //有权限的应用信息列表
            $find = TApp::find()
                ->where(['app_code' => $appCodeList, 'is_deleted' => 0]);
            if($this->_level==3){
                $find = $find->andWhere(['not', ['app_code' => 'account']]);
            }
             $this->_appList = $find
                ->select('app_code, app_name, type  , no_show_in_sysnav ,site_code')
                ->orderBy('sort')
                ->asArray()
                ->all();

            //处理应用类型字段
            foreach ($this->_appList as &$app) {
                if (empty($app['type'])) {
                    $app['type'] = [];
                } else {
                    $app['type'] = explode(',', $app['type']);
                }
            }
        }
    }

    /**
     * 初始化-获取有权限的动作点列表
     * @param $appCodeList array|string
     */
//    private function initFuncActionList($appCodeList)
//    {
//        //应用下当前用户组对应的角色
//        $roleIds = TUGroupAppRole::find()
//            ->where([
//                'ugroup_id' => $this->_groupId,
//                'app_code' => $appCodeList,
//                'is_deleted' => 0
//            ])
//            ->select('`approle_id`')
//            ->column();
//
//        //角色有权限的功能动作点
//        $funcAction = TAppRolePermission::find()
//            ->where([
//                'app_code' => $appCodeList,
//                'role_id' => $roleIds,
//                'is_deleted' => 0
//            ])
//            ->select('app_code, func_code, action_code')
//            ->distinct()
//            ->asArray()
//            ->all();
//
//        //赋值私有变量（按 app_code->func_code->[action_code]索引）
//        $funcAction = ArrayHelper::group($funcAction, 'app_code');
//        foreach ($funcAction as &$app) {
//            $app = ArrayHelper::group($app, 'func_code');
//            foreach ($app as &$func) {
//                $action = ArrayHelper::getColumn($func, 'action_code');
//                $func = $action;
//            }
//        }
//
//        $this->_funcActionList = $funcAction;
//    }

    /**
     * 初始化-获取有权限的模块列表
     */
    private function initFuncList($appCodeList)
    {
        if (count($appCodeList)==0) {
            return;
        }

//        $funcList = [];
//        foreach ($this->_appList as $appCode => $functions) {
//            $funcKeys = array_keys($functions);
//            foreach ($funcKeys as $funcCode) {
//                $funcList[] = $appCode . '_' . $funcCode; // 处理app下func_code重复
//            }
//        }

        $dbConn = TAppFunction::getDb();
        $query = (new Query())
            ->from('t_appfunction main')
            ->leftJoin('t_appfunction parent', 'main.parent_id = parent.id and parent.is_deleted = 0')
            ->where(['main.is_deleted' => 0,  'main.app_code' => $appCodeList ,'main.package_type'=>$this->_package_type])//'CONCAT_WS(\'_\',main.app_code,main.func_code)' => $funcList])
           // ->select('main.mutex_app_code,main.app_code,main.provideby_site_code, main.func_code,main.func_name,main.func_url,main.icon,main.no_show_in_funcnav,main.link_target,parent.func_code as parent_func_code,parent.func_name as parent_func_name,parent.icon as parent_func_icon')
            ->select('main.app_code,main.mutexcode, main.func_code,main.func_name,main.func_url,main.icon,main.no_show_in_funcnav,parent.func_code as parent_func_code,parent.func_name as parent_func_name,parent.icon as parent_func_icon')
            ->orderBy('main.app_code,IFNULL(`parent`.`sort`,`main`.`sort`),IFNULL(`parent`.`func_code`,`main`.`func_code`),case when ISNULL(parent.sort) then -1 else main.sort end');

        $command = $query->createCommand($dbConn);
        $rows = $command->queryAll();
        $rows= $this->setMutexAppFunc($rows);
        $this->_funcList = ArrayHelper::group($rows, 'app_code');
    }

    /*
     * 如果互斥的应用已经授权，则当前模块不需要显示
     */
    private function setMutexAppFunc($rows)
    {
        foreach ($rows as $key => $row) {
            $mutexAppCode = $row['mutexcode'];
            if (!empty($mutexAppCode)) {
               foreach ($this->_appList as $app) {
                    if ($mutexAppCode==$app['app_code'] ) {
                         unset($rows[$key]);
                    }
                } 
            }
        }
        return $rows;
    }

    /**
     * 初始化-获取有权限的模块列表（导航）
     */
    private function initFuncListForNav()
    {
        if (empty($this->_funcList)) {
            return;
        }

        $this->_funcListForNav = [];
        foreach ($this->_funcList as $appCode => $functions) {
            $this->_funcListForNav[$appCode] = [];
            foreach ($functions as $function) {
                $isShowInNav = !$function['no_show_in_funcnav'];
                if ($isShowInNav) {
                    $this->_funcListForNav[$appCode][] = $function;
                }
            }
        }
    }

    /**
     * 初始化-应用信息列表（导航）
     */
    private function initAppListForNav()
    {
        if (empty($this->_appList)) {
            return;
        }
        $this->_appListForNav = [];
        foreach ($this->_appList as $app) {
           // $isExistsActionRight = array_key_exists($app['app_code'], $this->_funcActionList);
            $isShowInNav = intval($app['no_show_in_sysnav']) === 0;

            if ($isShowInNav) {
                $this->_appListForNav[] = $app;
            }
        }

        \Yii::error('applist:' . json_encode($this->_appList));
        //\Yii::error('funcActionList' . json_encode($this->_funcActionList));
    }

    /**
     * 获得用户有权限的模块编码(func_code)
     * @param $appCode string 应用code
     * @return array 功能模块集合
     */
    public function getFunctions($appCode)
    {
        if (empty($appCode)) {
            return [];
        }
        if (!isset($this->_funcList[$appCode])) {
            return [];
        }

        return $this->_funcList[$appCode];
    }

    /**
     * 获取用户的模块导航（导航使用）
     * @param $appCode string 应用code
     * @return array
     */
    public function getFunctionsForNav($appCode)
    {
        if (empty($appCode)) {
            return [];
        }
        if (!isset($this->_funcListForNav[$appCode])) {
            return [];
        }

        $rows = $this->_funcListForNav[$appCode];

//      if ($appCode == "OperationCenter") {
//            //运营中心模块权限特殊控制
//            //若租户只购买了管理中心、运营中心、移动验房，只显示运营中心下的便民信息、粉丝管理和客户台账
//            $isSpecial = true;
//            $tmpAppCodeList = ['ManagementCenter', 'MobileCheckRoom', 'OperationCenter'];
//            $sessionAccessor = \Yii::$container->get('app\framework\auth\interfaces\UserSessionAccessorInterface');
//            $userSession = $sessionAccessor->getUserSession();
//            if (isset($userSession)) {
//                $tenantAppCodeCache = ApplicationCacheManager::getAppCodeListByTenant($userSession->tenantCode);
//            }
//            if (isset($tenantAppCodeCache)) {
//                foreach ($tenantAppCodeCache->appCodeList as $tac) {
//                    if (!in_array($tac, $tmpAppCodeList)) {
//                        $isSpecial = false;
//                        break;
//                    }
//                }
//            }
//            if ($isSpecial) {
//                foreach ($rows as $idx => $row) {
//                    if ($row['parent_func_code'] == 'Operation') {
//                        if ($row['func_code'] != "ConvenientInfo") {
//                            unset($rows[$idx]);
//                        }
//                    } elseif ($row['parent_func_code'] == 'CustomerManagement') {
//                        if ($row['func_code'] != "Fan" && $row['func_code'] != "Member") {
//                            unset($rows[$idx]);
//                        }
//                    }
//                }
//            }
            
            
//            //买了质检、验房、客服任意一款产品才展示承建商管理菜单 ['MobileCheckQuality','MobileCheckRoom','CustomerService']
//            $tmpAppCodeList = ['MobileCheckQuality','MobileCheckRoom','CustomerService'];
//            $isSpecial = false;//默认没有
//            if(!empty($tenantAppCodeCache->appCodeList)){
//            	foreach ($tenantAppCodeCache->appCodeList as $tac) {
//            		if (in_array($tac, $tmpAppCodeList)) {
//            			$isSpecial = true;//有啦
//            			break;
//            		}
//            	}
//            }
//            if(!$isSpecial){//没有
//            	foreach ($rows as $idx => $row) {
//            		if ($row['func_code'] == 'ConstructionManage' && $row['app_code'] == 'OperationCenter') {
//            			unset($rows[$idx]);
//            		}
//            	}
//            }
            
            
//        }
       
        return  $rows;
    }

//    /**
//     * 获得用户有权限的功能点
//     * @param $appCode string 应用code
//     * @param string $funcCode 功能模块Code
//     * @return array 功能点集合
//     */
//    public function getActions($appCode, $funcCode)
//    {
//        if (empty($appCode) || empty($funcCode)) {
//            return [];
//        }
//        if (!isset($this->_funcActionList[$appCode])) {
//            return [];
//        }
//        if (!isset($this->_funcActionList[$appCode][$funcCode])) {
//            return [];
//        }
//
//        return $this->_funcActionList[$appCode][$funcCode];
//    }

    /**
     * 检查用户在应用下的功能模块的功能点
     * @param $appCode string 应用code
     * @param string $funcCode 功能模块编码 
     * @return bool 是否具备功能点权限
     */
    public function checkRight($appCode, $funcCode)
    {
        //如果没有传递则表示不校验
        if (empty($appCode) || empty($funcCode)) {
            return true;
        }

        if (!isset($this->_funcList[$appCode])) {
            return false;
        }
        if (!isset($this->_funcList[$appCode][$funcCode])) {
            return false;
        } 
        return true;
    }

    /**
     * 获取有权限的应用
     * @return array
     */
    public function getApps()
    {
        return $this->_appList;
    }

    /**
     * 获取有权限的应用（导航使用）
     * @return array
     */
    public function getAppsForNav()
    {
        return $this->_appListForNav;
    }
}
 
<?php
 
namespace app\framework\biz\cache\models;
 
use Yii;
use yii\db\Query; 
use app\framework\entities\TAppFunction; 
use app\framework\cache\CacheObject;
use app\framework\utils\ArrayHelper;

/**
 * Class UserGroupRightsCacheObject 用户组权限缓存对象
 * @package app\framework\biz\cache\models
 */
class UserGroupRightsCacheObject extends CacheObject
{  
    /**
     * @var array 用户有权限的模块列表
     */
    private $_funcList = []; 

    /**
     * @param string $groupId
     * @param int $scope
     */
    public function __construct( $scope = 1)
    {
       
        yii::info('UserGroupRightsCacheObject:init start:');

        $this->init();

        //记录日志开始
        ob_start();
        print_r($this);
        $dumpString = ob_get_contents();
        ob_end_clean();
        yii::info(
            'UserGroupRightsCacheObject:init completed:'
             . '$this:' . $dumpString
        );
        //记录日志结束

        parent::__construct(SUPER_USER_ID, $scope);
    }

    /**
     * 初始化
     */
    private function init()
    { 
        //初始化所有app的func列表
        $this->initFuncList(); 
    }
 
   
    /**
     * 初始化-获取有权限的模块列表
     */
    private function initFuncList()
    { 
        $dbConn = TAppFunction::getDb();
        $query = (new Query())
            ->from('t_appfunction main')
            ->leftJoin('t_appfunction parent', 'main.parent_id = parent.id and parent.is_deleted = 0')
            ->where(['main.is_deleted' => 0 ])
            ->select('main.app_code,main.func_code,main.func_name,main.func_url,main.icon,main.no_show_in_funcnav, parent.func_code as parent_func_code,parent.func_name as parent_func_name,parent.icon as parent_func_icon')
            ->orderBy('main.app_code,IFNULL(`parent`.`sort`,`main`.`sort`),IFNULL(`parent`.`func_code`,`main`.`func_code`),case when ISNULL(parent.sort) then -1 else main.sort end');

        $command = $query->createCommand($dbConn);
        $rows = $command->queryAll();

        $this->_funcList =$rows;
    } 
   
    /**
     * 获取有权限的应用（导航使用）
     * @return array
     */
    public function getFuncListForNav()
    {
        return $this->_funcList;
    }
}

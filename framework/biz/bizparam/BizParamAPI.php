<?php

namespace app\framework\biz\bizparam;

use app\framework\cache\CacheObject;
use app\models\BizParam;
use app\framework\cache\CachePackageManger;
use app\framework\biz\bizparam\services\AutoCodeService;
use app\framework\biz\bizparam\services\ParameterService;
use app\framework\biz\bizparam\services\ParameterValueService;
use app\framework\biz\bizparam\models\TParameterValue;

//业务参数
defined('BIZ_CODE_ATTENTION_URL') or define('BIZ_CODE_ATTENTION_URL', 'AttentionUrl'); //关注信息的URL
defined('BIZ_CODE_CERTIFICATE_TYPE') or define('BIZ_CODE_CERTIFICATE_TYPE', 'IdType'); //证件类型
defined('BIZ_CODE_LOGIN_POINT') or define('BIZ_CODE_LOGIN_POINT', 'LoginPoint'); //登录积分
defined('BIZ_CODE_RECOMMEND_POINT') or define('BIZ_CODE_RECOMMEND_POINT', 'RecommendPoint'); //推荐入会积分
defined('BIZ_CODE_RECOMMEND_AUTH_POINT') or define('BIZ_CODE_RECOMMEND_AUTH_POINT', 'AuthPoint'); //认证积分
defined('BIZ_CODE_SIGNIN_POINT') or define('BIZ_CODE_SIGNIN_POINT', 'SignInPoint'); //认证积分
defined('BIZ_CODE_BINDING_RULE') or define('BIZ_CODE_BINDING_RULE', 'OwnerAuthType'); //房产绑定类型
defined('BIZ_CODE_MEMBER_CUSTOM') or define('BIZ_CODE_MEMBER_CUSTOM', 'MemberCustom'); //用户自定义信息
defined('BIZ_CODE_NAME_RULE') or define('BIZ_CODE_NAME_RULE', 'NameUpdateRule'); //会员姓名修改规则


/**
 * 缓存业务参数
 **/
class BizParamAPI
{
    //用户数据缓存时长(s)
    const CACHE_USER_TIME_SECONDS = 86400;

    /**
     * @var BizParamAPI
     */
    private static $_instance;

    /**
     * @var ParameterService
     */
    private static $_parameterService;

    /**
     * @var ParameterValueService
     */
    private static $_parameterValueService;

    /**
     * @var AutoCodeService
     */
    private static $_autoCodeService;

    /**
     * @param ParameterService $parameterService
     * @param ParameterValueService $parameterValueService
     * @param AutoCodeService $autoCodeService
     */
    private function _construct($parameterService, $parameterValueService, $autoCodeService)
    {
        static::$_parameterService = $parameterService;
        static::$_parameterValueService=$parameterValueService;
        static::$_autoCodeService = $autoCodeService;
    }

    /**
     * @return BizParamAPI
     * @throws \yii\base\InvalidConfigException
     */
    public static function instance($parameterService = null, $autoCodeService = null,$parameterValueService=null)
    {
        if (!isset(static::$_instance)) {
            $parameterService = ($parameterService == null ? \Yii::$container->get('app\framework\biz\bizparam\services\ParameterService') : $parameterService);
            $parameterValueService = ($parameterValueService == null ? \Yii::$container->get('app\framework\biz\bizparam\services\ParameterValueService') : $parameterValueService);
            $autoCodeService = ($autoCodeService == null ? \Yii::$container->get('app\framework\biz\bizparam\services\AutoCodeService') : $autoCodeService);
            static::$_parameterService = $parameterService;
            static::$_parameterValueService=$parameterValueService;
            static::$_autoCodeService = $autoCodeService;
            static::$_instance = new BizParamAPI($parameterService, $autoCodeService);
        }
        return self::$_instance;
    }

    public function getCacheKeyName($mykey, $scopeid = null)
    {
        $cacheKey = "businessparamters";
        if ($mykey) {
            $cacheKey .= '_' . $mykey;
        }
        if ($scopeid) {
            $cacheKey .= '_' . $scopeid;
        }
        $cacheKey = sha1($cacheKey);
        return $cacheKey;
    }

    /**
     * 获得业务参数
     * @param string $key 业务关键字
     * @param string $scopeId  应用范围
     * @return null|object
     */
    public function getBusinessParameters($key, $scopeId = null)
    {
        //1、根据code获得对应的业务参数类型
        $parameter = $this->getParameterByCode($key);
        $data = array();
        //2、根据业务参数类型获得对应的业务参数数据
        if (isset($parameter)) {
            switch ($parameter[0]["type"]) {
                case "自动编码":
                    $data = self::$_autoCodeService->getAutoCodeInfoByScopeId($parameter[0]["id"], $scopeId);
                    break;
                default:
                    //标准业务参数
                    $data = $this->getStandardBusinessParameters($parameter[0]["id"], $scopeId, $parameter[0]["type"]);
                    break;
            }
        }
        return $data;
    }


    public function getStandardBusinessParametersItemsTitleById($itemId, $key, $scopeId = null)
    {
        $title = $itemId;
        $parameter = $this->getParameterByCode($key);
        if (isset($parameter)) {
            $data = $this->getStandardBusinessParameters($parameter[0]["id"], $scopeId, "列表");
            $result = array_filter($data, function ($param) use ($itemId) {
                if ($param['id'] == $itemId) {
                    return true;
                }
                return false;
            });
            $result = array_values($result);
            if (isset($result) && count($result) > 0) {
                $title = $result[0]["title"];
            }
        }
        return $title;
    }

    /**
     * 获得标准的业务参数值
     * @param $parameterId
     * @param $scopeId 应用范围
     * @param $parameterType 业务参数类型（单值、层级、列表）与业务参数表Type字段对应
     * @return array
     */
    private function getStandardBusinessParameters($parameterId, $scopeId, $parameterType)
    {
        $data = self::$_parameterService->getParameterValueByParameterId($parameterId, $scopeId);
        if ($parameterType == '层级') {
            $data = $this->formatToArrayTree($data);
        }
        return $data;
    }

    /**
     * 将列表数组格式化为层级结构的数组
     * @param $tree
     * @param string $rootId
     * @return array
     */
    private function formatToArrayTree($tree, $rootId = '0')
    {
        $result = array();
        foreach ($tree as $leaf) {
            $leaf['children'] = null;
            $parentId = (isset($leaf['parent_id']) ? $leaf['parent_id'] : '0');
            if ($parentId == $rootId) {
                foreach ($tree as $subLeaf) {
                    if ($subLeaf['parent_id'] == $leaf['id']) {
                        $leaf['children'] = $this->formatToArrayTree($tree, $leaf['id']);
                    }
                }
                $result[] = $leaf;
            }
        }
        return $result;
    }


    /**
     * 根据业务参数CODE获得对应的记录
     * @param $key
     * @return array
     */
    private function getParameterByCode($key)
    {
        $data = $this->getAllParameterKey();
        $result = array_filter($data, function ($param) use ($key) {
            if (strtolower($param['code']) == strtolower($key)) {
                return true;
            }
            return false;
        });
        return $result == null ? $result : array_values($result);
    }

    /**
     * 获取所有参数的KEY键与类型
     * @author denghg 2015-4-16
     * @return array
     */
    private function getAllParameterKey()
    {
//        $myKey = "bizParameterKey";
//        $cacheKey = $this->getCacheKeyName($myKey);
//        $cacheObject = CachePackageManger::instance($cacheKey);
//        $data = $cacheObject->get();
//        if (!isset($data)) {
        $data = self::$_parameterService->getAllParameterKey();
//            $cacheObject->set($data);
//        }
        return $data;
    }

    /**
     * 清除相应的业务参数缓存
     * @param $mykey string 业务关键字
     * @param $scopeid string 应用范围
     * @throws \Exception
     */
    public function clearBusinessParametersCache($mykey, $scopeid = null)
    {
        $cacheKey = $this->getCacheKeyName($mykey, $scopeid);
        $cacheObject = CachePackageManger::instance($cacheKey);
        $cacheObject->remove();
    }

    /**
     * 更新自动编码的下一个序列号
     * @param $id 自动编码表的ID
     * @param $currentDate
     * @param $nextSerialNo 下一个序列号
     */
    public function updateAutoCodeNextSerial($id, $currentDate, $nextSerialNo)
    {
        return self::$_autoCodeService->updateAutoCodeNextSerial($id, $currentDate, $nextSerialNo);
    }

    /**
     * 只更新业务参数值
     * @param $parameterValueId  业务参数值ID
     * @param $title 业务参数的title
     * @return bool
     * @throws \Exception
     */
    public function updateParameterTitle($parameterValueId, $title)
    {
        return self::$_parameterValueService->updateParameterTitle($parameterValueId, $title);
    }

    /**
     * 只更新业务参数值
     * @param $parameterValueId  业务参数值ID
     * @param $value 业务参数的value
     * @return bool
     * @throws \Exception
     */
    public function updateParameterValue($parameterValueId, $value)
    {
        return self::$_parameterValueService->updateParameterValue($parameterValueId, $value);
    }
    
     /**
     * 添加新业务参数
     * @param $param 业务参数
     * @return bool
     * @throws \Exception
     */
    public function insertParameterValue(TParameterValue $paramterValueEntity)
    {
        return self::$_parameterValueService->insert($paramterValueEntity);
    }  
    
     /**
     * 根据code获取新业务参数
     * @param $code
     * @return bool
     * @throws \Exception
     */
    public function getParameterIdByCode($code)
    {
        return self::$_parameterValueService->getParameterIdByCode($code);
    }
    
     /**
     * 删除新业务value
     * @param $parameter_value_id
     * @return bool
     * @throws \Exception
     */
    public function deleteParameterValue($parameter_value_id)
    {
        return self::$_parameterValueService->delete($parameter_value_id);
    }

     /**
     * 根据应用 返回业务参数
     * @param $appCode
     * @return bool
     * @throws \Exception
     */
    public function getParameterByAppCode($appCode)
    {
        return self::$_parameterService->getAllParameter($appCode);
    }

     /**
     * 根据应用 返回业务参数
     * @param $groupName
     * @return bool
     * @throws \Exception
     */
    public function getParameterByGroupName($groupName)
    {
        return self::$_parameterService->getAllParameterGroupName($groupName);
    }

    /**
     * 获取项目列表及指定参数的配置信息
     * @param $parameterCode
     * @param $projType，first=1级项目，final=末级项目，其它=所有项目
     * @param $corpId，取指定公司下的项目列表，为空则获取系统中所有项目列表
     * @return array
     */
    public function getProjectListWithParameterValue($parameterCode, $projType = 'first', $corpId = null)
    {
        return self::$_parameterService->getProjectListWithParameterValue($parameterCode, $projType, $corpId);
    }
}

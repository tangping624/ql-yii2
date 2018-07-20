<?php

namespace app\framework\cache;

use app\framework\cache\interfaces\KeyPrefixGeneratorInterface;

class MobileKeyPrefixGenerator implements KeyPrefixGeneratorInterface
{

    public function createKeyPrefix($scope = 1, $scopeId = '')
    {
        $prefix = '';

        if ($scope == 0) {
            return $prefix;

        } elseif ($scope == 1 || $scope == 2) {

            //租户/分公司范围内的缓存;
            $scopeId = empty($scopeId) ? $this->_getCurrentScopeId($scope) : $scopeId;
            $prefix = $scopeId;

        } elseif ($scope == 3 || $scope == 4) {
            //租户应用级别缓存
            $appId = \Yii::$app->id;
            if (empty($appId)) {
                throw new \Exception('$appId 不能为空');
            }

            $scopeId = empty($scopeId) ? $this->_getCurrentScopeId($scope) : $scopeId;
            $prefix = $scopeId . static::KEY_SEPARATOR . $appId;
        }

        return $prefix;
    }

    private function _getCurrentScopeId($scope)
    {

        $tenantReader = \Yii::$container->get('app\framework\biz\tenant\TenantReaderInterface');
        if($scope == 1 || $scope == 3)
        {
            $scopeId = $tenantReader->getCurrentTenantCode();
            return $scopeId;

        }elseif($scope == 2 || $scope == 4)
        {
            $scopeId =$tenantReader->getPublicId();
            return $scopeId;
        }else{
            throw new \InvalidArgumentException('无效的$scope值 ' . $scope);
        }
    }
}
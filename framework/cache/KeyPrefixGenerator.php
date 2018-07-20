<?php

namespace app\framework\cache;

use app\framework\auth\interfaces\TokenAccessorInterface;
use app\framework\biz\tenant\TenantReaderInterface;
use app\framework\cache\interfaces\KeyPrefixGeneratorInterface;

class KeyPrefixGenerator implements KeyPrefixGeneratorInterface
{

    /**
     * @var TokenAccessorInterface
     */
    private $_tokenAccessor;

    public function __construct()
    {
        $this->_tokenAccessor = \Yii::$container->get('app\framework\auth\interfaces\TokenAccessorInterface');
    }

    /**
     * @inheritdoc
     */
    public function createKeyPrefix($scope = 0, $scopeId = '')
    {
        $prefix = '';

      

        return $prefix;
    }

   
}

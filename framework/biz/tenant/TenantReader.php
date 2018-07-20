<?php

namespace app\framework\biz\tenant;

use yii\base\NotSupportedException;

/**
 * 租户相关信息读取
 */
class TenantReader implements TenantReaderInterface
{

    /**
     * @inheritdoc
     */
    protected $tokenAccessor;

    public function __construct()
    {
        $this->tokenAccessor = \Yii::$container->get('app\framework\auth\interfaces\TokenAccessorInterface');
    }
 

    /**
     * @inheritdoc
     */
    public function getOpenId($openid='')
    {
        throw new NotSupportedException('getOpenId');
    }

    /**
     * @inheritdoc
     */
    public function getPublicId()
    {
        throw new NotSupportedException('getPublicId');
    }
}
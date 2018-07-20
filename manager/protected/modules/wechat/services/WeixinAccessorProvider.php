<?php

namespace app\modules\wechat\services;

use app\framework\weixin\AccessTokenHelper;
use app\framework\weixin\proxy\fw\MassMessage;
use app\framework\weixin\proxy\fw\MassPreview;
use app\modules\wechat\repositories\AccountRepository;

class WeixinAccessorProvider
{
    private $_accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->_accountRepository = $accountRepository;
    }

    /**
     * 获取群发微信接口代理
     * @param string $accountId
     * @return MassMessage
     * @throws \Exception
     */
    public function getWeixinMassMessageProxy($accountId)
    {
        $accessTokenHelper = $this->getAccessTokenHelper($accountId);
        $apiProxy = new MassMessage($accessTokenHelper);

        return $apiProxy;
    }

    /**
     * @param $accountId
     * @return MassPreview
     * @throws \Exception
     */
    public function getWeixinMassPreviewProxy($accountId)
    {
        $accessTokenHelper = $this->getAccessTokenHelper($accountId);
        $apiProxy = new MassPreview($accessTokenHelper);

        return $apiProxy;
    }

    /**
     * @param $accountId
     * @return AccessTokenHelper
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function getAccessTokenHelper($accountId)
    {
        $originalId = $this->_accountRepository->getWeChatOriginalId($accountId);
        /** @var \app\framework\weixin\interfaces\IAccessTokenRepository $accessTokenRepository */
        $accessTokenRepository = \Yii::$container->get('app\framework\weixin\interfaces\IAccessTokenRepository');
        if (!isset($accessTokenRepository)) {
            throw new \Exception('未注入app\framework\weixin\interfaces\IAccessTokenRepository实例');
        }
        $accessTokenHelper = new AccessTokenHelper($originalId, $accessTokenRepository);

        return $accessTokenHelper;
    }
}
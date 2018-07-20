<?php

namespace app\modules\api\services;

use app\modules\api\repositories\MemberRepository;
use app\modules\api\repositories\PublicAccountRepository;
use app\modules\api\repositories\WeixinLogRepository;
use app\modules\ServiceBase;
use app\framework\weixin\AccessTokenHelper;
use app\framework\weixin\proxy\fw\TemplateMessage;

class WeixinApiService extends ServiceBase
{
    protected $memberRepository;
    protected $publicAccountRepository;
    protected $weixinLogRepository;

    public function __construct(
        MemberRepository $memberRepository,
        PublicAccountRepository $publicAccountRepository,
        WeixinLogRepository $weixinLogRepository
    ) {
        $this->publicAccountRepository = $publicAccountRepository;
        $this->memberRepository = $memberRepository;
        $this->weixinLogRepository = $weixinLogRepository;
    }

    /**
     * @param array $memberId
     * @return array
     */
    public function getOpenidListByMemberId($memberId)
    {
        if (empty($memberId)) {
            return [];
        }
        return $this->memberRepository->getOpenidList($memberId);
    }

    /**
     * @param array $memberId
     * @param string $accountId
     * @return array
     */
    public function getOpenidAndFanIdListByMemberId($memberId, $accountId = '')
    {
        if (empty($memberId)) {
            return [];
        }

        return $this->memberRepository->getOpenidAndFanIdList($memberId, $accountId);
    }

    /**
     * @param $corpId
     * @return false|array
     * @throws \Exception
     */
    public function getAccountIdByCorpId($corpId)
    {
        if (empty($corpId)) {
            throw new \InvalidArgumentException('$corpId');
        }

        $accountId = $this->publicAccountRepository->getAccountIdByCorpId($corpId);
//        if ($accountId == false) {
//            throw new \Exception("找不到公司{$corpId}的公众号!");
//        }
        return $accountId;
    }

    /**
     * 微信模板消息日志
     * @param array $templateMsgLogRowData
     * @return int
     */
    public function insertTemplateMsgLog($templateMsgLogRowData)
    {
        return $this->weixinLogRepository->insertTemplateMsgLog($templateMsgLogRowData);
    }
    
    /**
     * 获取模板消息API代理
     * @param string $accountId 公众号ID
     * @return TemplateMessage
     * @throws \Exception
     */
    public function getTemplateMsgProxy($accountId)
    {
        if (!\Yii::$container->has('app\framework\weixin\interfaces\IAccessTokenRepository')) {
            throw new \Exception('未注入app\framework\weixin\interfaces\IAccessTokenRepository实例');
        }

        $accessTokenRepository = \Yii::$container->get('app\framework\weixin\interfaces\IAccessTokenRepository');
        $wxAccessTokenHelper= new AccessTokenHelper($accountId, $accessTokenRepository);
        $msgProxy = new TemplateMessage($wxAccessTokenHelper);
        
        return $msgProxy;
    }
}


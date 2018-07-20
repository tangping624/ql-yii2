<?php

namespace app\modules\api\controllers;

use ReflectionClass;
use app\controllers\WxapiControllerBase;
use app\framework\weixin\AccessTokenHelper;
use app\framework\weixin\interfaces\IAccessTokenRepository;
use app\framework\weixin\interfaces\IMsgTemplateRepository;
use app\framework\weixin\proxy\fw\TemplateMessage;
use app\modules\api\services\WeixinApiService;

class WxapiController extends WxapiControllerBase
{
    /**
     * @var WeixinApiService
     */
    protected $weixinApiService;
    /**
     * @var IAccessTokenRepository
     */
    protected $accessTokenRepository;

    public function __construct($id, $module, WeixinApiService $weixinApiService, $config = [])
    {
        if (!\Yii::$container->has('app\framework\weixin\interfaces\IAccessTokenRepository')) {
            throw new \Exception('未注入app\framework\weixin\interfaces\IAccessTokenRepository实例');
        }

        $this->accessTokenRepository = \Yii::$container->get('app\framework\weixin\interfaces\IAccessTokenRepository');
        
        $this->weixinApiService = $weixinApiService;
        parent::__construct($id, $module, $config);
    }

    protected function createInstance($className, $args = [])
    {
        $reflection_class = new ReflectionClass($className);
        if (empty($args)) {
            return $reflection_class;
        }
        return $reflection_class->newInstanceArgs($args);
    }

    /**
     * ['member_id'=>, 会员ID
     * 'template_class'=>, 消息模板类名
     * 'params'=>, 模板变量参数, 按参数顺序
     * 'corp_id'=> 公司ID]
     */
    public function actionSendTemplateMessage()
    {
        if ($this->request->isGet) {
            return $this->json(['errcode' =>40013, 'errmsg' => '无效的请求']);
        }

        $memberId = $this->request->post('member_id', []);
        $templateClass = $this->request->post('template_class', '');
        $params = $this->request->post('params', []);
        $corpId = $this->request->post('corp_id', '');
        $validResult = $this->validForSendTlpMsg($memberId, $templateClass, $params, $corpId);
        
        // 校验
        if ($validResult !== true) {
            return $this->json($validResult);
        }
        
        $accountInfo = $this->weixinApiService->getAccountIdByCorpId($corpId);
        if ($accountInfo == false) {
            return $this->json(['errcode' => 40013, 'errmsg' => "找不到公司{$corpId}的公众号"]);
        }
        
        $accountId = $accountInfo['id'];
        $fans = $this->weixinApiService->getOpenidAndFanIdListByMemberId($memberId, $accountId);
        if (empty($fans)) {
            return $this->json(['errcode' => 40013, 'errmsg' => '没有找到任何粉丝']);
        }
        
        // 发送
        try {
            $sendResult = $this->sendTlpMsg($memberId, $fans, $accountId, $templateClass, $params);
        } catch (\Exception $ex) {
            \Yii::error($ex);
            return $this->json(['errcode' => 40013, 'success' => [], 'failure' => array_column($fans, 'openid'), 'errmsg' => $ex->getMessage()]);
        }

        if (count($sendResult['failure']) > 0) {
            return $this->json(['errcode' => 40013, 'success' => $sendResult['success'], 'failure' => $sendResult['failure'], 'errmsg' => json_encode($sendResult['failure'], JSON_UNESCAPED_UNICODE)]);            
        }
        
        // 成功
        return $this->json(['errcode' => 0, 'success' => $sendResult['failure']]);
    }
    
    private function validForSendTlpMsg($memberId, $tlpMsgClass, $tlpMsgParams, $corpId)
    {
        if (empty($memberId)) {
            return ['errcode' => 1002, 'errmsg' => '会员id不能为空'];
        }

        if (!class_exists($tlpMsgClass)) {
            return ['errcode' => 1002, 'errmsg' => '模板类不存在'];
        }

        if (empty($tlpMsgParams)) {
            return ['errcode' => 1002, 'errmsg' => '缺少模板参数'];
        }

        if (empty($corpId)) {
            return ['errcode' => 1002, 'errmsg' => '公司id不能为空'];
        }
        
        return true;
    }
    
    private function sendTlpMsg($memberId, $fans, $accountId, $tlpMsgClass, $tlpMsgParams)
    {
        $failure = [];
        $success = [];
        $tlpMsgInstance = $this->createInstance($tlpMsgClass, json_decode($tlpMsgParams, true));
        $wxTlpMsgProxy = $this->weixinApiService->getTemplateMsgProxy($accountId);
        $templateRepo = \Yii::$container->get('app\framework\weixin\interfaces\IMsgTemplateRepository', [$this->tenantCode, $accountId]);
        // 插入发送日志表
        $templateMsgLogRowData = [
            'template_id_short' => $tlpMsgInstance::TEMPLATE_NO,
            'account_id' => $accountId,
            'member_id' => $memberId,
            'url' => $tlpMsgInstance->url,
            'topcolor' => $tlpMsgInstance->topColor,
            'data' => json_encode($tlpMsgInstance->getData(), JSON_UNESCAPED_UNICODE),
            'status' => '发送中'
        ];
        $hasSendOpenids = [];
        foreach ($fans as $fan) {
            try {
                // 增强健壮性：发送模板消息时，如果由于数据原因导致openid相同，则不多发
                if (in_array($fan['openid'], $hasSendOpenids)) {
                    continue;
                }
                $templateMsgLogRowData['fan_id'] = $fan['id'];
                $templateMsgLogRowData['openid'] = $fan['openid'];
                $msgResult = $wxTlpMsgProxy->sendMsg($fan['openid'], $tlpMsgInstance, $templateRepo);
                $templateMsgLogRowData['msg_id'] = $msgResult->msgid;
                $success[] = ['openid' => $fan['openid'], 'msgid'=>$msgResult->msgid];
                $hasSendOpenids[] = $fan['openid'];
            } catch (\app\framework\weixin\WeixinException $ex) {
                $errCode = $ex->getCode();
                // 行业设置错误时自动进行修正，房产地|建筑 房产产｜物业
                if ($errCode == 40102 || $errCode == 45027) {
                    $wxTlpMsgProxy->setIndustry("29", "30");
                    // $fans[] = $fan; 不做自动重试处理，避免循环死
                }
                
                // 模板ID无效时自动修正 错误码:[40037] 消息:[invalid template_id
                if ($errCode == 40037) {
                    $templateRepo->deleteTemplate($tlpMsgInstance->shortId);
                }
                $templateMsgLogRowData['status'] = '非用户拒收失败';
                $failure[] = ['openid' => $fan['openid'], 'errcode' => 40013, 'errmsg' => '调用微信接口出错'];
                \Yii::error($ex);
            } catch (\Exception $ex) {
                $templateMsgLogRowData['status'] = '非用户拒收失败';
                $failure[] = ['openid' => $fan['openid'], 'errcode' => 40013, 'errmsg' => $ex->getMessage()];
                \Yii::error($ex);
            }
            
            $this->weixinApiService->insertTemplateMsgLog($templateMsgLogRowData);
        }
        
        return ['failure' => $failure, 'success' => $success];
    }
    
    /**
     * 获取临时素材
     * https://api.weixin.qq.com/cgi-bin/media/get?access_token=ACCESS_TOKEN&media_id=MEDIA_ID
     * @param type $account_id
     * @param type $media_id
     * @return type
     */
    public function actionGetMedia($account_id, $media_id)
    {
        try {
            $mch = \Yii::$container->get('app\modules\api\repositories\PublicAccountRepository')->getMch($account_id, ['original_id']);
            $orgAccountId = $mch['original_id'];
            $wxAccessTokenHelper = new AccessTokenHelper($orgAccountId, $this->accessTokenRepository);
            $media = new \app\framework\weixin\proxy\fw\Media($wxAccessTokenHelper);
            $result = $media->get($media_id);
            if (strpos(trim($result), '{') === 0) {
                $obj = json_decode($result);
                if (!is_null($obj) && $obj->errcode > 0) {
                    return $this->json(['errcode' => $obj->errcode, 'errmsg' => $obj->errmsg]);
                }
            }
            return $result;
        } catch (\Exception $ex) {
            return $this->json(['errcode' => 40013, 'errmsg' => $ex->getMessage()]);
        }
    }
}

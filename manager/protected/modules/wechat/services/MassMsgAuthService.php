<?php
namespace app\modules\wechat\services;
/**
 * @todo Description
 * @author fanwq
 */
use app\framework\weixin\msgtemplate\RepairAssign;
use app\framework\weixin\proxy\fw\TemplateMessage;
use app\modules\ServiceBase;
use app\modules\wechat\repositories\MassMsgAuthRepository;
use app\modules\wechat\repositories\MassMsgApproverRepository;
use app\entities\PMassMsgAuth;
use app\framework\utils\StringHelper;
use app\framework\weixin\AccessTokenHelper;
use app\framework\weixin\proxy\fw\CustomerMessage;
//use app\modules\wechat\services\WeixinHelperService;
use app\framework\weixin\msgtemplate\MassMsgApply;

class MassMsgAuthService extends ServiceBase
{
    private $_massMsgAuthRepository;
    private $_massMsgApproverRepository;
    
    public function __construct(MassMsgAuthRepository $massMsgAuthRepository, MassMsgApproverRepository $massMsgApproverRepository) {
        $this->_massMsgAuthRepository = $massMsgAuthRepository;
        $this->_massMsgApproverRepository = $massMsgApproverRepository;
       // $this->_weixinHelpService = new WeixinHelperService();
    }
    
    public function initAuth($data)
    {
        $authorizer = $this->getAuthAdmin($data['public_id']);
        if( empty($authorizer) ) {
            throw new \Exception('群发管理员未登录或未关注');
        }
        foreach ($authorizer as $value) {
            $pMassMsgAuth = new PMassMsgAuth();
            $pMassMsgAuth->id = StringHelper::uuid();
            $pMassMsgAuth->mass_msg_id = $data['mass_msg_id'];
            $pMassMsgAuth->applicant_user_id = $data['applicant_user_id'];
            $pMassMsgAuth->applicant_fan_id = $data['applicant_fan_id'];
            $pMassMsgAuth->authorizer_fan_id = $value['id'];
            $pMassMsgAuth->authorizer_member_id = $value['member_id'];
            $pMassMsgAuth->public_id =$data['public_id'];
            $pMassMsgAuth->status = '审批中';
            $this->_massMsgAuthRepository->insert($pMassMsgAuth);
        }
        
        return true;
    }
    
    public function getAuthAdmin($public_id)
    {
        return $this->_massMsgApproverRepository->getApproverByAccountID($public_id);
    }
    
    public function updateAuth($mass_msg_id, $status)
    {
        return $this->_massMsgAuthRepository->updateAuth($mass_msg_id, $status);
    }
    
    public function deleteAuth($mass_msg_id)
    {
        return $this->_massMsgAuthRepository->deleteAuth($mass_msg_id);
    }


    public function isOutOf30Min($mass_msg_id)
    {
        $auth_info = $this->_massMsgAuthRepository->getByMassMsgId($mass_msg_id);
        if( empty($auth_info) ) {
            return false;
        }
        
       return time() - strtotime($auth_info['created_on']) > 1800 ? true : false;
    }
    
    public function getAuthInfo($mass_msg_id)
    {
        return $this->_massMsgAuthRepository->getByMassMsgId($mass_msg_id);
    }
    
    public function hasAuthRecord($mass_msg_id)
    {
        $row = $this->_massMsgAuthRepository->getByMassMsgId($mass_msg_id);
        return $row['id'] ? true : false;
    }

    public function getMemberNames($mass_msg_id)
    {
        $member_id_arr = [];
        $member_list = $this->_massMsgAuthRepository->getAuthorizer($mass_msg_id);
        foreach($member_list as $value) {
            $member_id_arr[] = $value['authorizer_member_id'];
        }
        
    }

    public function getAccountName($public_id)
    {
        $info = $this->_massMsgAuthRepository->getAccount($public_id);
        return empty($info) ? '' : $info['name'];
    }
    
    public function getMassTitle($mass_msg_id)
    {
        return $this->_massMsgAuthRepository->getMassTitle($mass_msg_id);
    }
    
    public function getAccount($public_id)
    {
        return $this->_massMsgAuthRepository->getAccount($public_id);
    }   

    public function getMassMsgAuthById($id)
    {
        return $this->_massMsgAuthRepository->getMassMsgAuthById($id);
    }

    public function getMassMsgAuthByFanId($msgId, $fanId)
    {
        return $this->_massMsgAuthRepository->getMassMsgAuthByFanId($msgId, $fanId);
    }

    public function getMassMsgAuthByMsgId($msgId)
    {
        return $this->_massMsgAuthRepository->getMassMsgAuthByMsgId($msgId);
    }
    
    public function getUserCorpId($userId)
    {
        return $this->_massMsgAuthRepository->getUserCorpId($userId);
    }

    public function getWeixinProxy($accountId)
    {
        $originalId = $this->_massMsgAuthRepository->getWeChatOriginalId($accountId);
        $accessTokenRepository = \Yii::$container->get('app\framework\weixin\interfaces\IAccessTokenRepository');
        if (!isset($accessTokenRepository)) {
            throw new \Exception('未注入app\framework\weixin\interfaces\IAccessTokenRepository实例');
        }
        $accessTokenHelper = new AccessTokenHelper($originalId, $accessTokenRepository);
        $apiProxy = new CustomerMessage($accessTokenHelper);

        return $apiProxy;
    }    

    /**
     * 发送模板消息
     * @param type $memberId
     * @param RepairAssign $msgTemplate
     * @return type
     */
    public function sendTemplateMsg( $memberId, $msgTemplate, $public_id)
    {
        $users = $this->getToUsers($memberId, $public_id);
        // 插入日志:找不到用户失败
        if (count($users) == 0) {
            $templateMsgLogRowData = [
                'template_id_short' => $msgTemplate->shortId,
                'member_id' => $memberId,
                'status' => '未找到用户',
                'data' => json_encode($msgTemplate->getData(), JSON_UNESCAPED_UNICODE)
            ];
            $this->_massMsgAuthRepository->insertTemplateMsgLog($templateMsgLogRowData);
            return;
        }

        $accessTokenRepository = \Yii::$container->get('app\framework\weixin\interfaces\IAccessTokenRepository');
        $templateRepo = \Yii::$container->get('app\framework\weixin\interfaces\IMsgTemplateRepository', [$public_id]);
        $wxAccessTokenHelper= new AccessTokenHelper($public_id, $accessTokenRepository);
        $wxTemplateMsgSender = new TemplateMessage($wxAccessTokenHelper);
        
        // 发送模板消息
        foreach ($users as $u) {
            $accountId = $u['account_id'];
            $toUser = $u['openid'];
            $msgTemplate->url = $msgTemplate->url . "&public_id={$accountId}";

            //$msgId = $this->_weixinHelpService->sendTemplateMsg($accountId, $toUser, $msgTemplate);
            $result = $wxTemplateMsgSender->sendMsg($toUser, $msgTemplate, $templateRepo);

            // 插入发送日志表：发送中
            $templateMsgLogRowData = [
                'template_id_short' => MassMsgApply::TEMPLATE_NO,
                'account_id' => $accountId,
                'member_id' => $memberId,
                'fan_id' => $u['id'],
                'openid' => $toUser,
                'url' => $msgTemplate->url,
                'topcolor' => $msgTemplate->topColor,
                'data' => json_encode($msgTemplate->getData(), JSON_UNESCAPED_UNICODE),
                'msg_id' => $result->msgid,
                'status' => '发送中'
            ];
            
            $this->_massMsgAuthRepository->insertTemplateMsgLog($templateMsgLogRowData);
        }
    }

    /**
     * 获取发送的对象
     * @param type $memberId
     * @return type
     */
    private function getToUsers($memberId, $public_id)
    {
        $users = $this->_massMsgAuthRepository->getBindFollowedFans($memberId, $public_id, ['account_id','openid', 'id']);
        return $users;
    }    
    
    /**
     * 用户是否登陆，若未登陆，不可发微信消息
     * @param type $memberId
     * @return type
     */
    public function isMemberLogin($memberId)
    {
        return $this->_massMsgAuthRepository->isFanLogged($memberId);
    }
    
    
}

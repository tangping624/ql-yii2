<?php
namespace app\modules\wechat\services;
/**
 * @todo Description
 * @author fanwq
 */
use app\modules\ServiceBase;
use app\modules\wechat\repositories\MassMsgApproverRepository;
use app\framework\biz\cache\SiteCacheManager;
use app\framework\utils\WebUtility;
use app\framework\weixin\proxy\fw\Shorturl;
use app\framework\weixin\AccessTokenHelper;
use app\modules\wechat\repositories\MassMsgAuthRepository;
use app\framework\weixin\QrCodeLoginHelper;


class MassMsgApproverService extends ServiceBase
{
    private $_massMsgApproverRepository;
    private $_massMsgAuthRepository;
    
    public function __construct(MassMsgApproverRepository $massMsgApproverRepository, MassMsgAuthRepository $massMsgAuthRepository) {
        $this->_massMsgApproverRepository = $massMsgApproverRepository;
        $this->_massMsgAuthRepository = $massMsgAuthRepository;
    }
    
    /**
     * 检查是否添加 群发消息管理员
     * @param type $corp_id
     * @return boolean
     */
    public function hasApprover($corp_id)
    {
        $list = $this->_massMsgApproverRepository->getApproverByCondition(['corp_id'=>$corp_id, 'limit'=>1]);
        if( count($list) > 0 ) {
            return true;
        }
        return false;
    }
    
    /**
     * 生成二维码
     * @param type $text
     * @return type
     */
    public function genQrcode($text)
    {
        $qrcode = QrCodeLoginHelper::createOssQRCode($text);
        return $qrcode->url;
    }
    
    /**
     * 二维码
     * @param type $public_id
     * @param type $msg_id
     * @return type
     */
    public function genReturnQrCode($data)
    {
        $query = [
            'public_id'=>$data['public_id'], 
           // 'openid'=>$data['openid'],
            'mass_msg_id'=>$data['mass_msg_id'],
            'applicant_user_id'=>  $data['applicant_user_id'],
        ];
        $url = WebUtility::createBeautifiedUrl("wechat/v-mass-message/scan",$query); 
        
        try {
            $short = $this->getShortUrl($data['public_id'], $url);   
            $short_url = $short->short_url;
        } catch (\app\framework\weixin\WeixinException $e) {
            $short_url = $url;
        } catch (\Exception $e) {
            $short_url = $url;            
        }

        return $this->genQrcode($short_url);
    } 
    
    /**
     * 初始审核状态
     * @param type $entity
     * @return type
     */
    public function insert($entity)
    {
        return $this->_massMsgApproverRepository->insert($entity);
    }
    
    public function updateStatus($param, $id)
    {
        return $this->_massMsgApproverRepository->updateEntity(['status'=>$param['status']], $id);
    }
    
    /**
     * 通过openid 获取粉丝id
     * @param type $open_id
     * @return type
     */
    public function getFanId($condition)
    {
        $fan_info = $this->_massMsgApproverRepository->getFanInfo($condition);
        
        return $fan_info;
    }
    
    public function isAdmin($memberId, $accountId)
    {      
        $checkData = $this->checkApproverLogin($accountId, $memberId);
        if( !$checkData['approver'] ) {
            return false;
        }
        if( !$checkData['member'] ) {
            return false;
        }
        return true;
    }
    
    public function getFanInfo($condition)
    {
        return  $this->_massMsgApproverRepository->getFanInfo($condition);
    }
    
    public function getShortUrl($accountId, $url)
    {
        $originalId = $this->_massMsgAuthRepository->getWeChatOriginalId($accountId);
        $accessTokenRepository = \Yii::$container->get('app\framework\weixin\interfaces\IAccessTokenRepository');
        if (!isset($accessTokenRepository)) {
            throw new \Exception('未注入app\framework\weixin\interfaces\IAccessTokenRepository实例');
        }
        $accessTokenHelper = new AccessTokenHelper($originalId, $accessTokenRepository);
        $shortUrl = new Shorturl($accessTokenHelper);
        $result = $shortUrl->get($url);
        return $result;
    }        
    
    /**
     * 查看当前是否有群发管理员，当前用户是否是合法群发管理员
     * @param type $corpId
     * @param type $memberId
     * @return boolean
     */
    public function checkApproverLogin($accountId, $memberId=null)
    {
        $rows = $this->_massMsgApproverRepository->getApproverByAccountID($accountId);
        $data['approver'] = count($rows)<=0 ? false : true;
        $data['member'] = false;
        if( !$data['approver'] ) {
            return $data;
        }
        
        if( $memberId ) {
            foreach( $rows as $value) {
                if( $value['member_id']==$memberId ) {
                    $data['member'] = true;
                }
            }
        }
        return $data;
    }
}

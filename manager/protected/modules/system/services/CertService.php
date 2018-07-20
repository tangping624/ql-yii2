<?php 
namespace app\modules\system\services;

use app\modules\system\services\AccountService;
use app\modules\ServiceBase;
use yii\base\Security;

/**
 * Description of StatService
 *
 * @author Lvq
 */
class CertService extends ServiceBase
{
    /**
     * @var AccountService
     */
    private $_accountService;

    private $_security;

    public function __construct(AccountService $accountService, Security $security)
    {
        $this->_accountService = $accountService;
        $this->_security = $security;
    }

    //加密后数据写不进去
    public function insertContent($type, $content, $accountId)
    {
        $mchKey = $this->getMchKeyByAccountId($accountId);
        $entryKey = substr($mchKey, 0, 16);
        $content = $this->_security->encryptByKey($content, $entryKey);
        $content = base64_encode($content);
        return $this->_accountService->updateAccountInfo($accountId, $type, $content);
    }

    public function getMchKeyByAccountId($accountId)
    {
        $info = $this->_accountService->getAccountById($accountId);
        return isset($info['mch_key']) ? $info['mch_key'] : '';
    }
}

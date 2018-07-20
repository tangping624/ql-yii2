<?php
/**
 * 公众号管理逻辑
 * User: lvq
 * Date: 2015/5/5
 * Time: 16:32
 */
namespace app\modules\wechat\services;

use app\modules\wechat\repositories\AccountRepository;
use app\modules\ServiceBase;
use app\modules\api\services\BizCacheManager;
use Yii;

/**
 * Description of StatService
 *
 * @author Lvq
 */
class AccountService extends ServiceBase
{
    /**
     * @var AccountRepository
     */
    private $_accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->_accountRepository = $accountRepository;
    }
    
  
    
    /**
     * 获取公众号信息
     * @param string $id
     * @return array
     */
    public function getAccountInfo($id)
    {
        $row =  $this->_accountRepository->findAccountInfoById($id);
        return $row ?: [];
    }
    
    /**
     * 获取公众号信息
     * @param string $appId
     * @return array
     */
    public function getAccountInfoByAppId($appId)
    {
        $row =  $this->_accountRepository->findAccountInfoByAppId($appId);
        return $row ?: [];
    }
    
    /**
     * 保存授权公众号
     * @param string $id
     * @param string $appId
     * @param string $name
     * @param string $type
     * @param string $origianlId
     * @param string $wechatNumber
     * @param string $authorizerCode
     * @param string $authorizerFunc
     * @param string $authorizerRefreshToken
     * @param string $headimgUrl
     * @param string $qrcodeUrl
     * @param string $bindCorpId
     * @return string id
     * @throws \InvalidArgumentException
     */
    public function authAccount($id, $appId, $name, $type, $origianlId, $wechatNumber, $authorizerCode, $authorizerFunc, $authorizerRefreshToken, $headimgUrl, $qrcodeUrl, $bindCorpId)
    {
        // 校验
        if (empty($appId) || empty($origianlId) || empty($authorizerCode) || empty($authorizerRefreshToken) || empty($bindCorpId)) {
            throw new \InvalidArgumentException("参数值appId、origianlId、authorizerCode、authorizerRefreshToken、bindCorpId无效");
        }
        
        // 同一租户下授权校验
        $accountInfo = $this->getAccountInfoByAppId($appId);
        if ($accountInfo && $accountInfo["is_authed"] && $accountInfo["corp_id"] != $bindCorpId) {
            throw new \Exception("该公众号已授权给" . $accountInfo["corp_name"] . "，不能重复授权");
        }
                
        // 跨租户校验
        $tenantReader= \Yii::$container->get('app\framework\biz\tenant\TenantReaderInterface');
        $tenantCode = $tenantReader->getCurrentTenantCode();     
        $tenantMapping = $this->_accountRepository->getAccountMappingTenanantCode($appId);
        if ($tenantMapping && $tenantMapping["tenant_code"] != $tenantCode) {
            throw new \Exception("该公众号已授权给租户" . $tenantMapping["tenant_code"] . "，不能重复授权");
        }
        
        // 校验id
        $oldAccountInfo = null;
        if ($id) {
            $oldAccountInfo = $this->getAccountById($id);
        } else {
            $oldAccountInfo = $this->getAccountByCorpId($bindCorpId, false);//暂时不考虑集团已绑定则公司不能绑定的情况
            if (!empty($oldAccountInfo)) {
                $id = $oldAccountInfo['id'];
            }
        }

        $dataRow = ["id" => $oldAccountInfo ? $id :\app\framework\utils\StringHelper::uuid(),
                "app_id" => $appId,
                "name" => $name,
                "type" => $type,
                "original_id" => $origianlId,
                "wechat_number" => $wechatNumber,
                "authorizer_code" => $authorizerCode,
                "authorized_privilege_set" => $authorizerFunc,
                "authorizer_refresh_token" => $authorizerRefreshToken,
                "headimg_url" => $headimgUrl,
                "qrcode_url" => $qrcodeUrl,
                "auth_time" => date('Y-m-d H:i:s', time()),
                "is_authed" => 1,
                "corp_id" => $bindCorpId
        ];
        
        // 新增
        if (empty($id)) {
            $this->_accountRepository->insertWechatAccount($tenantCode, $dataRow);
            return $dataRow["id"];
        }
        
        // 修改        
        $this->_accountRepository->updateWechatAccount($tenantCode, $oldAccountInfo["app_id"], $dataRow);
        // 清除缓存
        BizCacheManager::clearAccountCache($id);
        return $dataRow["id"];
    }
    
    /**
     * 删除公众号
     * @param string $id
     * @return int
     */
    public function removeAccount($id)
    {
        // 清除缓存
        BizCacheManager::clearAccountCache($id);
        $affectedCount = $this->_accountRepository->remove($id);
        return $affectedCount;
    }

    /**
     * 保存公众号信息
     * @param object $account
     * @param array $appList
     * @throws HttpUnSignedException
     * @throws \Exception
     * @return bool
     */
    public function addAccount($account, $appList)
    {
        return $this->_accountRepository->insertAccount($account, $appList);
    }

    /**
     * 查询公众号信息
     * @param string $corpId
     * @param string $keyword
     * @throws \Exception
     * @return bool
     */
    public function queryAccountInfo($corpId, $keyword)
    {
        return $this->_accountRepository->queryAccountInfo($corpId, $keyword);
    }

    /**
     * 根据ID查询公众号信息
     * @param string $Id
     * @throws HttpUnSignedException
     * @throws \Exception
     * @return array
     */
    public function getAccountById($Id)
    {
        return $this->_accountRepository->getAccountById($Id);
    }

    /**
     * @param $Id
     * @param bool|true $getSuper
     * @return array|bool
     */
    public function getAccountByCorpId($Id, $getSuper = true)
    {
        $accounts = $this->_accountRepository->getAccountByCorpId($Id);
        //该公司未配置公众号，取集团对应的公众号列表
        if (count($accounts)<=0 && $getSuper) {
            $accounts = $this->_accountRepository->getAccountByCorpId(SUPER_ORGANIZATION_ID);
        }

        return $accounts;
    }

    public function getAllAccount()
    {
        return $this->_accountRepository->getAllAccount();
    }


    /**
     * 根据Id查询公众号对应应用
     * @param string $Id
     * @throws HttpUnSignedException
     * @throws \Exception
     * @return bool
     */
//    public function getAppsByAccountId($Id)
//    {
//        return $this->_accountRepository->getAppsByAccountId($Id);
//    }

    /**
     * 根据Id修改公众号信息
     * @param string $id
     * @param string $column
     * @param string $value
     * @return bool
     */
    public function updateAccountInfo($id, $column, $value)
    {
        return $this->_accountRepository->updateAccountInfo($id, $column, $value);
    }

    /**
     * 根据Id删除公众号信息
     * @param string $id
     * @return bool
     */
    public function removeAccountInfo($id)
    {
        return $this->_accountRepository->removeAccountInfo($id);
    }

    /**
     * 根据Id修改公众号对应应用
     * @param string $id
     * @param array $appList
     * @return bool
     */
    public function updateApps($id, $appList)
    {
        return $this->_accountRepository->updateApps($id, $appList);
    }

    /**
     * 获取功能入口数据
     * @return array|bool
     */
    public function getEntry($accountId)
    {
        return $this->_accountRepository->getEntry($accountId);
    }

    public function getAccountByOriginalId($accountId, $originalId)
    {
        return $this->_accountRepository->getAccountByOriginalId($accountId, $originalId);
    }
    
    public function getVSites(){
        
        $settingAccessor = Yii::$container->get('app\framework\settings\interfaces\SettingsAccessorInterface');
        $config = $settingAccessor->get('vshop_site');

        if (!isset($config)) {
            throw new \Exception('缺少配置项 vshop_site');
        }
        return $config;
    }
    /**
     * 获取所有微网站url
     * @return array
     */
    public function getMicroSites()
    {
        $rows = $this->_accountRepository->getMicroSites();
        $urlArr = [];
        foreach ($rows as $row) {
            $url = $row["url"];
            $pos = strpos($url,".");
            if ($pos) {
                $urlArr[] = substr($url,$pos+1);
            } else {
                $urlArr[] = str_replace("http://","",$url);
            }
        }
        return array_unique($urlArr);
    }
    
    /**
     * 获取公众号微信权限集合
     * @param string $accountId
     * @return array ID为1到15时分别代表：
        消息管理权限
        用户管理权限
        帐号服务权限
        网页服务权限
        微信小店权限
        微信多客服权限
        群发与通知权限
        微信卡券权限
        微信扫一扫权限
        微信连WIFI权限
        素材管理权限
        微信摇周边权限
        微信门店权限
        微信支付权限
        自定义菜单权限
     */
    public function getWechatAuthedPermission($accountId)
    {
        $repo = \Yii::$container->get('app\framework\weixin\interfaces\IAccessTokenRepository');
        $isAuthed = $repo->getConfigValue($accountId, "is_authed");
        $strPrivilegeSet = $repo->getConfigValue($accountId, "authorized_privilege_set");
        // 未授权或无任何权限
        if (!$isAuthed || !$strPrivilegeSet) {
            return [];
        }
        $privilegeSet = json_decode($strPrivilegeSet, true);
        $ids = [];
        foreach ($privilegeSet as $p) {
            $ids[] = $p['funcscope_category']['id'];
        }
                
        return $ids;
    }
    
    /**
     * 检查指定公众号是否具有指定的授权
     * @param string $accountId
     * @param int $permissionId 权限id,参考getWechatAuthedPermission
     * @return bool
     */
    public function checkWechatAuthed($accountId, $permissionId)
    {
        $ids = $this->getWechatAuthedPermission($accountId);
        return $ids ? in_array($permissionId, $ids) : false;
    }
}

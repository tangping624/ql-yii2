<?php

namespace app\framework\web\extension;

use yii\base\Component;
use yii\db\Connection; 
use app\framework\biz\cache\FanCacheManager;  
use yii\db\Query;
use app\framework\db\EntityBase;
use app\framework\utils\Security;

/**
 * 上下文对象
 * @property DbRoutingInterface $dbRouting dbRouting
 * @property Connection $tenantDb 租户db connection
 * @property string $openid This property is read-only.
 * @property string $fanId This property is read-only.
 * @property bool $isMember This property is read-only.
 * @property string $memberId This property is read-only.
 * @property string $tenantCode This property is read-only.
 * @property string $publicId 公众号id This property is read-only.
 * @property string $selectedCorpId 当前用户选择的城市公司id
 * @property bool $hasMemberCenter 当前租户是否有会员中心权限
 * @property array $appCodeList 租户的应用
 * @property array $user;
 */
class MobileContext extends Component
{
    protected static $_user = null;
    //protected static $_openid = null;
    protected static $_openid = "";
    protected static $_mobile = "";
    protected static $_public_id = null; 
    protected static $_tenantDb = null; 

    /**
     * 当前租户是否有会员中心权限
     * @var bool
     */
    protected static $_hasMemberCenter = null;

    /**
     * @var array 租户的应用
     */
    protected static $_appCodeList = null;

    /**
     * 当前租户是否有会员中心权限
     * @return bool
     * @throws \Exception
     */
    public function getHasMemberCenter()
    {
        static::$_hasMemberCenter = true;
        return true;
    }
 

    public function getUser()
    {
        /*if (defined('WEIXIN_AGENT') && WEIXIN_AGENT == false) {
            if (static::$_user == null) {
                //若为开发环境，模拟固定用户
                $db = EntityBase::getDb();
                $sql = "select id as memberId,name,headimg_url from h_member where id='4aa0d522-1b34-11e7-8de5-4ccc6a355efc'";
                $result = $db->createCommand($sql)->queryOne();
                static::$_user = [
                    'id' => $result['memberId'],
                    'mobile' => '13986211671',
                    'name' => '美国队长',
                    'headimgUrl' => $result['headimg_url'],
                    'pwd' => ''
                ];
            }
        }*/
        if (static::$_user == null) {
            //$openid = $this->getOpenid();
            \Yii::trace('get context->user, start query from cache');
            //$result = FanCacheManager::getFan();
            $mobile = self::getMobile();
            $pwd = md5(self::getPwd());
            $db = EntityBase::getDb();
            $params = [
                ':mobile' => $mobile,
                ':pwd' => $pwd
            ];
            $sql = "select id as memberId,name,headimg_url from h_member where mobile=:mobile and pwd=:pwd";
            $result = $db->createCommand($sql, $params)->queryOne();
            if ($result != null && isset($result)) {
                static::$_user = [
                    'id' => $result['memberId'],
                    //'fanId' => $result->fanId,
                    //'sex' => $result->sex,
                    //'nickName' => $result->nickName,
                    'mobile' => $mobile,
                    'name' => $result['name'],
                    'headimgUrl' => $result['headimg_url'],
                    'pwd' => $pwd
                ];
            }
        }
        return static::$_user;
    }

    private function getMobile()
    {
        if (static::$_mobile == "") {
            $cookie = \Yii::$app->request->cookies->get("u");
            if (isset($cookie)) {
                static::$_mobile = $cookie->value;
            }
        }
        return static::$_mobile;
    }

    private function getPwd()
    {
        $cookie = \Yii::$app->request->cookies->get("w");
        if (isset($cookie)) {
            return $cookie->value;
        }
        return "";
    }

    public function getOpenid()
    {
        /*if (static::$_openid == null) {
            $tReader = \Yii::$container->get('app\framework\biz\tenant\TenantReaderInterface');
            $openId = $tReader->getOpenId();
            static::$_openid = $openId;
        }
        return static::$_openid;*/
        return "";
    }

    public function getFanId()
    {
        /*
        $user = $this->getUser();
        return $user == null ? '' : $user['fanId'];*/
        return "";
    }

    public function getMemberId()
    {
        $user = $this->getUser();
        if(isset($user) && !empty($user)){
            return $user['id'];
        }
        return "";
    }
 


    public function getPublicId()
    {
        if(static::$_public_id == null){
            $tReader = \Yii::$container->get('app\framework\biz\tenant\TenantReaderInterface');
            static::$_public_id = $tReader->getPublicId();
        } 
        return static::$_public_id;
    }
 

    /**
     * @return Connection
     */
    public function getTenantDb()
    {
        if (static::$_tenantDb == null) { 
            static::$_tenantDb = EntityBase::getDb();
        } 
        return static::$_tenantDb;
    }


    /**
     * 是否会员
     * @return bool
     */
    public function getIsMember()
    {
        $result = $this->getUser();
        return $result == null ? false : (empty($result['id']) ? false : true);
    }
 
 

    /**
     * 当前url对应的自定义标题
     * @return bool
     */
    public function getCustomTitle()
    { 
        return "";
    }
}

<?php

namespace app\framework\auth;

use app\framework\auth\interfaces\AuthorizationInterface;
use app\framework\auth\interfaces\TokenAccessorInterface;
use app\framework\auth\interfaces\UserSessionAccessorInterface; 
use app\framework\settings\SettingsAccessor;
use app\framework\settings\SettingsProvider;
use app\framework\utils\Security;
use app\framework\utils\WebUtility;
use app\framework\utils\DateTimeHelper;
use soapclient;

class Authorization implements AuthorizationInterface
{
    private $_tokenAccessor;
    private $_userSessionAccessor;

    public function __construct(TokenAccessorInterface $tokenAccessor,
                                UserSessionAccessorInterface $userSessionAccessor)
    {
        $this->_tokenAccessor = $tokenAccessor;
        $this->_userSessionAccessor = $userSessionAccessor;
    }

    public function loginOther($openid, $type, $authinfo){
        if (empty($openid) || empty($type) ||empty($authinfo) ) {
            return LOGIN_STATUS_INVALID_USER;
        }
        $account = Store::getAccount($openid, $type);
        if (!isset($account)) {
            return ['status' => LOGIN_STATUS_NO_USER, 'session' => null, 'msg' => '不是系统用户'];
        }

        //$user = Store::getUserById($account->user_id);
        $user = Store::getMemberById($account->user_id);
       /* if (!isset($user['enabled']) || $user['enabled'] == 0) {
            return ['status' => LOGIN_STATUS_DISABLE_USER, 'session' => null, 'msg' => '用户被禁用'];
        }
        $expireTime = $user['expire_time'];
        if (!empty($expireTime) &&  strtotime($expireTime) < time()) {
            return ['status' => LOGIN_STATUS_INVALID_EXPIRED, 'session' => null, 'msg' => '用户已经失效'];
        }*/
        $account->modified_on=DateTimeHelper::now();
        $account->authinfo=$authinfo;
        Store::updateAccount($account);
        //set cookie
        $sessionId = sha1($user['id'] . time() . mt_rand(1, 9000));
        \Yii::trace('登录成功, sessionId: ' . $sessionId);
        //set session
        $session = new UserSession();
        $session->user_id = $user['id'];
        $session->mobile = $user['mobile'];
        $session->nickName = $user['nick_name'];
        $session->name= $user['name'];
        $session->userType = $user['user_type'];
        $session->headimg_url = $user['headimg_url'];
        $session->key = $sessionId;
        $this->_userSessionAccessor->updateSession($session);
        return ['status' => LOGIN_STATUS_SUCCESS, 'session' => $session, 'token' => $sessionId, 'msg' => '登录验证成功'];
    }
    
    public function login($account, $password, $from = 0)
    {
        if (empty($account) ) {
            return LOGIN_STATUS_INVALID_USER;
        } 
        $user = Store::getUser($account);
        //$user = Store::getMember($account);
        if (!isset($user)) {
            return ['status' => LOGIN_STATUS_INVALID_USER, 'session' => null, 'msg' => '帐号或密码错误'];
        }
        if (!isset($user['enabled']) || $user['enabled'] == 0) {
            return ['status' => LOGIN_STATUS_DISABLE_USER, 'session' => null, 'msg' => '用户被禁用'];
        }

        $expireTime = $user['expire_time'];
        if (!empty($expireTime) &&  strtotime($expireTime) < time()) {
            return ['status' => LOGIN_STATUS_INVALID_EXPIRED, 'session' => null, 'msg' => '用户已经失效'];
        }
          
            if (empty($password)) {
                return ['status' => LOGIN_STATUS_INVALID_USER, 'session' => null, 'msg' => '帐号或密码错误'];
            }
        $success = Security::validatePassword($password, $user['pwd']);
        if (!$success) {
            return ['status' => LOGIN_STATUS_INVALID_PASSWORD, 'session' => null, 'msg' => '帐号或密码错误'];
        }
          
        //set cookie
        $sessionId = sha1($account . time() . mt_rand(1, 9000));
        \Yii::trace('登录成功, sessionId: ' . $sessionId); 
        //set session
        $session = new UserSession();
        $session->user_id = $user['id'];
        $session->account = $account;
        $session->displayName = $user['name'];
        $session->key = $sessionId;  
        $this->_userSessionAccessor->updateSession($session);
        return ['status' => LOGIN_STATUS_SUCCESS, 'session' => $session, 'token' => $sessionId, 'msg' => '登录验证成功'];
      
    }

    //会员登入
    public function loginMember($account, $password, $from = 0)
    {
        if (empty($account) ) {
            return LOGIN_STATUS_INVALID_USER;
        }
        //$user = Store::getUser($account);
        $user = Store::getMember($account);
        if (!isset($user)) {
            return ['status' => LOGIN_STATUS_INVALID_USER, 'session' => null, 'msg' => '帐号或密码错误'];
        }
        /*if (!isset($user['enabled']) || $user['enabled'] == 0) {
            return ['status' => LOGIN_STATUS_DISABLE_USER, 'session' => null, 'msg' => '用户被禁用'];
        }*/

      /*  $expireTime = $user['expire_time'];
        if (!empty($expireTime) &&  strtotime($expireTime) < time()) {
            return ['status' => LOGIN_STATUS_INVALID_EXPIRED, 'session' => null, 'msg' => '用户已经失效'];
        }*/

        if (empty($password)) {
            return ['status' => LOGIN_STATUS_INVALID_USER, 'session' => null, 'msg' => '帐号或密码错误'];
        }
       // $success = Security::validatePassword($password, $user['pwd']);
        $success=(md5($password)==$user['pwd'])?1:0;
        if (!$success) {
            return ['status' => LOGIN_STATUS_INVALID_PASSWORD, 'session' => null, 'msg' => '帐号或密码错误'];
        }

        //set cookie
        $sessionId = sha1($account . time() . mt_rand(1, 9000));
        \Yii::trace('登录成功, sessionId: ' . $sessionId);
        //set session
        $session = new UserSession();
        $session->memberId = $user['id'];
        $session->mobile= $user['mobile'];
        $session->name=$user['name'];
        $session->headimg_url=$user['headimg_url'];
        $session->key = $sessionId;
        $this->_userSessionAccessor->updateSession($session);
        return ['status' => LOGIN_STATUS_SUCCESS, 'session' => $session, 'token' => $sessionId, 'msg' => '登录验证成功'];

    }
 


    public function logout() {
        //$cookie = \Yii::$app->request->cookies->get(\Yii::$app->session->name);
        $sid = $this->_tokenAccessor->getToken();
        \Yii::trace('读取sid: ' . $sid);
        

        $this->_userSessionAccessor->removeUserSession($sid);
        $this->_tokenAccessor->removeToken();
        \Yii::$app->session->destroy();
    }

    public function isAuthorized()
    {
        $paramAccessToken = isset($_GET['access_token']) ? $_GET['access_token'] : (isset($_POST['access_token']) ? $_POST['access_token'] : '');
        if (empty($paramAccessToken)) {
            //从cookie获取session
            $session = $this->_userSessionAccessor->getUserSession();
            if(!is_null($session)){
                return true;
            }else{
                return false;
            }

        } else {
            //url get或post access_token 获取session
            $paramAccessToken = trim($paramAccessToken);
            //检查本地session是否存在
            $sessionId = $this->_userSessionAccessor->sessionId(trim($paramAccessToken));
            $session = $this->_userSessionAccessor->getUserSession($sessionId);
            if(!is_null($session)){
                return true;
            }

           
        }

    }
 

    public function wxLoginByOpenid($openid)
    {
        $data = Store::getWechatUserByOpenid($openid);
        if( empty($data) ) {
            return false;
        }
        return $this->login($data['account'], '', $data['tenant_code'], 0, false, true);
    }

    public function qyhLogin($account, $tenantCode)
    {
        return $this->login($account, '', $tenantCode, 1, false, true);
    }

    public function wxBind($openid, $account, $tenantcode)
    {
        $bool = Store::insertWechatUser(['openid'=>$openid, 'account'=>$account, 'tenant_code'=>$tenantcode]);
        if( !$bool ) {
             return ['result'=>false, 'msg'=>'绑定用户失败'];
        }
        $data = Store::getWechatUserByOpenid($openid);
        if( empty($data) ) {
            return ['result'=>false, 'msg'=>'查询用户失败'];
        }
        return $this->login($account, '', $tenantcode, 0, false, true);
    }

    public function decodePassword($password) {

        $pass = base64_encode(pack("H*", $password));

        $settingsProvider = new SettingsProvider();
        $config = $settingsProvider->get("login_key");
        $config = json_decode($config["value"]);

        $privateKey = $config->private_key;
        $privateKey = base64_decode($privateKey);

        $fileName = 'pri.key';
        $dir = \yii::$app->runtimePath . '/temp/';
        !is_dir($dir) && mkdir($dir, 0755, true);
        $path = $dir . $fileName;

        $myfile = fopen($path, "w");

        fwrite($myfile, $privateKey);
        fclose($myfile);
        
//        $dir = __DIR__ . '/';
//        !is_dir($dir) && mkdir($dir, 0755, true);
//        $path = $dir . $fileName;

        $text = $this->privatekeyDecodeing($pass, $path, false);

        return $text;
    }

    /**
     * 私钥解密
     *
     * @param string $crypttext 密文（二进制格式且base64编码）
     * @param string $fileName 密钥文件（.pem / .key）
     * @param bool $fromjs 密文是否来源于JS的RSA加密
     * @return string 明文
     */
    public function privatekeyDecodeing($crypttext, $fileName, $fromjs = false)
    {
        $key_content = file_get_contents($fileName);
        $prikeyid = openssl_get_privatekey($key_content);
        $crypttext = base64_decode($crypttext);
        $padding = $fromjs ? OPENSSL_NO_PADDING : OPENSSL_PKCS1_PADDING;
        if (openssl_private_decrypt($crypttext, $sourcestr, $prikeyid, $padding)) {
            return $fromjs ? rtrim(strrev($sourcestr), "/0") : "" . $sourcestr;
        }
        return "";
    }

    /**
     * 公钥加密
     *
     * @param string 明文
     * @param string 证书文件（.crt）
     * @return string 密文（base64编码）
     */
    public function publickeyEncodeing($sourcestr, $fileName)
    {
        $key_content = file_get_contents($fileName);
        $pubkeyid = openssl_get_publickey($key_content);

        if (openssl_public_encrypt($sourcestr, $crypttext, $pubkeyid)) {
            return base64_encode("" . $crypttext);
        }
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: hwl
 * Date: 15-12-1
 * Time: 下午2:51
 */

namespace app\services;

class LoginLockerService
{
    //锁住半小时
    public $lockerTimeSec = 1800;
    //10次错误则锁住
    public $maxErrorToLock = 10;

    //多少次错误就显示验证码
    public $maxErrorShowCaptcha = 1;
   
    private $_account;
    private $_lockingKey;

    public function __construct( $account)
    {
        if (empty($account)) {
            throw new \InvalidArgumentException('无效的参数');
        }

        $this->_account = $account;
        //Note: 这个KEY的规则不能改, 管理中心重置密码会使用
        $this->_lockingKey = 'passport:login:lock:' . sha1("{$account}");
    }

    /**
     * @param int $loginStatusResult 登录状态码 app\framework\auth\interfaces\AuthorizationInterface
     * @return bool
     */
    public function lock($loginStatusResult)
    {
        if (in_array($loginStatusResult, [0x002, 0x003, 0x004])) {
            $this->increaseError();
        }
    }

    /**
     * @param $code
     * @return array [验证码是否通过, 是否显示验证码]
     */
    public function valid($code)
    {
        //自动化测试环境不验证
        if (in_array(YII_ENV, ['auto_test', 'auto_beta'])) {
            return [true, false];
        }
        if (\Yii::$app->cache->add($this->_lockingKey, 0, $this->lockerTimeSec)) {
            return [true, false];
        } else {
            $times = \Yii::$app->cache->get($this->_lockingKey);
            if ($times > $this->maxErrorShowCaptcha) {
                require_once('./securimage/securimage.php');
                $securimage = new \Securimage();
                $result = $securimage->check($code);
                \Yii::$app->session->set('captcha_show', 1);
                return [$result, true];
            } else {
                return [true, false];
            }
        }

    }

    /**
     * 是否显示验证码
     * @return bool
     */
    public static function needCaptchaCode()
    {
        return \Yii::$app->session->get('captcha_show') == 1;
    }

    /**
     * @return bool|int
     */
    public function checkLoginLocker()
    {
        if (\Yii::$app->cache->add($this->_lockingKey, 0, $this->lockerTimeSec)) {
            return true;
        } else {
            $times = \Yii::$app->cache->get($this->_lockingKey);
            if ($times >= $this->maxErrorToLock) {
                return false;
            } else {
                return $times;
            }
        }
    }

    public function increaseError()
    {
        if (\Yii::$app->cache->add($this->_lockingKey, 1, $this->lockerTimeSec)) {
            return;
        } else {
            $num = \Yii::$app->cache->get($this->_lockingKey);
            $num = $num == false ? 1 : $num;
            $num ++;
            \Yii::$app->cache->set($this->_lockingKey, $num, $this->lockerTimeSec);
        }
    }

    public function releaseLocker()
    {
        \Yii::$app->session->remove('captcha_show');
        \Yii::$app->cache->delete($this->_lockingKey);
    }
}

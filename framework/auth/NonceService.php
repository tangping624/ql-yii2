<?php
namespace app\framework\auth;


use app\framework\biz\cache\models\NonceCache;

class NonceService
{
    /**
     *  Constant for the parameter used in the return_to URL
     */
    const RETURN_TO_NONCE = 'sso.v1.0';
    const TIME_OUT_SECOND = 600;

    /**
     * Default clock skew, i.e. how long in the past we're willing to allow for.
     *
     * @var int
     * @see validate()
     */
    protected $clockSkew = 18000;

    /**
     * Validates the syntax of a nonce, as well as checks to see if its timestamp is
     * within the allowed clock skew
     *
     * @param string $nonce The nonce to validate
     *
     * @return bool true on success, false on failure
     * @see $clockSkew
     */
    public static function validate($nonce)
    {
        if (strlen($nonce) > 255) {
            return false;
        }

        $nc = NonceCache::getCache($nonce, 0);
        if (isset($nc)) {
            return true;
        }

        return false;

    }
    
    public static function createNonce($length, $startWithTime = true)
    {
        return static::_createNonce($length, $startWithTime);
    }

    /**
     * Creates a nonce, but does not store it.  You may specify the lenth of the
     * random string, as well as the time stamp to use.
     *
     * @param int $length Lenth of the random string, defaults to 6
     *
     * @return string The nonce
     * @see createNonceAndStore()
     */
    private static function _createNonce($length = 6, $startWithTime = true)
    {
        $time = time();
        $nonce = $startWithTime ? $time : "";
        if ($length < 1) {
            return $nonce;
        }

        $length = (int)$length;
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $chars .= 'abcdefghijklmnopqrstuvwxyz';
        $chars .= '1234567890';

        $unique = '';
        for ($i = 0; $i < $length; $i++) {
            $unique .= substr($chars, (rand() % (strlen($chars))), 1);
        }

        return $nonce . $unique;
    }

    /**
     * @param UserSession $session
     * @return NonceCache
     * @throws \app\framework\cache\CacheException
     */
    public static function createNonceAndStore($session)
    {
        $nonceId = static::_createNonce();

        $nc = new NonceCache($nonceId, 0);
        $nc->user_id = $session->user_id; 
        $nc->account = $session->account; 
        $nc->displayName = $session->displayName; 
        $nc->cache(static::TIME_OUT_SECOND);

        return $nc;
    }

    /*
     * @param MemberSession $session
     * */
    public static function createMemberNonceAndStore($session)
    {
        $nonceId = static::_createNonce();

        $nc = new NonceCache($nonceId, 0);
        $nc->member_id = $session->memberId;
        $nc->headimg_url = $session->headimg_url;
        $nc->mobile =  $session->mobile;
        $nc->name =  $session->name;
        $nc->cache(static::TIME_OUT_SECOND);

        return $nc;
    }

    /**
     * @param string $nonce id
     */
    public static function remove($nonce)
    {
        if (!empty($nonce)) {
            NonceCache::remove($nonce, 0);
        }
    }

    /**
     * @param string $nonce
     * @return NonceCache|null
     */
    public static function get($nonce)
    {
        if(empty($nonce)){
            throw new \InvalidArgumentException('$nonce');
        }
        return NonceCache::getCache($nonce, 0);
    }
}

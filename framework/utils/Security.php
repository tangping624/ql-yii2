<?php

namespace app\framework\utils;


use app\framework\settings\SettingsAccessor;

class Security
{
    public static function encryptByPassword($password, $cost = 13)
    {
        if (!function_exists('password_hash')) {
            throw new \Exception('Password hash key strategy "password_hash" requires PHP >= 5.5.0, either upgrade your environment or use another strategy.');
        }
        /** @noinspection PhpUndefinedConstantInspection */
        return password_hash($password, PASSWORD_DEFAULT, ['cost' => $cost]);
    }

    /**
     * @param string $password 明文密码
     * @param string $hash 存储的加密密码
     * @return bool
     * @throws \Exception
     */
    public static function validatePassword($password, $hash)
    {
        if (!is_string($password) || $password === '') {
            throw new \InvalidArgumentException('Password must be a string and cannot be empty.');
        }

        if (!function_exists('password_verify')) {
            throw new \Exception('Password hash key strategy "password_hash" requires PHP >= 5.5.0, either upgrade your environment or use another strategy.');
        }
        return password_verify($password, $hash);

    }

    /**
     * 产生一个随机密码
     * @param int $pwdLength 密码位数
     * @return string 密码
     */
    public static function genRandPassword($pwdLength)
    {
        $randpwd = '';
        for ($i = 0; $i < $pwdLength; $i++) {
            $randpwd .= chr(mt_rand(33, 126));
        }
        return $randpwd;
    }


    /**
     * 生成字符或数字的随机码
     * @param int $len 长度
     * @param string $format 格式
     * @return string
     */
    public static function randStr($len = 6, $format = 'NUMBER')
    {
        switch ($format) {
            case 'ALL':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~';
                break;
            case 'CHAR':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-@#~';
                break;
            case 'NUMBER':
                $chars = '0123456789';
                break;
            default :
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~';
                break;
        }
        mt_srand((double)microtime() * 1000000 * getmypid());
        $password = "";
        while (strlen($password) < $len) {
            $password .= substr($chars, (mt_rand() % strlen($chars)), 1);
        }
        return $password;
    }

    /**
     * 使用crypt加密字符串
     * @param string $source 明文
     * @param int $cost 强度
     * @param string $hashType 决定算法类型
     * 2y: CRYPT_BLOWFISH
     * 5: CRYPT_SHA256
     * @return string
     */
    public static function encrypt($source, $cost=11, $hashType='2y')
    {
        /* To generate the salt, first generate enough random bytes. Because
         * base64 returns one character for each 6 bits, the we should generate
         * at least 22*6/8=16.5 bytes, so we generate 17. Then we get the first
         * 22 base64 characters
         */
        $salt = substr(base64_encode(openssl_random_pseudo_bytes(17)), 0, 22);
        /* As blowfish takes a salt with the alphabet ./A-Za-z0-9 we have to
         * replace any '+' in the base64 string with '.'. We don't have to do
         * anything about the '=', as this only occurs when the b64 string is
         * padded, which is always after the first 22 characters.
         */
        $salt = str_replace("+", ".", $salt);
        /* Next, create a string that will be passed to crypt, containing all
         * of the settings, separated by dollar signs
         */
        $param = '$' . implode('$', array(
                $hashType,
                str_pad($cost, 2, "0", STR_PAD_LEFT), //add the cost in two digits
                $salt //add the salt
            ));

        //now do the actual hashing
        return  crypt($source, $param);
    }


    /**
     * 和encrypt对应，验证加密结果
     * @param string $clear 明文
     * @param string $hash 待验证密文
     * @return bool
     */
    public static function validate_crypt($clear, $hash)
    {
        /* Regenerating the with an available hash as the options parameter should
         * produce the same hash if the same password is passed.
         */
        return crypt($clear, $hash) == $hash;
    }

    /**
     * 获取签名
     * @param array $params 待校验的参数数组
     * @param string 签名key
     * @return string 获取签名
     */
    public static function getSign($params, $key)
    {
        $params["key"] = $key;

        $keyNames = array_keys($params);

        sort($keyNames);

        $sortedHash = [];

        foreach ($keyNames as $keyName) {
            array_push($sortedHash, "{$keyName}={$params[$keyName]}");
        }

        $rawString = join("&", $sortedHash);

        $encodeString = strtoupper(md5($rawString));

        return $encodeString;
    }

    /**
     * 获取积分变更签名
     * @param array $params 待校验的参数数组
     * @param string 签名key
     * @return string 获取签名
     */
    public static function getPointSign($params)
    {
        $settingsAccessor = new SettingsAccessor();
        $config = $settingsAccessor->get("sign_key");
        $config = json_decode($config);

        return self::getSign($params, $config->key);
    }
}

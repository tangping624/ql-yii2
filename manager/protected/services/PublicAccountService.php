<?php

namespace app\services;

use app\repositories\PublicAccountRepository;

class PublicAccountService
{

    private $_publicAccountRepository;

    public function __construct(PublicAccountRepository $publicAccountRepository)
    {
        $this->_publicAccountRepository = $publicAccountRepository;
    }

    /**
     * @param $tenantCode
     * @param $publicId
     * @param string $appEncrypt
     * @return array|bool|null
     * @throws \Exception
     */
    public function getAccount( $publicId, $appEncrypt = "")
    {
        if (empty($publicId) ) {
            return false;
        }

        $result = $this->_publicAccountRepository->getAccount( $publicId);
        if ($result == false) {
            return false;
        }

        if (empty($appEncrypt)) {
            return $result;
        } else {
            if (empty($result["mch_key"])) {
                throw new \Exception("公众号未配置商户密钥 " . ' public_id: ' . $publicId);
            }

            $appEncrypt = base64_decode($appEncrypt);
            $halfKey = substr($result['mch_key'], 0, 16);
            $appInfo = \Yii::$app->getSecurity()->decryptByPassword($appEncrypt, $halfKey);
            $appArr = explode(',', $appInfo);
            if (count($appArr) > 0) {
                $app_data = [
                    'app_id' => $appArr[0],
                    'app_secret' => $appArr[1],
                    'mch_id' => $appArr[2],
                    'mch_key' => $appArr[3],
                    'is_authed' => 0
                ];
                if ($appArr[4]) {
                    $app_data['sub_mch_id'] = $appArr[4];
                }

                return $app_data;
            }
        }
    }
}

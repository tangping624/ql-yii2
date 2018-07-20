<?php
namespace app\controllers;

use app\services\AppVersionService;
use app\modules\appapi\utils\WebUtils;
use Yii;
//use app\utils\WebUtils;

class AppversionController extends ControllerBase
{ 
    /**
     * 获取APP版本信息
     */
    public function actionGet()
    {
       if (!WebUtils::IsRequestParam('platform')) {
            return $this->json(['errcode' => INVALID_PARAMS, 'errmsg' => '未提供应用类型[platform]']);
        }

        $platform = $_REQUEST['platform']; 
        //$app_version = $_REQUEST['app_version'];

        $versionInfo = AppVersionService::getAppVersionInfo($platform);
        $downLoadUrl = AppVersionService::getAppDownloadUrl($platform);
        if (empty($downLoadUrl)) {
            $downLoadUrl = null;
        } 
        // 返回--update at 2015-12-04 IOS请求返回版本为空

        $result = [];
        $result['version_code'] = $versionInfo['version_code'];
        $result['update_info'] = $versionInfo['update_info'];
        $result['update_type'] = $versionInfo['update_type'];
        $result['download_url'] = $downLoadUrl;  
        return $this->json($result);
    }

    /**
     * 打开安装app页面
     */
    public function actionInstallApp()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        if (preg_match("/(iPod|iPad|iPhone)/", $userAgent)) {
            $iosUrl = AppVersionService::getAppDownloadUrl('iOS', APP_CODE);
            header("Location: $iosUrl");

        } elseif (preg_match("/android/i", $userAgent)) {
            $androidUrl = AppVersionService::getAppDownloadUrl('Android', APP_CODE);
            header("Location: $androidUrl");

        } else {
            echo '
                <!DOCTYPE html>
                <html lang="zh-cn">
                <head>
                    <meta charset="utf-8">
                </head>
                <body>
                仅支持iOS和Android
                </body>
                </html>';
        }
    }

    /**
     * 获取app数据库脚本升级数据
     * @return mixed
     */
    public function actionGetDbScript()
    { 
       if (!WebUtils::IsRequestParam('current_db_version_code')) {
            return $this->json(['errcode' => INVALID_PARAMS, 'errmsg' => '未提供当前数据库版本[current_db_version_code]']);
        }

        if (!WebUtils::IsRequestParam('target_db_version_code')) {
            return $this->json(['errcode' => INVALID_PARAMS, 'errmsg' => '未提供升级到目标版本的版本号[target_db_version_code]']);
        }
        $current_db_version_code = $_REQUEST['current_db_version_code'];
        $target_db_version_code = $_REQUEST['target_db_version_code']; 
        //获取app数据库脚本升级信息
        $result = AppVersionService::getAppDbScript( $current_db_version_code, $target_db_version_code);

        return $this->json($result);
    }
}

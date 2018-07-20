<?php
/**
 *调用oss 服务上传文件
 * @author : yangz03
 * @since  : 2014-10-09
 */
namespace app\framework\services;

require_once __DIR__ . '/../3rd/alioss/sdk.class.php';


use ALIOSS;
use Yii;
use app\framework\settings\SettingsAccessor;

require_once __DIR__ . '/../3rd/aliyun-oss-php-sdk-2.2.4/autoload.php';


class OssService
{

    private $oss;
    public $bucket;
    public $debug;
    public $rootPath;
    public $imgDomain;
    public $host;

    public function __construct()
    {
        //修改参数配置获取方式
        $settingsAccessor = new SettingsAccessor();
        $config = $settingsAccessor->get("oss_config");
        $config = json_decode($config);
        $this->bucket = $config->oss_bucket;
        $hostname = $config->oss_hostname; //定义操作的指定节点hostname
        $this->host = $hostname;
        $OSS_ACCESS_ID = $config->oss_accessid;
        $OSS_ACCESS_KEY = $config->oss_accesskey;
        $this->rootPath = isset($config->root) ? $config->root : '';
        $this->imgDomain = $config->img_domain;
        //$this->oss = new ALIOSS($OSS_ACCESS_ID, $OSS_ACCESS_KEY, $hostname);

        $this->oss = new \OSS\OssClient($OSS_ACCESS_ID, $OSS_ACCESS_KEY, $hostname);
    }


    //是否开启调试模式
    public function debug($debug = true)
    {
        $this->debug = $debug;
        return $this;
    }

    /**
     *上传逻辑
     */
    public function uploadFile($localFile, $remoteFile)
    {
        try{
            $response = $this->oss->uploadFile($this->bucket,$remoteFile,$localFile);
            \Yii::info('oss返回的原始信息为：'.'===>'. json_encode($response, JSON_UNESCAPED_UNICODE));
            if(!empty($response['info']) && isset($response['info'])){
                return $response['info']['url'];
            }else{
                return '';
            }
        }catch(\OSS\Core\OssException $e) {
            \Yii::error('oss返回了异常状态码:' . $e->getMessage() . '数据包:' . json_encode($response, JSON_UNESCAPED_UNICODE));
            return '';
        }

        /*
        $response = $this->oss->upload_file_by_file($this->bucket, $remoteFile, $localFile);
        if ($this->debug) {
            $this->_format($response);
        }
        if ($response->status == '200') {
            return $response->header['_info']['url'];
        }else {
            \Yii::error('oss返回了异常状态码:' . $response->status . '数据包:' . json_encode($response, JSON_UNESCAPED_UNICODE));
            return '';
        }*/

    }

    /**
     *格式化返回结果
     *
     **/
    private function _format($response)
    {
        echo '|-----------------------Start---------------------------------------------------------------------------------------------------' . "\n";
        echo '|-Status:' . $response->status . "\n";
        echo '|-Body:' . "\n";
        echo $response->body . "\n";
        echo "|-Header:\n";
        print_r($response->header);
        echo '-----------------------End-----------------------------------------------------------------------------------------------------' . "\n\n";
        exit;
    }


}

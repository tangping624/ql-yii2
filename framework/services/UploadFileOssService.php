<?php

namespace app\framework\services;

class UploadFileOssService
{
    /**
     * @var OssService
     */
    private static $ali_oss;
    protected static $forbidExtensions = ['php', 'exe', 'sh', 'js', 'html', 'css'];

    public function __construct()
    {
        if (!isset(static::$ali_oss)) {
            static::$ali_oss = new OssService();
        }
    }

    /**
     * @param $file 文件流
     * @param string $sub_folder 子文件夹
     * @param array $allows 允许的文件扩展名
     * @param string $rule_filename 规则化文件名
     * @return array
     */
    public function upload($file, $sub_folder = '', $allows = [], $rule_filename = '')
    {
        $filename = $file["name"];
        $tmp_name = $file['tmp_name'];

        if (!$this->allow($filename, $allows)) {
            return ['status' => 0, 'msg' => '文件类型不被允许!'];
        }

        $saveFilePath = $this->generate_filename($filename, $sub_folder, $rule_filename);

        $ossFilePath = static::$ali_oss->uploadFile($tmp_name, $saveFilePath);

        if (file_exists($tmp_name)) {
            unlink($tmp_name);
        }

        return ['status' => 1, 'file' => $ossFilePath, 'msg' => ''];
    }

    /**
     * 将oss的图片地址转成使用阿里图片服务的地址
     * @param string $originalImageUrl
     * 上传oss接口返回的原始图片地址
     * @return mixed
     */
    protected function convertToOSSImageServiceUrl($originalImageUrl)
    {
        if (empty($originalImageUrl)) {
            return $originalImageUrl;
        }

        $prefix = strtolower('http://' . static::$ali_oss->host . '/' . static::$ali_oss->bucket);
        $imageServiceUrl = strtolower('http://' . static::$ali_oss->imgDomain);
        $resultUrl = str_replace($prefix, $imageServiceUrl, $originalImageUrl);
        return $resultUrl;
    }

    protected function allow($filename, $allows)
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if (in_array($ext, static::$forbidExtensions)) {
            return false;
        }

        if (empty($allows)) {
            \Yii::warning('上传文件类型没有限制!');
            return true;
        }

        return in_array($ext, $allows);
    }

    protected function generate_filename($filename, $sub_folder, $rule_filename)
    {
        if (empty($filename)) {
            throw new \InvalidArgumentException('$filename');
        }

        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        $filename = time() . mt_rand(1, 1000) . '.' . $ext;
        if (!empty($rule_filename)) {
            $filename = $rule_filename . $filename;
        }

        $root = '';
        if (!empty(static::$ali_oss->rootPath)) {
            $root = static::$ali_oss->rootPath . '/';
        }

        $fullFileName = $root . \Yii::$app->name;// . '/' . $sub_folder . '/' . $filename;
        if (!empty($sub_folder)) {
            $fullFileName = $fullFileName . '/' . $sub_folder;
        }
        $fullFileName = $fullFileName . '/' . $filename;
        return strtolower($fullFileName);
    }
}

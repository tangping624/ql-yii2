<?php

namespace app\framework\services;

use Imagine\Image\Box;
use yii\imagine\Image;

use app\framework\utils\StringHelper;

/**
 * 上传图片，并保存缩略图
 */
class ImageService
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
     * 上传文件
     * @param $file 表单 $_FILES['filename'] ,
     * {"name":"bg.jpg","type":"image\/jpeg","tmp_name":"C:\\Windows\\Temp\\php8678.tmp","error":0,"size":11042}
     * @param string $sub_folder 默认是\Yii::$app->name前缀(不以/开头)
     * @param bool $thumbnail 是否生成缩略图
     * @param [with, height] $size 缩略图大小
     * @return array ['status'=>1, 'original'=>url, 'thumbnail'=>url2 , 'msg'=>'' ] status:0 表示失败
     */
    public function upload($file, $sub_folder = '', $thumbnail = false, $size = [])
    {
        if (!isset($file)) {
            throw new \InvalidArgumentException('$file');
        }

        if ($thumbnail) {
            if (!isset($size) || count($size) != 2) {
                throw new \InvalidArgumentException('缩略图必须指定大小');
            }
        }

        $filename = $file["name"];
        $tmp_name = $file['tmp_name'];
        if (!$this->allow($filename)) {
            return ['status' => 0, 'msg' => '文件类型不被允许!'];
        }

        $tmep_filename = pathinfo($tmp_name, PATHINFO_FILENAME);
        $temp_path = pathinfo($tmp_name, PATHINFO_DIRNAME);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if ($ext == "bmp") {
            copy($tmp_name, $tmp_name . ".bmp");
            $imgres = $this->imageCreateFromBmp($tmp_name . ".bmp");
            imagejpeg($imgres, $tmp_name, null);
            $ext = "jpg";

            if (file_exists($tmp_name . ".bmp")) {
                unlink($tmp_name . ".bmp");
            }
        }

        $save_name_thumb = $this->generateFilename($filename, $sub_folder);
        $save_name_original = $this->original($save_name_thumb);

        $thumbnail_filename = $tmep_filename . '_thumbnail';
        $thumbnail_file = $temp_path . DIRECTORY_SEPARATOR . $thumbnail_filename . '.' . $ext;
        $thumbnail_return = '';

        if ($thumbnail) {
            $imagine = new Image();
            \Yii::warning('$' . $tmp_name . '$');
            $imagine = $imagine->getImagine()->open($tmp_name)->thumbnail(new Box($size[0], $size[1]));
            $imagine->save($thumbnail_file);

            $thumbnail_return = static::$ali_oss->uploadFile($thumbnail_file, $save_name_thumb);

            if (file_exists($thumbnail_file)) {
                unlink($thumbnail_file);
            }
        }

        $original_return = static::$ali_oss->uploadFile($tmp_name, $save_name_original);
        //转换成oss的图片服务地址
        $original_return = $this->convertToOSSImageServiceUrl($original_return);
        $thumbnail_return = $this->convertToOSSImageServiceUrl($thumbnail_return);

        if (file_exists($tmp_name)) {
            unlink($tmp_name);
        }

        return ['status' => 1, 'original' => $original_return, 'thumbnail' => $thumbnail_return, 'msg' => ''];
    }

    /**
     * 根据缩略图路径得到原图路径
     * @param string $thumb_filename 缩略图
     * @return string
     */
    public function original($thumb_filename)
    {
        if (empty($thumb_filename)) {
            return null;
        }

        $ext = pathinfo($thumb_filename, PATHINFO_EXTENSION);
        $name = str_replace('.' . $ext, '', $thumb_filename);
        return $name . '_orig.' . $ext;
    }


    /**
     * 根据原图路径得到缩略图路径
     * @param $filename
     * @return mixed|null
     */
    public function thumbnail($filename)
    {
        if (empty($filename)) {
            return null;
        }

        return str_replace($filename, '_orig', '');
    }

    /**
     * 根据原图路径得到缩略图路径
     * @param $filename
     * @param int $width
     * @param int $height
     * @param int $quality
     * @return string
     */
    public static function getThumbnailPath($filename, $width = 100, $height = 100, $quality = 90)
    {
        $tmpIdx = strripos($filename, '.');
        return $filename . '@' . $width . 'w_' . $height . 'h_' . $quality . 'Q' . substr($filename, $tmpIdx);
    }

    /**
     * 将oss的图片地址转成使用阿里图片服务的地址
     * @param string $originalImageUrl
     * 上传oss接口返回的原始图片地址
     * @return mixed
     */
    private function convertToOSSImageServiceUrl($originalImageUrl)
    {
        if (empty($originalImageUrl)) {
            return $originalImageUrl;
        }

        $prefix = strtolower('http://' . static::$ali_oss->host . '/' . static::$ali_oss->bucket);
        $imageServiceUrl = strtolower('http://' . static::$ali_oss->imgDomain);
        $resultUrl = str_replace($prefix, $imageServiceUrl, $originalImageUrl);
        return $resultUrl;
    }

    private function allow($filename)
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $ext = strtolower($ext);
        if (in_array($ext, static::$forbidExtensions)) {
            return false;
        }

        $allow = \Yii::$app->params['allow_file'];
        if (empty($allow)) {
            \Yii::warning('上传文件类型没有限制!');
            return true;
        }


        return in_array($ext, $allow);
    }

    private function generateFilename($filename, $sub_folder)
    {
        if (empty($filename)) {
            throw new \InvalidArgumentException('$filename');
        }

        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        $filename = StringHelper::uuid() . '.' . $ext;
        $root = '';
        if (!empty(static::$ali_oss->rootPath)) {
            $root = static::$ali_oss->rootPath . '/';
        }
        $filename = $root . \Yii::$app->name . '/' . $sub_folder . '/' . $filename;
        return strtolower($filename);

    }

    /**
     * BMP 创建函数
     * @param string $filename path of bmp file
     * @return resource of GD
     */
    public function imageCreateFromBmp($filename)
    {
        if (!$f1 = fopen($filename, "rb")) {
            return false;
        }

        $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1, 14));
        if ($FILE['file_type'] != 19778) {
            return false;
        }

        $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel' . '/Vcompression/Vsize_bitmap/Vhoriz_resolution' . '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1, 40));
        $BMP['colors'] = pow(2, $BMP['bits_per_pixel']);
        if ($BMP['size_bitmap'] == 0) {
            $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
        }
        $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel'] / 8;
        $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
        $BMP['decal'] = ($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
        $BMP['decal'] -= floor($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
        $BMP['decal'] = 4 - (4 * $BMP['decal']);
        if ($BMP['decal'] == 4) {
            $BMP['decal'] = 0;
        }
        $PALETTE = [];

        if ($BMP['colors'] < 16777216) {
            $PALETTE = unpack('V' . $BMP['colors'], fread($f1, $BMP['colors'] * 4));
        }

        $IMG = fread($f1, $BMP['size_bitmap']);
        $VIDE = chr(0);

        $res = imagecreatetruecolor($BMP['width'], $BMP['height']);
        $P = 0;
        $Y = $BMP['height'] - 1;
        while ($Y >= 0) {
            $X = 0;
            while ($X < $BMP['width']) {
                if ($BMP['bits_per_pixel'] == 32) {
                    $COLOR = unpack("V", substr($IMG, $P, 4));
                    $B = ord(substr($IMG, $P, 1));
                    $G = ord(substr($IMG, $P + 1, 1));
                    $R = ord(substr($IMG, $P + 2, 1));
                    $color = imagecolorexact($res, $R, $G, $B);
                    if ($color == -1) {
                        $color = imagecolorallocate($res, $R, $G, $B);
                    }
                    $COLOR[0] = $R * 256 * 256 + $G * 256 + $B;
                    $COLOR[1] = $color;
                } elseif ($BMP['bits_per_pixel'] == 24)
                    $COLOR = unpack("V", substr($IMG, $P, 3) . $VIDE);
                elseif ($BMP['bits_per_pixel'] == 16) {
                    $COLOR = unpack("n", substr($IMG, $P, 2));
                    $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                } elseif ($BMP['bits_per_pixel'] == 8) {
                    $COLOR = unpack("n", $VIDE . substr($IMG, $P, 1));
                    $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                } elseif ($BMP['bits_per_pixel'] == 4) {
                    $COLOR = unpack("n", $VIDE . substr($IMG, floor($P), 1));
                    if (($P * 2) % 2 == 0) {
                        $COLOR[1] = ($COLOR[1] >> 4);
                    } else {
                        $COLOR[1] = ($COLOR[1] & 0x0F);
                    }
                    $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                } elseif ($BMP['bits_per_pixel'] == 1) {
                    $COLOR = unpack("n", $VIDE . substr($IMG, floor($P), 1));
                    if (($P * 8) % 8 == 0) {
                        $COLOR[1] = $COLOR[1] >> 7;
                    } elseif (($P * 8) % 8 == 1) {
                        $COLOR[1] = ($COLOR[1] & 0x40) >> 6;
                    } elseif (($P * 8) % 8 == 2) {
                        $COLOR[1] = ($COLOR[1] & 0x20) >> 5;
                    } elseif (($P * 8) % 8 == 3)
                        $COLOR[1] = ($COLOR[1] & 0x10) >> 4;
                    elseif (($P * 8) % 8 == 4)
                        $COLOR[1] = ($COLOR[1] & 0x8) >> 3;
                    elseif (($P * 8) % 8 == 5)
                        $COLOR[1] = ($COLOR[1] & 0x4) >> 2;
                    elseif (($P * 8) % 8 == 6)
                        $COLOR[1] = ($COLOR[1] & 0x2) >> 1;
                    elseif (($P * 8) % 8 == 7)
                        $COLOR[1] = ($COLOR[1] & 0x1);
                    $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                } else {
                    return false;
                }
                imagesetpixel($res, $X, $Y, $COLOR[1]);
                $X++;
                $P += $BMP['bytes_per_pixel'];
            }
            $Y--;
            $P += $BMP['decal'];
        }
        fclose($f1);

        return $res;
    }
}

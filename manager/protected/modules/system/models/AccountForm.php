<?php
 namespace app\modules\system\models;
use app\framework\web\extension\FormBase;  
use app\entities\PAccount;
class AccountForm  extends FormBase {  
     /*
     * 名称
     */
    public $name;
     /*
     * 原始id
     */
    public $original_id; 
      /*
     * 微信号
     */
    public $wechat_number; 
      /*
     * 服务号
     */
    public $type; 
     /*
     * 套餐类型
     */
    public $package_type; 
    /*
     * AppId
     */
    public $app_id; 
      /*
     * AppSecret
     */
    public $app_secret; 
      /*
     * 商户id
     */
    public $mch_id; 
      /*
     * 商户密钥
     */
    public $mch_key; 
      /*
     * API支付证书
     */
    public $mch_ssl_cert; 
      /*
     * API支付密钥
     */
    public $mch_ssl_key; 
     /*
     * 头像
     */
    public $headimg_url; 
     /*
     * 二维码
     */
    public $qrcode_url; 
    /**
     * 转换成对应实体
     * @return app\entities\s_gb_items
     */
    public function convertToEntity(PAccount $entity = NULL) {
        if (is_null($entity)) {
            $entity = new PAccount(); 
        } 
        $this->assignAttributes($entity);
        return $entity;
    }
     protected function assignAttributes(PAccount $entity) {
        $attrs = $this->getAttributes();
        $cols = PAccount::getTableSchema()->columnNames;
        foreach ($attrs as $key => $value) {
            foreach ($cols as $col) {
                if (strtolower($key) == strtolower($col)) {
                    $entity->$col = $value;
                }
            }
        }
    }
} 
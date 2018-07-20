<?php 
namespace app\framework\auth; 
class PublicAccountSession {
     public $key;

    /**
     * @var string 公众号Id 
     */
    public $account_id;

    /**
     * @var string 公众号名称
     */
    public $name;

    /**
     * @var string 公众号原始ID
     */
    public $originalId; 
    /**
     * @var array string 公众号编码
     */
    public $wechatNumber; 
    
    /**
     * app_id
     */
    public $appId; 
    /**
     * @var $app_secret
     */
    public $appSecret;
      /**
     * @var $$token
     */
    public $token;
    /**
     * 套餐类型
     * @var type 
     */
    public $package_type;
    
     

}

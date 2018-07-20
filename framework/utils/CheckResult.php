<?php
namespace app\framework\utils;

class CheckResult {
   //是否成功
   private $_issucess;  
   //错误提示
   private $_msg; 
   public function __construct($issucess=true,$msg='') {
      $this->_issucess=$issucess ;
       $this->_msg=$msg ;
   }
   public function setIsSuccess($val){
       $this->_issucess=$val;
   }
   public function setMsg($val){
        $this->_msg=$val;
   }
   
    public function getIsSuccess(){
      return $this->_issucess;
   }
   public function getMsg(){
       return $this->_msg;
   }
}

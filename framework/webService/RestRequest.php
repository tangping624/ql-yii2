<?php

namespace app\framework\webService;


/**
 * Description of RestRequest
 *
 * @author likg
 */
class RestRequest {
    
    
     private $curl_opts;
     private $method;
     private $params=array();
     private $urlParam="";
     private $httpHeader=array();
     private $acceptXmlType="Accept:application/xml";
     private $acceptJsonType="Accept:application/json";
     
     public  function __construct($method)
    {
        $this->method=$method;
        $this->curl_opts = array(
			CURLOPT_CONNECTTIMEOUT	=> 60,   /* 在发起连接前等待的时间，如果设置0，则无限等待 */
			CURLOPT_TIMEOUT			=> 300,  /* 允许执行的最长秒 */
			CURLOPT_USERAGENT		=> 'MysoftRequester',
                        CURLOPT_HTTP_VERSION	=> CURL_HTTP_VERSION_1_1,
                        CURLOPT_RETURNTRANSFER	=> true,
                        CURLOPT_HEADER			=> false,
                        CURLOPT_FOLLOWLOCATION	=> false,
                        CURLOPT_SSL_VERIFYPEER  => false,
                        CURLOPT_SSL_VERIFYHOST  => false
                        );  
    }
    
     public function SetParameters($params)
    {
        $this->params=$params;
    }
    
    public function SetAuthorization($accessToken)
    {
       $this->httpHeader[0]="Authorization:Bearer ".$accessToken;
    } 
    
    public function SetAcceptType($acceptType)
    {
        if($acceptType=="xml")
        {
            $this->httpHeader[1]=$this->acceptXmlType;
        }
        else
        {             
             $this->httpHeader[1]=$this->acceptJsonType;
        }
    }
    
    public function GetUrlParams()
    {
        return $this->urlParam;
    } 
    
    public function GetSeting()
    { 
        if($this->method=="POST")
        {
            // post数据
          $this->curl_opts[CURLOPT_POST]=true;
          if(is_string($this->params)){
              $this->curl_opts[CURLOPT_POSTFIELDS]=$this->params;
          }  else {
              $this->curl_opts[CURLOPT_POSTFIELDS]=  json_encode($this->params, JSON_UNESCAPED_UNICODE); 
          }
        }
        else if($this->method=="PUT")
        {
             // put数据  
            $fields = is_array($this->params) ? http_build_query($this->params) : $this->params; 
            $this->curl_opts[CURLOPT_CUSTOMREQUEST]='PUT';
            $this->curl_opts[CURLOPT_RETURNTRANSFER]=true;
            $this->curl_opts[CURLOPT_POSTFIELDS]=$fields;   
        }
       else {
            $urlParam="";
            if($this->params!=null)
            {
                foreach ($this->params as $key => $value) {
                    $urlParam=$urlParam."$key=" . urlencode($value) ."&";
                }
            }
            $this->urlParam=$urlParam;
        } 
        
         $this->curl_opts[CURLOPT_HTTPHEADER]=$this->httpHeader;
         
        return  $this->curl_opts;
    } 
}

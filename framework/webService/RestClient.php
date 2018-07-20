<?php

namespace app\framework\webService;

require_once(dirname(__FILE__) . '/RestRequest.php'); 
use \app\framework\webService\Exceptions;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RestClient
 *
 * @author likg
 */
class RestClient
{

    private $baseURL;
    private static $boundary = '';
    private static $errno = 0;
    private static $errmsg = '';
    private static $isDebug = true;

    public function __construct($baseURL)
    {
        $this->baseURL = $baseURL;
    }


    /**
     * @param string $resource
     * @param RestRequest $request
     * @param array $headers
     * @return mixed
     * @throws Exceptions\BadRequestException
     * @throws Exceptions\ConnectException
     * @throws Exceptions\InterfaceInternalErrorException
     * @throws Exceptions\NotFoundException
     * @throws Exceptions\NotImplementedException
     * @throws Exceptions\UnauthorizedAccessException
     */
    public function Excute($resource, $request, $headers=[])
    {
        // when using bae(baidu app engine) to deploy the application,
        // just comment the following line
        $ch = curl_init();
        curl_setopt_array($ch, $request->GetSeting());
        if(!empty($headers)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $urlParams = $request->GetUrlParams();
        if (strpos($this->baseURL . $resource, '?') > 0) {
            $requestUrl = $this->baseURL . $resource . "&" . $urlParams;
        } else {
            $requestUrl = $this->baseURL . $resource . "?" . $urlParams;
        }

        curl_setopt($ch, CURLOPT_URL, $requestUrl);

        $result = curl_exec($ch);
        $code = curl_errno($ch);
        if ($code != 0) {
            $msg = curl_error($ch) .  " request url is({$code}):" . $requestUrl;
            throw new Exceptions\ConnectException($msg, $code);
        }

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($http_code != 200) {
            switch ($http_code) {
                case 500:
                    throw new Exceptions\InterfaceInternalErrorException();
                case 400:
                    throw new Exceptions\BadRequestException($requestUrl);
                case 401:
                    throw new Exceptions\UnauthorizedAccessException($requestUrl);
                case 404:
                    throw new Exceptions\NotFoundException($requestUrl);
                case 501:
                    throw new Exceptions\NotImplementedException($requestUrl);
            }
        }

        return $result;
    }
}

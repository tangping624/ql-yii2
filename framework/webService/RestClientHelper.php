<?php

namespace app\framework\webService;

class RestClientHelper
{
    protected $restClient;
    /**
     * @var array
     */
    private $_headerDict;
    
    public function __construct($url = '')
    {
        $this->restClient = new RestClient($url);
        $this->_headerDict = [];
    }

    /**
     * @param $path
     * @param $params
     * @param string $method
     * @param bool|true $jsonParse 是否返回对象
     * @return string | object
     * @throws Exceptions\BadRequestException
     * @throws Exceptions\ConnectException
     * @throws Exceptions\InterfaceInternalErrorException
     * @throws Exceptions\NotFoundException
     * @throws Exceptions\NotImplementedException
     * @throws Exceptions\UnauthorizedAccessException
     */
    public function invoke($path, $params, $method = 'GET', $jsonParse = true)
    {
        $request = new RestRequest($method);
        $request->SetAcceptType('json');
        $request->SetParameters($params);
        $content = $this->restClient->Excute($path, $request, $this->_headerDict);
        
        return $jsonParse ? json_decode($content) : $content;
    }

    /**
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        if (empty($headers)) {
            return;
        }

        foreach ($headers as $k => $v) {
            $this->_headerDict[$k] = $v;
        }
    }
}




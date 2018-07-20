<?php

namespace app\framework\webService;

require_once(__DIR__ . '/../3rd/nusoap/lib/nusoap.php');


class WebServiceHelper
{
    protected $client;
    
    public function __construct($url)
    {
        $this->client = new \nusoap_client($url, 'wsdl');
        $this->client->soap_defencoding = 'utf-8';
        $this->client->decode_utf8 = false;
        $this->client->xml_encoding = 'utf-8';
    }
    
    public function invoke($method, $params)
    {
        $client = $this->client;
        $result = $client->call($method, array('parameters' => $params), '', '', false, true);
        if ($client->fault)
        {
            return false;
        //    echo '<h2>Fault</h2><pre>';
        //    print_r($result);
        //    echo '</pre>';
        }
        //else {
        //    // Check for errors
        //    $err = $client->getError();
        //    if ($err) {
        //        // Display the error
        //        echo '<h2>Error</h2><pre>' . $err . '</pre>';
        //    } else {
        //        // Display the result
        //        echo '<h2>Result</h2><pre>';
        //        print_r($result);
        //        echo '</pre>';
        //    }
        //}
        return $result;
    }
    
}

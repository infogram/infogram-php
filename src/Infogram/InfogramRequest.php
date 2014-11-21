<?php

namespace Infogram;

/**
* Class InfogramRequest
*/
class InfogramRequest extends SimpleRequest
{
    const VERSION = '0.1.0';
    const DEFAULT_BASE_URL = 'https://infogr.am/service/v1/';

    private $session;

    private $version;

    private $transport;
    
    public function __construct(InfogramSession $session, $method, $path, $parameters = null, $baseUrl = null, $version = null, $transport = null)
    {
        $base = isset($baseUrl) ? $baseUrl : self::DEFAULT_BASE_URL;
        parent::__construct($method, $base . $path, isset($parameters) ? $parameters : array());
        $this->session = $session;
        $this->version = isset($version) ? $version : self::VERSION;
        $this->transport = isset($transport) ? $transport : new HttpTransport();
    }

    public function execute()
    {
        $this->session->passThrough($this);
        $rawResponse = $this->transport->send($this);
        return new InfogramResponse($rawResponse);
    }

    public function getVersion()
    {
        return $this->version;
    }
}

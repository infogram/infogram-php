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
        $convertedParams = isset($parameters) ? self::convertCompoundParametersToStringIfNeeded($parameters) : array();
        parent::__construct($method, $base . $path, $convertedParams);
        $this->session = $session;
        $this->version = isset($version) ? $version : self::VERSION;
        $this->transport = isset($transport) ? $transport : new HttpTransport();
    }

    public function execute()
    {
        $this->session->passThrough($this);
        $rawResponse = $this->transport->send($this);
        if ($rawResponse == null) {
            return null;
        }
        return new InfogramResponse($rawResponse);
    }

    public function getVersion()
    {
        return $this->version;
    }

    private static function convertCompoundParametersToStringIfNeeded($params)
    {
        $arr = array();
        foreach ($params as $name => $value) {
            if (is_scalar($value)) {
                $arr[$name] = $value;
            }
            else {
                if (is_array($value) || is_object($value)) {
                    $arr[$name] = json_encode($value);
                }
                else {
                    throw new \ErrorException('Array contains a non-serializable value with name "' . $name . '"');
                }
            }
        }
        return $arr;
    }
}

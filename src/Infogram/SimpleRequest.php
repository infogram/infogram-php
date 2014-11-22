<?php

namespace Infogram;

class SimpleRequest implements Request
{
    private $method;

    private $url;

    private $parameters;

    private $version;

    public function __construct($method, $url, $params) {
        $this->method = $method;
        $this->url = $url;
        $this->parameters = $params;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function setParameter($name, $value) {
        $this->parameters[$name] = $value;
    }
}

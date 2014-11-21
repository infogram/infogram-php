<?php

namespace Infogram;

class InfogramResponse extends SimpleResponse
{
    private $ok;
    
    public function __construct(Response $parent)
    {
        parent::__construct($parent->getBody(), $parent->getHeaders(), $parent->getStatus());
        $status = $this->getStatus();
        $this->ok = $status >= 200 && $status < 300;
    }
    
    function isOK()
    {
        return $this->ok;
    }
}
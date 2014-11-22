<?php

namespace Infogram;

class InfogramResponse extends SimpleResponse
{
    private $ok;

    public function __construct(Response $parent)
    {
        $status = $parent->getStatus();
        $ok = self::statusIsOK($status);
        $bodyString = $parent->getBody();
        parent::__construct($ok ? json_decode($bodyString) : $bodyString, $parent->getHeaders(), $status);
        $this->ok = $ok;
    }

    function isOK()
    {
        return $this->ok;
    }

    private static function statusIsOK($status)
    {
        return $status >= 200 && $status < 300;
    }
}

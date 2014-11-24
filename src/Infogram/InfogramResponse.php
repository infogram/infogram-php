<?php

namespace Infogram;

class InfogramResponse extends SimpleResponse
{
    private $ok;

    public function __construct(Response $parent)
    {
        $status = $parent->getStatus();
        $convertToJson = false;
        $ok = self::statusIsOK($status);
        if ($ok) {
            $contentType = $parent->getHeader('Content-Type');
            if (!empty($contentType)) {
                $convertToJson = stristr($contentType, '/json') !== false;
            }
        }
        $bodyString = $parent->getBody();
        parent::__construct($convertToJson ? json_decode($bodyString) : $bodyString, $parent->getHeaders(), $status);
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

<?php

namespace Infogram;

class SimpleResponse implements Response
{

    public function __construct($body, $headers, $status = 200)
    {
        $this->body = $body;
        $this->headers = self::convertKeyNamesToLowerCase($headers);
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getHeader($name)
    {
        $key = strtolower($name);
        if (array_key_exists($key, $this->headers)) {
            return $this->headers[$key];
        }
        return null;
    }

    public static function convertKeyNamesToLowerCase(&$arr)
    {
        $ret = array();
        foreach ($arr as $key => $value) {
            $ret[strtolower($key)] = $value;
        }
        return $ret;
    }
}

<?php

namespace Infogram;

class DefaultCurl implements Curl
{
    private $ch;

    public function init()
    {
        $this->ch = curl_init();
        return $this->ch !== false;
    }

    public function close()
    {
        if ($this->ch) {
            curl_close($this->ch);
            $this->ch = null;
        }
    }

    public function exec()
    {
        return curl_exec($this->ch);
    }

    public function escape($input)
    {
        return curl_escape($this->ch, $input);
    }

    public function setOption($name, $value)
    {
        curl_setopt($this->ch, $name, $value);
    }

    public function getInfo($option)
    {
        return curl_getinfo($this->ch, $option);
    }

    public function getError()
    {
        return curl_error($this->ch);
    }
}
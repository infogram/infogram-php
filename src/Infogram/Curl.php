<?php

namespace Infogram;

interface Curl
{
    public function init();

    public function escape($input);

    public function setOption($name, $value);

    public function getError();

    public function getInfo($option);

    public function close();

    public function exec();
}
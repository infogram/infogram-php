<?php

namespace Infogram;

interface Response
{
    public function getStatus();

    public function getBody();

    public function getHeaders();

    public function getHeader($name);
}

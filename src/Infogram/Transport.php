<?php

namespace Infogram;

interface Transport
{
    public function send(Request $request);
}

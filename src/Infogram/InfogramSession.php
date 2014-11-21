<?php

namespace Infogram;

interface InfogramSession
{
    public function passThrough(Request $request);
}

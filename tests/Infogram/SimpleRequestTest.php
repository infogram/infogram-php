<?php

use Infogram\SimpleResponse;

class SimpleRequestTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct_shouldNormalizeHeaderNameCase()
    {
        $response = new SimpleResponse('blah', array('Content-Type' => 'application/json'));

        $this->assertEquals('application/json', $response->getHeader('Content-Type'));
        $this->assertEquals('application/json', $response->getHeader('content-type'));
        
    }
}
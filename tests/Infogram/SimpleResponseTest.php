<?php

use Infogram\SimpleResponse;

class SimpleResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct_shouldNormalizeHeaderNameCase()
    {
        $response = new SimpleResponse('blah', array('Content-Type' => 'application/json'));

        $this->assertEquals('application/json', $response->getHeader('Content-Type'));
        $this->assertEquals('application/json', $response->getHeader('content-type'));
        
    }

    public function testGetHeader_nonExistent_shouldReturnNull()
    {
        $response = new SimpleResponse('blah', array());
        $this->assertEquals(null, $response->getHeader('Content-Type'));
    }
}

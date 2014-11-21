<?php

use Infogram\InfogramRequest;
use Infogram\SimpleResponse;

use \Mockery as Mockery;

class InfogramRequestTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute_shouldSendAndReceiveResponse()
    {
        $templates = array(
            array(
                'id' => 1,
                'title' => 'The Simpsons'),
            array(
                'id' => 2,
                'title' => 'The Jetsons'),
            array(
                'id' => 3,
                'title' => 'The Flintstones'));
        
        $transport = Mockery::mock('Infogram\Transport');
        $transport->shouldReceive('send')->andReturn(new SimpleResponse($templates, array('Status' => '200')));
        
        $session = Mockery::mock('Infogram\InfogramSession');
        $session->shouldReceive('passThrough'); //noop
        
        $request = new InfogramRequest($session, 'GET', 'themes', null, null, null, $transport);
        $response = $request->execute();
        $this->assertNotNull($response);
        $this->assertTrue($response->isOK());

        $body = $response->getBody();
        $this->assertTrue(is_array($body));
        $this->assertEquals(3, count($body));
        $this->assertEquals(1, $body[0]['id']);
        $this->assertEquals('The Simpsons', $body[0]['title']);
        $this->assertEquals(3, $body[2]['id']);
        $this->assertEquals('The Flintstones', $body[2]['title']);
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
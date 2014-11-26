<?php

use Infogram\InfogramRequest;
use Infogram\SimpleResponse;

use \Mockery as Mockery;

class InfogramRequestTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute_shouldSendAndReceiveResponse()
    {
        $templates = '[{ "id": 1, "title": "The Simpsons"  }, { "id": 2, "title": "The Jetsons" }, { "id": 3, "title" : "The Flintstones" }]';
        
        $transport = Mockery::mock('Infogram\Transport');
        $transport->shouldReceive('send')->andReturn(new SimpleResponse($templates, array('Content-Type' => 'application/json'), 200));

        $session = Mockery::mock('Infogram\InfogramSession');
        $session->shouldReceive('passThrough'); //noop

        $request = new InfogramRequest($session, 'GET', 'themes', null, null, null, $transport);
        $response = $request->execute();
        $this->assertNotNull($response);
        $this->assertTrue($response->isOK());

        $body = $response->getBody();
        $this->assertTrue(is_array($body));
        $this->assertEquals(3, count($body));
        $this->assertEquals(1, $body[0]->id);
        $this->assertEquals('The Simpsons', $body[0]->title);
        $this->assertEquals(2, $body[1]->id);
        $this->assertEquals('The Jetsons', $body[1]->title);
        $this->assertEquals(3, $body[2]->id);
        $this->assertEquals('The Flintstones', $body[2]->title);
    }

    public function testExecute_shouldSerializeNonScalarsToJSON()
    {
        $transport = Mockery::mock('Infogram\Transport');
        $session = Mockery::mock('Infogram\InfogramSession');

        $parameters = array(
            'scalarInt' => 123,
            'scalarString' => 'foo',
            'compoundArray' => array(4, 'five')
        );
        
        $request = new InfogramRequest($session, 'POST', 'something', $parameters, null, null, $transport);

        $requestParameters = $request->getParameters();

        $this->assertTrue(is_array($requestParameters));
        $this->assertArrayHasKey('scalarInt', $requestParameters);
        $this->assertTrue($requestParameters['scalarInt'] === 123);
        $this->assertArrayHasKey('scalarString', $requestParameters);
        $this->assertTrue($requestParameters['scalarString'] === 'foo');
        $this->assertArrayHasKey('compoundArray', $requestParameters);
        $this->assertTrue(is_string($requestParameters['compoundArray']));
        $arr = json_decode($requestParameters['compoundArray']);
        $this->assertTrue($arr[0] === 4);
        $this->assertTrue($arr[1] === 'five');
    }

    public function testExecute_transportReturnsNull_returnNull()
    {
        $transport = Mockery::mock('Infogram\Transport');
        $transport->shouldReceive('send')->andReturn(null);
        
        $session = Mockery::mock('Infogram\InfogramSession');
        $session->shouldReceive('passThrough');

        $request = new InfogramRequest($session, 'GET', 'something', array(), null, null, $transport);

        $response = $request->execute();

        $this->assertNull($response);
    }

    public function tearDown()
    {
        Mockery::close();
    }
}

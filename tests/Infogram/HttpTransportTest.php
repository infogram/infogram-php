<?php

use Infogram\HttpTransport;
use Infogram\SimpleRequest;

use \Mockery as Mockery;

class HttpTransportTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException ErrorException
     */
    public function testSend_cannotInitialize_shouldThrow()
    {
        $curl = Mockery::mock('Infogram\Curl');
        $curl->shouldReceive('init')->andReturn(false);

        $request = Mockery::mock('Infogram\Request');

        $transport = new HttpTransport($curl);
        $transport->send($request);
    }

    /**
     * @expectedException ErrorException
     */
    public function testSend_cannotExec_shouldThrow()
    {
        $curl = Mockery::mock('Infogram\Curl');
        $curl->shouldReceive('init')->andReturn(true);
        $curl->shouldReceive('setOption');
        $curl->shouldReceive('close');
        $curl->shouldReceive('getError')->andReturn('');
        $curl->shouldReceive('exec')->andReturn(false);

        $transport = new HttpTransport($curl);
        
        $request = new SimpleRequest('GET', 'http://somewhere.dunno', array());
        $transport->send($request);
    }

    public function testSend_postRequest_shouldSetPostFieldsAndHeader()
    {
        $curl = Mockery::mock('Infogram\Curl');
        $curl->shouldReceive('init')->andReturn(true);
        $curl->shouldReceive('close');
        $curl->shouldReceive('escape')->andReturnUsing(function($input) {
            return rawurlencode($input);
        });
        $curl->shouldReceive('exec')->andReturn('');
        $curl->shouldReceive('getInfo')->with(CURLINFO_HTTP_CODE)->andReturn(200);

        $curl->shouldReceive('setOption')->with(CURLOPT_POSTFIELDS, 'one=1&two=2')->atLeast()->times(1);
        $curl->shouldReceive('setOption')->with(CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'))->atLeast()->times(1);
        $curl->shouldReceive('setOption');

        $transport = new HttpTransport($curl);
        
        $request = new SimpleRequest('POST', 'something', array('one' => 1, 'two' => 2));

        $transport->send($request);
    }

    public function testSend_successfulRequest_shouldParseReturnMessage()
    {
        $curl = Mockery::mock('Infogram\Curl');
        $curl->shouldReceive('init')->andReturn(true);
        $curl->shouldReceive('close');
        $curl->shouldReceive('escape')->andReturnUsing(function($input) {
            return rawurlencode($input);
        });
        $curl->shouldReceive('getInfo')->with(CURLINFO_HTTP_CODE)->andReturn(200);
        $curl->shouldReceive('setOption');
        $curl->shouldReceive('exec')->andReturn("HTTP/1.1 200 OK\r\n" .
                                                "Content-Type: application/json\r\n" .
                                                "X-Some-Header: foo\r\n" .
                                                "\r\n" .
                                                "response content");

        $transport = new HttpTransport($curl);

        $request = new SimpleRequest('GET', 'something', array());

        $response = $transport->send($request);

        $this->assertNotNull($response);
        $this->assertEquals(200, $response->getStatus());
        $this->assertEquals('response content', $response->getBody());
        $headers = $response->getHeaders();
        $this->assertArrayHasKey('Content-Type', $headers);
        $this->assertEquals('application/json', $headers['Content-Type']);
        $this->assertArrayHasKey('X-Some-Header', $headers);
        $this->assertEquals('foo', $headers['X-Some-Header']);
    }
    
    public function tearDown()
    {
        Mockery::close();
    }
}
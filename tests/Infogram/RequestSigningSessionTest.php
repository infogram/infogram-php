<?php

use Infogram\SimpleRequest;
use Infogram\RequestSigningSession;

class RequestSigningSessionTest extends \PHPUnit_Framework_TestCase
{
    public function testPassThrough_shouldSignWithCorrectSignature()
    {
        $session = new RequestSigningSession('john', 'passw0rd');
        $request = new SimpleRequest('GET', 'http://infogram.local.com:5000/service/v1/shelf', array('apples' => 2, 'oranges' => 'many'));
        $session->passThrough($request);
        $params = $request->getParameters();

        $this->assertArrayHasKey('api_key', $params);
        $this->assertEquals('john', $params['api_key']);

        $this->assertArrayHasKey('api_sig', $params);
        $this->assertEquals('x38tTpTI9SN0T2XRWZ/S0y0SwDQ=', $params['api_sig']);
    }

    /**
     * @expectedException ErrorException
     */
    public function testPassThrough_containsKeyParameter_shouldThrowException()
    {
        $session = new RequestSigningSession('john', 'passw0rd');
        $request = new SimpleRequest('GET', 'http://somewhere.vvz/foo/bar', array('one' => 1, 'two' => 2, 'api_key' => 'value'));
        $session->passThrough($request);
    }

    /**
     * @expectedException ErrorException
     */
    public function testPassThrough_containsSignatureParameter_shouldThrowException()
    {
        $session = new RequestSigningSession('john', 'passw0rd');
        $request = new SimpleRequest('GET', 'http://somewhere.vvz/foo/bar', array('one' => 1, 'two' => 2, 'api_sig' => 'asdfqwer'));
        $session->passThrough($request);
    }
}

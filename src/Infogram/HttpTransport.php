<?php

namespace Infogram;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client;

class HttpTransport implements Transport
{
    private $client;

    public function __construct($client = null)
    {
        $this->client = $client ? $client : new Client();
    }
    
    public function send(Request $request)
    {
        $url = $request->getUrl();
        $params = $request->getParameters();

        $method = $request->getMethod();
        $paramsKey = $method === 'GET' || $method === 'DELETE' ? 'query' : 'body';

        $requestOptions = array();
        $requestOptions[$paramsKey] = $params;
        $httpRequest = $this->client->createRequest($request->getMethod(), $url, $requestOptions);

        $httpResponse = $this->client->send($httpRequest);

        $headers = array();
        foreach ($httpResponse->getHeaders() as $name => $value) {
            $headers[$name] = implode(', ', $value);
        }

        return new SimpleResponse($httpResponse->getBody(), $headers, $httpResponse->getStatusCode());
    }
}

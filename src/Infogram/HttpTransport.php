<?php

namespace Infogram;

use Requests;

class HttpTransport implements Transport
{    
    public function send(Request $request)
    {
        $url = $request->getUrl();
        $params = $request->getParameters();
        $method = $request->getMethod();

        $httpResponse = Requests::request($url, array(), $params, $method);
        
        if (!$httpResponse) {
            return null;
        }

        return new SimpleResponse($httpResponse->body, $httpResponse->headers, $httpResponse->status_code);
    }
}

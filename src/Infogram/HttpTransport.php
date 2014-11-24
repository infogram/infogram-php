<?php

namespace Infogram;

class HttpTransport implements Transport
{

    private $curl;
    
    public function __construct($curl = null)
    {
        $this->curl = $curl ? $curl : new DefaultCurl();
    }
    
    public function send(Request $request)
    {
        if (!$this->curl->init()) {
            throw new \ErrorException('Could not initialize cURL');
        }

        $url = $request->getUrl();
        $params = $request->getParameters();
        $first = true;
        $queryString = '';
        foreach ($params as $name => $value) {
            if (!$first) {
                $queryString .= '&';
            }
            $queryString .= $this->curl->escape($name) . '=' . $this->curl->escape($value);
            $first = false;
        }

        $method = $request->getMethod();
        
        if (!empty(queryString)) {
            if ($method == 'GET' || $method == 'DELETE') {
                $url .= '?' . $queryString;
            }
            else if ($method == 'POST' || $method == 'PUT') {
                $this->curl->setOption(CURLOPT_POSTFIELDS, $queryString);
                $this->curl->setOption(CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
            }
        }

        $this->curl->setOption(CURLOPT_URL, $url);
        $this->curl->setOption(CURLOPT_CUSTOMREQUEST, $method);
        $this->curl->setOption(CURLOPT_RETURNTRANSFER, TRUE);
        $this->curl->setOption(CURLOPT_HEADER, TRUE);

        $result = $this->curl->exec();
        
        if ($result === FALSE) {
            $error = $this->curl->getError();
            $this->curl->close();
            throw new \ErrorException('Could not execute cURL request: ' . $error);
        }

        $status = $this->curl->getInfo(CURLINFO_HTTP_CODE);
        $this->curl->close();

        $headersAndBody = explode("\r\n\r\n", $result, 2);
        $headers = array();
        $body = '';
        if (count($headersAndBody) == 1) {
            $body = $headersAndBody[0];
        }
        else {
            $headerLines = explode("\r\n", $headersAndBody[0]);
            foreach ($headerLines as $line) {
                $nameValue = explode(': ', $line);
                if (count($nameValue) == 2) {
                    $headers[$nameValue[0]] = $nameValue[1];
                }
            }
            $body = $headersAndBody[1];
        }

        return new SimpleResponse($body, $headers, $status);
    }
}

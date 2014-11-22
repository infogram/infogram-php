<?php

namespace Infogram;

class HttpTransport implements Transport
{
    public function send(Request $request)
    {
        $cu = curl_init();
        if (!$cu) {
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
            $queryString .= curl_escape($cu, $name) . '=' . curl_escape($cu, $value);
            $first = false;
        }

        $method = $request->getMethod();
        
        if (!empty(queryString)) {
            if ($method == 'GET' || $method == 'DELETE') {
                $url .= '?' . $queryString;
            }
            else if ($method == 'POST' || $method == 'PUT') {
                curl_setopt($cu, CURLOPT_POSTFIELDS, $queryString);
                curl_setopt($cu, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
            }
        }

        curl_setopt($cu, CURLOPT_URL, $url);
        curl_setopt($cu, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($cu, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($cu, CURLOPT_HEADER, TRUE);

        $result = curl_exec($cu);
        
        if ($result === FALSE) {
            $error = curl_error($cu);
            curl_close($cu);
            throw new \ErrorException('Could not execute cURL request: ' . $error);
        }

        $status = curl_getinfo($cu, CURLINFO_HTTP_CODE);
        curl_close($cu);

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

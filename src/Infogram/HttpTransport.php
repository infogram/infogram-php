<?php

namespace Infogram;

class HttpTransport implements Transport
{
    public function send(Request $request)
    {
        $cu = curl_init();
        if (!$cu) {
            throw new ErrorException('Could not initialize cURL');
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

        if ($method == 'GET' || $method == 'DELETE') {
            if (!empty($queryString)) {
                $url .= '?' . $queryString;
            }
        }

        curl_setopt($cu, CURLOPT_URL, $url);
        curl_setopt($cu, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($cu, CURLOPT_RETURNTRANSFER, TRUE);
        //curl_setopt($cu, CURLOPT_HEADER, TRUE);

        $result = curl_exec($cu);

        if ($result === FALSE) {
            curl_close($cu);
            throw new \ErrorException('Could not execute cURL request: ' . $result);
        }

        $status = curl_getinfo($cu, CURLINFO_HTTP_CODE);

        $body = empty($result) ? null : json_decode($result);

        $headers = array(); //for now

        curl_close($cu);

        return new SimpleResponse($body, $headers, $status);
    }
}

<?php

namespace Infogram;

class RequestSigningSession implements InfogramSession
{

    private $consumerKey;

    private $consumerSecret;

    public function __construct($consumerKey, $consumerSecret)
    {
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
    }

    public function passThrough(Request $request)
    {
        $params = $request->getParameters();
        if (array_key_exists('api_key', $params)) {
            throw new \ErrorException('Request contains parameter with a reserved name: \'api_key\'');
        }
        if (array_key_exists('api_sig', $params)) {
            throw new \ErrorException('Request contains parameter with a reserved name: \'api_sig\'');
        }
        $params['api_key'] = $this->consumerKey;
        $baseString = BaseString::compute($request->getMethod(), $request->getUrl(), $params);
        $signature = base64_encode(hash_hmac('sha1', $baseString, rawurlencode($this->consumerSecret), true));
        $request->setParameter('api_key', $this->consumerKey);
        $request->setParameter('api_sig', $signature);
    }
}

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
        if (array_key_exists('key', $params)) {
            throw new \ErrorException('Request contains parameter with a reserved name: \'key\'');
        }
        if (array_key_exists('ig_sig', $params)) {
            throw new \ErrorException('Request contains parameter with a reserved name: \'ig_sig\'');
        }
        $params['key'] = $this->consumerKey;
        $baseString = BaseString::compute($request->getMethod(), $request->getUrl(), $params);
        $signature = base64_encode(hash_hmac('sha1', $baseString, rawurlencode($this->consumerSecret), true));
        $request->setParameter('key', $this->consumerKey);
        $request->setParameter('ig_sig', $signature);
    }
}

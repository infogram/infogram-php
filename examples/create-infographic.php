<?php
require('../vendor/autoload.php');

use Infogram\InfogramRequest;
use Infogram\RequestSigningSession;

$shortopts = 'k:s:b::';
$longopts = array('key:', 'secret:', 'base-url::');
$options = getopt($shortopts, $longopts);

if ((!array_key_exists('k', $options) && !array_key_exists('key', $options)) ||
     (!array_key_exists('s', $options) && !array_key_exists('secret', $options))) {
    die("Usage:\n" .
        "php list-infographics.php -k <API key> -s <API secret>\n" .
        "php list-infographics.php --key=<API key> --secret=<API secret>\n");
}

$consumerKey = array_key_exists('k', $options) ? $options['k'] : $options['key'];
$consumerSecret = array_key_exists('s', $options) ? $options['s'] : $options['secret'];

$baseUrl = null;
if (array_key_exists('b', $options)) {
    $baseUrl = $options['b'];
}
else if (array_key_exists('base-url', $options)) {
    $baseUrl = $options['base-url'];
}

$content = array(
    array(
        'type' => 'h1',
        'text' => 'Testing PHP API client'
    ),
    array(
        'type' => 'body',
        'text'=> 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi eu porttitor sapien. Donec hendrerit, mi id ultricies varius, sem ex venenatis erat, id posuere nunc quam quis metus'
    ),
    array(
        'type' => 'quote',
        'text' => 'God does not play dice',
        'author' => 'Альберт Эйнштейн'
    ),
    array(
        'type' => 'chart',
        'chart_type' => 'bar',
        'data' => array(
            array(
                array('apples', 'today', 'yesterday', 'd. bef. yesterday'),
                array('John', 4, 6, 7),
                array('Peter', 1, 3, 9),
                array('George', 4, 4, 3)
            )
        )  
    )    
);

$session = new RequestSigningSession($consumerKey, $consumerSecret);
$request = new InfogramRequest($session, 'POST', 'infographics/', array('content' => $content, 'theme_id' => 32), $baseUrl);

$response = $request->execute();

if (! $response) {
    die("Could not connect to the server\n");
}

if (!$response->isOK()) {
    die('Could not execute request: ' . $response->getBody() . "\n");
}

$id = $response->getHeader('X-Infogram-Id');

die('Infographic created, id: ' . $id . "\n");


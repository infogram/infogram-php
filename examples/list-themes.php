<?php

require('../vendor/autoload.php');

use Infogram\InfogramRequest;
use Infogram\RequestSigningSession;

$shortopts = 'k:s:b::';
$longopts = array('key:', 'secret:', 'base-url::');
$options = getopt($shortopts, $longopts);

if ((!array_key_exists('k', $options) && !array_key_exists('key', $options) || (!array_key_exists('s', $options) && !array_key_exists('secret', $options)))) {
    die("Usage:\nphp list-themes.php -k <API key> -s <API secret>\nphp list-themes.php --key=<API key> --secret=<API secret>");
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

$session = new RequestSigningSession($consumerKey, $consumerSecret);
$request = new InfogramRequest($session, 'GET', 'themes', null, $baseUrl);

$response = $request->execute();

if (! $response) {
    die("Could not connect to the server\n");
}

if (!$response->isOK()) {
    die("Could not execute request\n");
}


$themes = $response->getBody();
if (empty($themes)) {
    die("There are no themes available\n");
}

echo "ID\t\tTitle\n";
foreach ($themes as $theme) {
    $id = $theme->id;
    $title = $theme->title;
    echo "$id\t\t$title\n";
}

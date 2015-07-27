<?php

require('../vendor/autoload.php');

use Infogram\InfogramRequest;
use Infogram\RequestSigningSession;

$shortopts = 'k:s:u:b::';
$longopts = array('key:', 'secret:', 'user:', 'base-url::');
$options = getopt($shortopts, $longopts);

if ((!array_key_exists('k', $options) && !array_key_exists('key', $options)) ||
     (!array_key_exists('s', $options) && !array_key_exists('secret', $options)) ||
     (!array_key_exists('u', $options) && !array_key_exists('user', $options))) {
    die("Usage:\n" .
        "php list-infographics.php -k <API key> -s <API secret> -u <user profile>\n" .
        "php list-infographics.php --key=<API key> --secret=<API secret> --user=<user profile>\n");
}

$consumerKey = array_key_exists('k', $options) ? $options['k'] : $options['key'];
$consumerSecret = array_key_exists('s', $options) ? $options['s'] : $options['secret'];
$userName = array_key_exists('u', $options) ? $options['u'] : $options['user'];

$baseUrl = null;
if (array_key_exists('b', $options)) {
    $baseUrl = $options['b'];
}
else if (array_key_exists('base-url', $options)) {
    $baseUrl = $options['base-url'];
}

$session = new RequestSigningSession($consumerKey, $consumerSecret);
$request = new InfogramRequest($session, 'GET', 'users/' . $userName  . '/infographics', null, $baseUrl);

$response = $request->execute();

if (! $response) {
    die("Cannot connect to the server\n");
}

if (!$response->isOK()) {
    die("Could not execute request\n");
}


$infographics = $response->getBody();
if (empty($infographics)) {
    die("There are no public infographics for this user\n");
}

echo "ID" . str_repeat(' ', 36) . "Title\n";
foreach ($infographics as $infographic) {
    $id = $infographic->id;
    $title = $infographic->title;
    echo "$id" . str_repeat(' ', max(array(0, 38 - strlen($id))))  . "$title\n";
}

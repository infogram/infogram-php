#Infogram-PHP

This library provides an API to create and update infographics on Infogr.am

## Installation

The recommended way to install Infogram-PHP is via [Composer](https://getcomposer.org/). Composer is a tool for dependency management in PHP. It allows you to declare the dependent libraries your project needs and it will install them in your project for you.

```shell
#Install Composer
curl -sS https://getcomposer.org/installer | php
```

* Add `infogram/infogram-php` repository to the `repositories` section of your project's `composer.json`:
```json
"repositories": [
  {
    "type": "git",
    "url": "https://github.com/infogram/infogram-php"
  }
]
```
* Add the following dependency to the `require` section of your project's `composer.json`:
```json
"require": {
  "infogram/infogram": "dev-master"
}
```
* Execute command line `composer update` in your project's root

### API Keys
You're going to need API keys, the public key and the secret (or private) key, to be able to access infogr.am API service. To acquire those contact Infogr.am [sales team](https://infogr.am/pricing). Keep your secret key secret, don't send it to anyone and do not pass it to any service directly.

The public key is used to indentify the API account the code is accessing with and the secret key is used to sign every request, i.e., add additional parameter to the request query string or body.

## Usage
### Making a Request
For making a request, two Infogram-PHP classes are essential: `Infogram\RequestSigningSession` and `Infogram\InfogramRequest`. The former is responsible for HTTP request signing using the provided API secret key and the latter performs HTTP requests to the Infogr.am API service.

TODO: add reference to HTTP API documentation

```php
use Infogram\InfogramRequest;
use Infogram\RequestSigningSession;
//...
$consumerKey = 'Your public key';
$consumerSecret = 'Your secret key';
$session = new RequestSigningSession($consumerKey, $consumerSecret);
$request = new InfogramRequest($session, 'GET', 'themes');
$response = $request->execute();
```

### Using a Response
Method `Infogram\InfogramRequest::execute` returns an instance of class `Infogram\InfogramResponse` or `null` if Infogr.am API server cannot be accessed.

`Infogram\InfogramResponse` contains (1) HTTP status code from last API request, (2) HTTP response body, (3) HTTP response headers

```php
$content = array(
  array(
    'type' => 'h1',
    'text' => 'hello'
  )
);
$session = new RequestSigningSession($consumerKey, $consumerSecret);
$request = new InfogramRequest($session, 'POST', 'infographics', array('content' => $content));
$response = $request->execute();
if (! $response) {
   die("Could not contact Infogr.am API server\n");
}
if (! $response->isOK()) {
   die('Error executing API request: ' . $response->getBody() . "\n");
}
echo 'Created new infographic with ID: ' . $response->getHeader('X-Infogram-Id') . "\n";
```

On successful request (`$response->isOK() == true`), response's method `getBody` returns either a string or array (converted from a JSON string) if applicable.
On error (`! $response->isOK()`), `getBody` returns string which contains the error message if there is any.

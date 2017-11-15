# Germania KG · RouteNameUrlCallable

**Callable for generating full URL string using Slim Request und Router.**


## Installation

```bash
$ composer require germania-kg/routenameurlcallable
```

Alternatively, add this package directly to your *composer.json:*

```json
"require": {
    "germania-kg/routenameurlcallable": "^1.0"
}
```


## Usage


```php
<?php
use Germania\RouteNameUrlCallable\RouteNameUrlCallable;

// Have your Slim stuff available
use Slim\Http\Request;
use Slim\Router;
use Slim\Http\Uri;

$uri_factory = new RouteNameUrlCallable($request, $router);

// Generate Slim\Http\Uri instance 
$uri = $uri_factory("LoginPage");

// s.th. like 'http://test.com/login'
echo $uri->__toString();

// Customizing the URL
$url_arguments = array('hello' => 'John');
$query_parameters = array('page' => '2');

echo $uri_factory("IndexPage", $url_arguments);
echo $uri_factory("IndexPage", $url_arguments, $query_parameters);
```


## Development

```bash
$ git clone https://github.com/GermaniaKG/RouteNameUrlCallable.git
$ cd RouteNameUrlCallable
$ composer install
```


## Unit tests

Either copy `phpunit.xml.dist` to `phpunit.xml` and adapt to your needs, or leave as is. 
Run [PhpUnit](https://phpunit.de/) like this:

```bash
$ vendor/bin/phpunit
```

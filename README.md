# Germania KG · RouteNameUrlCallable

**Callable for generating full URL string using Slim 4 Request und Router.**
For using Slim Framework 3, checkout the *v1* branch.

[![Packagist](https://img.shields.io/packagist/v/germania-kg/routenameurlcallable.svg?style=flat)](https://packagist.org/packages/germania-kg/routenameurlcallable)
[![PHP version](https://img.shields.io/packagist/php-v/germania-kg/routenameurlcallable.svg)](https://packagist.org/packages/germania-kg/routenameurlcallable)
[![Build Status](https://img.shields.io/travis/GermaniaKG/RouteNameUrlCallable.svg?label=Travis%20CI)](https://travis-ci.org/GermaniaKG/RouteNameUrlCallable)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/GermaniaKG/RouteNameUrlCallable/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/RouteNameUrlCallable/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/GermaniaKG/RouteNameUrlCallable/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/RouteNameUrlCallable/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/GermaniaKG/RouteNameUrlCallable/badges/build.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/RouteNameUrlCallable/build-status/master)


## Installation with Composer

```bash
$ composer require germania-kg/routenameurlcallable "^2.0"
```

Alternatively, add this package directly to your *composer.json:*

```json
"require": {
    "germania-kg/routenameurlcallable": "^2.0"
}
```

## Usage

Generate **Slim\Http\Uri** instance from a named route:


```php
<?php
use Germania\RouteNameUrlCallable\RouteNameUrlCallable;
use Slim\Factory\AppFactory;

$app = AppFactory::create();
$app->addRoutingMiddleware();

// Create a named route
$app->get("/login", function($request, $response, $args) {
  // Show Form etc.
  return $response;
})->setName("LoginPage");

// 
$app->get("/", function($request, $response, $args) {
	$uri_factory = new RouteNameUrlCallable($request);  

  $login_url = $uri_factory("LoginPage");
  // echo $login_url->__toString();  
  // http://test.com/login
  
  return $response->withHeader('Location', $login_url)->withStatus(302);
});
```

**Customizing the URI** with URL arguments

```php
$uri_factory = new RouteNameUrlCallable($request); 

// A named route with Placeholders 
$app->get("/hello/{name}", function($request, $response, $args) {
  // Show Form etc.
  return $response;
})->setName("Hello");


// Fill in route URL placeholder
echo $uri_factory("Hello", ['name' => 'John']);

// Optionally pass query parameters
echo $uri_factory("Hello", array(), ['foo' => 'bar']);
echo $uri_factory("Hello", ['name' => 'John'], ['foo' => 'bar']);

```



## Development

```bash
$ git clone https://github.com/GermaniaKG/RouteNameUrlCallable.git
$ cd RouteNameUrlCallable
$ composer install
```

## Unit tests

Either copy `phpunit.xml.dist` to `phpunit.xml` and adapt to your needs, or leave as is. Run [PhpUnit](https://phpunit.de/) test or composer scripts like this:

```bash
$ composer test
# or
$ vendor/bin/phpunit
```



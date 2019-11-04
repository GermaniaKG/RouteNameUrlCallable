# Germania KG · RouteNameUrlCallable

**Callable for generating full URL string using Slim 3 Request und Router.**

**This is v1 which requires Slim Framework 3.
For using Slim Framework 4, see *master* branch.**

[![Packagist](https://img.shields.io/packagist/v/germania-kg/routenameurlcallable.svg?style=flat)](https://packagist.org/packages/germania-kg/routenameurlcallable)
[![PHP version](https://img.shields.io/packagist/php-v/germania-kg/routenameurlcallable.svg)](https://packagist.org/packages/germania-kg/routenameurlcallable)
[![Build Status](https://img.shields.io/travis/GermaniaKG/RouteNameUrlCallable.svg?label=Travis%20CI)](https://travis-ci.org/GermaniaKG/RouteNameUrlCallable)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/GermaniaKG/RouteNameUrlCallable/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/RouteNameUrlCallable/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/GermaniaKG/RouteNameUrlCallable/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/RouteNameUrlCallable/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/GermaniaKG/RouteNameUrlCallable/badges/build.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/RouteNameUrlCallable/build-status/master)


## Installation with Composer

```bash
$ composer require germania-kg/routenameurlcallable "^1.0"
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

Either copy `phpunit.xml.dist` to `phpunit.xml` and adapt to your needs, or leave as is. Run [PhpUnit](https://phpunit.de/) test or composer scripts like this:

```bash
$ composer test
# or
$ vendor/bin/phpunit
```



<img src="https://static.germania-kg.com/logos/ga-logo-2016-web.svgz" width="250px">

------



# Germania KG · RouteNameUrlCallable

**Callable for generating full URLs using Slim 4's *RouteContext* and *RouteParser*. Works well as Twig function.**

[![Packagist](https://img.shields.io/packagist/v/germania-kg/routenameurlcallable.svg?style=flat)](https://packagist.org/packages/germania-kg/routenameurlcallable)
[![PHP version](https://img.shields.io/packagist/php-v/germania-kg/routenameurlcallable.svg)](https://packagist.org/packages/germania-kg/routenameurlcallable)
[![Build Status](https://img.shields.io/travis/GermaniaKG/RouteNameUrlCallable.svg?label=Travis%20CI)](https://travis-ci.org/GermaniaKG/RouteNameUrlCallable)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/GermaniaKG/RouteNameUrlCallable/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/RouteNameUrlCallable/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/GermaniaKG/RouteNameUrlCallable/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/RouteNameUrlCallable/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/GermaniaKG/RouteNameUrlCallable/badges/build.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/RouteNameUrlCallable/build-status/master)



## Installation with Composer

This package requires Slim Framework 4. For using Slim Framework 3, checkout the *v1* branch.

```bash
$ composer require germania-kg/routenameurlcallable "^2.0"
```

Alternatively, add this package directly to your *composer.json:*

```json
"require": {
    "germania-kg/routenameurlcallable": "^2.0"
}
```



## Introduction

The controller callable within a Slim route sometimes needs a full URL, be it for a redirect response  or for rendering links. Consider this route which creates a new *thing* via POST and redirects to its GET representation. The redirect requires a full URL.

Given a **named Slim route** like this…

```php
$app->get("/hello/{name}", function($request, $response, $args) {
  $response->getBody()->write("Hello " . $args['name'] );
  return $response;
})->setName("Hello");
```

…**Slim framework** provides a solution using *RouteContext* and *RouteParser*:

```php
$app->post("/users", function($request, $response, $args) {
  // Create new user and grab name
  $user = "john";
  $route = "Hello";
  
  // The Slim way
  $uri      = $request->getUri();
  $context  = \Slim\Routing\RouteContext::fromRequest($request);
  $parser   = $context->getRouteParser();
  $location = $parser->fullUrlFor($uri, $route, ['name' => $user]);
  # http://test.com/hello/john
  
  return $response->withHeader('Location', $location)->withStatus(302);
});
```

The **RouteNameUrlCallable** now hides the cumbersome stuff. It works basically as a shortcut:

```php
$app->post("/users", function($request, $response, $args) {
  
  // Create new user and grab name
  $user = "john";
  $route = "Hello";
  
  $uri_factory = new RouteNameUrlCallable($request);  
  $location = $uri_factory($route, ['name' => $user]);
  # http://test.com/hello/john
  
  return $response->withHeader('Location', $location)->withStatus(302);
});
```



## Usage

While Slim's **RouteParser's** *relativeUrlFor, relativeUrlFor, and fullUrlFor* methods return a string, the **RouteNameUrlCallable** returns a **Slim\Http\Uri** instance:


```php
<?php
use Germania\RouteNameUrlCallable\RouteNameUrlCallable;
use Slim\Factory\AppFactory;

// Setup Slim 4 App
$app = AppFactory::create();
$app->addRoutingMiddleware();


// Our named route:
$app->get("/hello/{name}", function($request, $response, $args) {
  $response->getBody()->write("Hello " . $args['name'] );
  return $response;
})->setName("Hello");


// ...and a route we use the callable within:
$app->post("/users", function($request, $response, $args) {

  // Create new user and grab name
  $user = "john";
  $route = "Hello";
  
  $uri_factory = new RouteNameUrlCallable($request);  

  $location = $uri_factory($route, ['name' => $user]);
  echo get_class($location); 
  # \Slim\Http\Uri
  
  echo $login_url->__toString();  
  # http://test.com/hello/john
  
  return $response->withHeader('Location', $location)->withStatus(302);
});
```



### Invokation alternatives

The first *RouteNameUrlCallable* parameter may be an *array* or *object* which holds the *name*, *args*, and *query* stuff. 

*Placeholder args* and *query parameters* may be overridden with the second resp. third parameter which will be merged:

```php
$url_data = array(
	'name' => "Hello",
  'args' => ['name' => 'John'],
  'params' => ['foo' => 'bar']
);

echo $uri_factory( $url_data );
http://test.com/hello/john?foo=bar

echo $uri_factory( $url_data, [], ['view' => 'table'] );
http://test.com/hello/john?foo=bar&view=table

echo $uri_factory( $url_data, [], ['foo' => 'baz', 'view' => 'table'] );
http://test.com/hello/john?foo=baz&view=table
```



### Usage with Twig

Since *RouteNameUrlCallable* is callable, it can be used as [Twig Function](https://twig.symfony.com/doc/2.x/advanced.html#functions) inside a template:

```twig
{# "hello-user.html" #}
<a href="{{route_url('hello', {'name' => user})}}">Say hello to {{user}}</a>
```

Inside you route controller, add the callable to Twig like this:

```php
<?php
$app->get("/", function($request, $response, $args) {
  
  $uri_factory = new RouteNameUrlCallable($request);

  $twig = new \Twig\Environment( ... );
  $twig->addFunction(new \Twig\TwigFunction('route_url', $uri_factory));
  
  $html = $twig->render("hello-user.html", [
    "user" => "john"
  ]);
  
  $response->getBody()->write( $html );

  return $response;
});
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



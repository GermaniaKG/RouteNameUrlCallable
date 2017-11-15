# Germania KG · RouteNameUrlCallable

**Generates full URL string using Slim Request und Router.**


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

// Instantiation
$at = new RouteNameUrlCallable;

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

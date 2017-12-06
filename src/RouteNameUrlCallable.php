<?php
namespace Germania\RouteNameUrlCallable;

use Slim\Http\Request;
use Slim\Router;

class RouteNameUrlCallable
{

    /**
     * @var Request
     */
    public $request;


    /**
     * @var Router
     */
    public $router;


    /**
     * @param Request $request Slim Request instance
     * @param Router  $router  Slim Router instance
     */
    public function __construct( Request $request, Router $router )
    {
        $this->request = $request;
        $this->router = $router;
    }


    /**
     * @param  string|array $route Route name or array with name, arguments and query parameters
     * @param  array  $args   Optional array with URL arguments
     * @param  array  $params Optional array with query string parameters
     * @return string Full URI
     */
    public function __invoke( $route, $args = array(), $params = array() )
    {
        if (is_null( $args)):
            $args = array();
        endif;

        if (is_null( $params)):
            $params = array();
        endif;

        if (is_string($route)):
            $name = $route;

        elseif (is_array($route)):
            $name   = isset($route['name'])   ? $route['name'] : null;
            $args   = isset($route['args'])   ? array_merge($route['args'], $args) : $args;
            $params = isset($route['params']) ? array_merge($route['params'], $params) : $params;

        elseif (is_object($route)):
            $name   = isset($route->name)   ? $route->name : null;
            $args   = isset($route->args)   ? array_merge( (array) $route->args, $args)   : $args;
            $params = isset($route->params) ? array_merge( (array) $route->params, $params) : $params;
        endif;

        if (empty($name) or !is_string( $name )):
            throw new \InvalidArgumentException("Route must be either a) non-empty string with route name or b) array with keys 'name' and, optionally, 'args' and/or 'params' array");
        endif;


        // Get URL path
        $url_path = $this->router->pathFor($name, $args );

        // Build GET parameters
        $query_string = http_build_query($params);

        // Create return value
        return $this->request->getUri()->withPath( $url_path )->withQuery( $query_string );
    }
}

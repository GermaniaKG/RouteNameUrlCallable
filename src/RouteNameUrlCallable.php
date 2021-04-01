<?php
namespace Germania\RouteNameUrlCallable;

use Slim\Psr7\Request as SlimRequest;
use Slim\Psr7\Uri as SlimUri;
use Slim\Routing\RouteContext;
use Slim\Routing\RouteParser;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class RouteNameUrlCallable
{


    /**
     * @var SlimRequest
     */
    public $request;

    /**
     * @var Slim\Interfaces\RouteParser
     */
    public $route_parser;


    /**
     * @param SlimRequest $request [description]
     */
    public function __construct( SlimRequest $request )
    {
        $this->setSlimRequest($request);
    }



    /**
     * @param  string|array $route Route name or array with name, arguments and query parameters
     * @param  array  $args   Optional array with URL arguments
     * @param  array  $params Optional array with query string parameters
     *
     * @return Psr\Http\Message\UriInterface
     * @return Slim\Http\Uri Full URI in Slim flavour
     */
    public function __invoke( $route, $args = array(), $params = array() ) : SlimUri
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
            throw new RouteNameUrlInvalidArgumentException("Route must be either a) non-empty string with route name"
              . " or b) array with keys 'name' and, optionally, 'args' and/or 'params' array");
        endif;


        // Get URL path and build GET parameters
        $url_path = $this->getRouteParser()->urlFor($name, $args);
        $query_string = http_build_query($params);

        $request = $this->getSlimRequest();
        return $request->getUri()->withPath( $url_path )->withQuery( $query_string );
    }


    public function setSlimRequest( SlimRequest $request )
    {
        $this->request = $request;
        $this->route_parser = null;
        return $this;
    }


    public function getSlimRequest( ) : SlimRequest
    {
        return $this->request;
    }


    public function setRouteParser(RouteParser $route_parser)
    {
        $this->route_parser = $route_parser;
        return $this;
    }


    public function getRouteParser() : RouteParser
    {
        if (!$this->route_parser)
        {
            // Method 1
            $request = $this->getSlimRequest();
            $routeContext = RouteContext::fromRequest($request);
            $route_parser = $routeContext->getRouteParser();
            $this->setRouteParser($route_parser);

            // Method 2
            // $this->route_parser = $request->getAttribute('routeParser');
        }

        return $this->route_parser;
    }

}

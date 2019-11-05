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
     * @var Psr\Http\Message\UriInterface;
     */
    public $uri;


    /**
     * @var Slim\Interfaces\RouteParser
     */
    public $route_parser;


    /**
     * @param SlimRequest $request [description]
     */
    public function __construct( SlimRequest $request )
    {
        $this->request = $request;
        $this->uri = $request->getUri();

        
        # Method 1
        $routeContext = RouteContext::fromRequest($request);
        $this->route_parser = $routeContext->getRouteParser();
        
        # Method 2
        // $this->route_parser = $request->getAttribute('routeParser');
    }



    /**
     * @param  string|array $route Route name or array with name, arguments and query parameters
     * @param  array  $args   Optional array with URL arguments
     * @param  array  $params Optional array with query string parameters
     * 
     * @return Psr\Http\Message\UriInterface
     * @return Slim\Http\Uri Full URI in SLim flavour
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
        $url_path = $this->route_parser->urlFor($name, $args);
        $query_string = http_build_query($params);

        return $this->uri->withPath( $url_path )->withQuery( $query_string );
    }

}

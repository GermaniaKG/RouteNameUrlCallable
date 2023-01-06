<?php
namespace Germania\RouteNameUrlCallable;

use Slim\Interfaces\RouteParserInterface;
use Slim\Routing\RouteContext;
use Slim\Psr7\Request as SlimRequest;
use Slim\Psr7\Factory\UriFactory;
use Psr\Http\Message\UriInterface;

class RouteNameUrlCallable
{

    /**
     * @var RouteParserInterface
     */
    public $route_parser;

    /**
     * @var UriInterface|null
     */
    public $uri;


    /**
     * In order to prepare for v3, the ctor accepts both SlimRequest or RouteParserInterface.
     * As of v3, only RouteParserInterface will be accepted.
     */
    public function __construct( $route_parser, UriInterface $uri = null )
    {
        if ($route_parser instanceOf RouteParserInterface) {
            $this->setRouteParser($route_parser);
        }
        elseif ($route_parser instanceOf SlimRequest) {
            $uri = $uri ?: $route_parser->getUri();
            $route_parser = static::getRouteParserFromRequest($route_parser);
            $this->setRouteParser($route_parser);
        }
        else {
            throw new \InvalidArgumentException("Expected RouteParserInterface or SlimRequest.");
        }

        $this->setUri( $uri ?: (new UriFactory)->createUri() );
    }


    public static function fromRequest(SlimRequest $request)
    {
        $route_parser = static::getRouteParserFromRequest($request);
        return new RouteNameUrlCallable($route_parser, $request->getUri());
    }


    public static function getRouteParserFromRequest( SlimRequest $request ) : RouteParserInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        return $routeContext->getRouteParser();
    }


    /**
     * @param  string|array $route Route name or array with name, arguments and query parameters
     * @param  array  $args   Optional array with URL arguments
     * @param  array  $params Optional array with query string parameters
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function __invoke( $route, $args = array(), $params = array() ) : UriInterface
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

        return $this->uri->withPath( $url_path )->withQuery( $query_string );
    }


    public function setUri(UriInterface $uri) : self
    {
        $this->uri = $uri;
        return $this;
    }


    public function setRouteParser(RouteParserInterface $route_parser)
    {
        $this->route_parser = $route_parser;
        return $this;
    }


    public function getRouteParser() : RouteParserInterface
    {
        return $this->route_parser;
    }

}

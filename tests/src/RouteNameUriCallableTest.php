<?php
namespace tests;

use Germania\RouteNameUrlCallable\RouteNameUrlCallable;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Slim\Http\Uri;

use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\UriFactory;



class RouteNameUriCallableTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @dataProvider provideArguments
     */
    public function testInstantiation( UriInterface $start_page_uri, $route_name, $sut_route, $args, $params, $basePath, $expected )
    {

        // Setup Slim App
        $app = AppFactory::create();
        $app->addRoutingMiddleware();


        // This is the route we want the SUT create an URL for
        $app->get( $basePath, function($request, $response, $args) {
            return $response;
        })->setName( $route_name );


        // This route should utilize SUT
        $route_url = $start_page_uri->getPath();
        $app->get( $route_url, function($request, $response) use ($sut_route, $params, $args) {

            $sut = new RouteNameUrlCallable( $request );
            $result = $sut($sut_route, $args, $params)->__toString();

            return $response->withHeader("RouteNameUrlCallable", $result);
        });


        // Rund Slim 4, retrieving a Response
        $serverRequest = (new ServerRequestFactory)->createServerRequest("GET", $start_page_uri);
        $response = $app->handle($serverRequest);

        // Eval
        $sut_result = $response->getHeaderLine( "RouteNameUrlCallable" );
        $this->assertEquals( $expected, $sut_result);
    }


    /**
     * @dataProvider provideArguments
     */
    public function testExceptionOnWrongRouteNameArgument( UriInterface $start_page_uri, $route_name, $sut_route, $args, $params, $basePath, $expected )
    {


        // Setup Slim App
        $app = AppFactory::create();
        $app->addRoutingMiddleware();



        // This is the route we want the SUT create an URL for
        $app->get( $basePath, function($request, $response, $args) {
            return $response;
        })->setName( $route_name );


        // This route should utilize SUT
        $route_url = $start_page_uri->getPath();
        $app->get( $route_url, function($request, $response) use ($params, $args) {
            $sut = new RouteNameUrlCallable( $request );

            $invalid_route_name = "false";
            $result = $sut($invalid_route_name, $args, $params)->__toString();
            return $response;
        });


        // Rund Slim 4, trying retrieving a Response.
        // In fact, the controller will throw an exception.
        $start_page_uri = (new UriFactory)->createUri( $start_page_uri );
        $serverRequest = (new ServerRequestFactory)->createServerRequest("GET", $start_page_uri);

        $this->expectException( \Exception::class );
        $app->handle($serverRequest);

    }





    public function provideArguments()
    {
        $uri = "http://localhost/just/a/site";
        $uri = (new UriFactory)->createUri( $uri );


        $route_name      = "FooRouteName";
        $args            = array('eins' => 'Eins');
        $params          = array("quu" => "baz", "hello" => "john");
        $params_override = array('hello' => 'anna', 'page' => 2);

        $route_array     = array('name' => $route_name, 'params' => $params);
        $route_object    = (object) $route_array;

        $small_route     = array('name' => $route_name);
        $small_object    = (object) $small_route;


        $path     = "/foo/moin/hallo";
        $expected = $uri->withPath($path)->__toString();

        return array(
            #                      $uri, $route_name, $sut_route,    $args,   $params,          $path, $expected
            [ $uri, $route_name, $route_name,   array(), array(),          $path, "$expected" ],
            [ $uri, $route_name, $route_name,   array(), $params,          $path, "$expected?quu=baz&hello=john" ],
            [ $uri, $route_name, $route_array,  array(), array(),          $path, "$expected?quu=baz&hello=john"],
            [ $uri, $route_name, $route_array,  null,    null,             $path, "$expected?quu=baz&hello=john"],
            [ $uri, $route_name, $route_object, array(), array(),          $path, "$expected?quu=baz&hello=john"],
            [ $uri, $route_name, $route_array,  array(), $params_override, $path, "$expected?quu=baz&hello=anna&page=2"],
            [ $uri, $route_name, $route_object, array(), $params_override, $path, "$expected?quu=baz&hello=anna&page=2"],
            [ $uri, $route_name, $small_route,  $args,   $params,          $path, "$expected?quu=baz&hello=john"],
            [ $uri, $route_name, $small_object, $args,   $params,          $path, "$expected?quu=baz&hello=john"]
        );
    }
}

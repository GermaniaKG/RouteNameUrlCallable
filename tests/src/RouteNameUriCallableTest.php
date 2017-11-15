<?php
namespace tests;

use Germania\RouteNameUrlCallable\RouteNameUrlCallable;

use Slim\Http\Request;
use Slim\Router;
use Slim\Http\Uri;
use Prophecy\Argument;

class RouteNameUriCallableTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @dataProvider provideArguments
     */
    public function testInstantiation( $name, $args, $params, $scheme, $host, $path, $expected )
    {
        $uri = new Uri($scheme, $host);

        $request_mock = $this->prophesize( Request::class );
        $request_mock->getUri()->willReturn($uri);
        $request = $request_mock->reveal();

        $router_mock = $this->prophesize( Router::class );
        $router_mock->pathFor( Argument::any(), Argument::any() )->willReturn( $path );
        $router = $router_mock->reveal();

        $sut = new RouteNameUrlCallable( $request, $router );

        $result = $sut($name, $args, $params)->__toString();
        $this->assertEquals( $expected, $result);
    }


    /**
     * @dataProvider provideArguments
     */
    public function testExceptionOnWrongRouteNameArgument( $name, $args, $params, $scheme, $host, $path, $expected )
    {
        $uri = new Uri($scheme, $host);

        $request_mock = $this->prophesize( Request::class );
        $request_mock->getUri()->willReturn($uri);
        $request = $request_mock->reveal();

        $router_mock = $this->prophesize( Router::class );
        $router_mock->pathFor( Argument::any(), Argument::any() )->willReturn( $path );
        $router = $router_mock->reveal();

        $sut = new RouteNameUrlCallable( $request, $router );

        $this->expectException( \InvalidArgumentException::class );

        $result = $sut( false, $args, $params)->__toString();
    }





    public function provideArguments()
    {
        $scheme = "http";
        $host = "localhost";
        $path = "/foo/moin/hallo";

        $route_name = "FooRouteName";
        $args   = array('eins' => 'Eins');
        $params = array("quu" => "baz", "hello" => "john");
        $params_override = array('hello' => 'anna', 'page' => 2);

        $route_array = array(
            'name' => $route_name,
            'params' => $params
        );

        return array(
            [ $route_name, array(), array(), $scheme, $host, $path, "$scheme://$host$path" ],
            [ $route_name, array(), $params, $scheme, $host, $path, "$scheme://$host$path?quu=baz&hello=john" ],

            [ $route_array, array(), array(), $scheme, $host, $path, "$scheme://$host$path?quu=baz&hello=john"],

            [ (object) $route_array, array(), array(), $scheme, $host, $path, "$scheme://$host$path?quu=baz&hello=john"],

            [ $route_array, array(), $params_override, $scheme, $host, $path, "$scheme://$host$path?quu=baz&hello=anna&page=2"],

            [ (object) $route_array, array(), $params_override, $scheme, $host, $path, "$scheme://$host$path?quu=baz&hello=anna&page=2"],


            [ array( 'name' => $route_name ), $args, $params, $scheme, $host, $path, "$scheme://$host$path?quu=baz&hello=john"],
            [ (object) array( 'name' => $route_name ), $args, $params, $scheme, $host, $path, "$scheme://$host$path?quu=baz&hello=john"]
        );
    }
}

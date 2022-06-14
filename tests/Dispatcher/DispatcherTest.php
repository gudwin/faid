<?php


namespace Faid\tests\Dispatcher;

use Faid\Dispatcher\RouteException;
use \Faid\Request\HttpRequest;
use Faid\Dispatcher\HttpRoute;
use Faid\Dispatcher\Dispatcher;
use Faid\Dispatcher\Route;
use Faid\Request\Request;

class DispatcherTest extends BasicTest
{
    /**

     */
    public function testGetUnknownNamed()
    {
	    $this->expectException(RouteException::class);
        $dispatcher = new Dispatcher(new Request());
        $dispatcher->getNamed('unknown');
    }

    public function testGetNamed()
    {
        $route = new Route([
            'name' => 'hello'
        ]);
        $dispatcher = new Dispatcher(new Request());
        $dispatcher->addRoute($route);
        $this->assertEquals($route, $dispatcher->getNamed('hello'));

    }
    public function getActiveRoute() {

        //
        $request = new HttpRequest();
        $request->uri(self::fixtureTestUrl);
        //
        $route = new HttpRoute(
            array(
                'url' => '/Controller/',
                'action' => 'testDispatchFunction'
            )
        );
        //
        $dispatcher = new Dispatcher($request);
        $this->assertNull( $dispatcher->getActiveRoute());
        //
        $dispatcher->addRoute($route);
        //
        $foundRoute = $dispatcher->run();
        //
        $this->assertEquals($route, $foundRoute);
        $this->assertEquals( $route, $dispatcher->getActiveRoute());
    }

}

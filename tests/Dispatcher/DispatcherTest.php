<?php


namespace Faid\tests\Dispatcher;


use Faid\Dispatcher\Dispatcher;
use Faid\Dispatcher\Route;
use Faid\Request\Request;

class DispatcherTest extends BasicTest {
    /**
     * @expectedException \Faid\Dispatcher\RouteException
     */
    public function testGetUnknownNamed() {
        $dispatcher = new Dispatcher(new Request());
        $dispatcher->getNamed('unknown');
    }
    public function testGetNamed() {
        $route = new Route([
            'name' => 'hello'
        ]);
        $dispatcher = new Dispatcher(new Request());
        $dispatcher->addRoute($route );
        $this->assertEquals( $route, $dispatcher->getNamed('hello'));

    }

} 
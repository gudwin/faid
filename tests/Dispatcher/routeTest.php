<?php
namespace Faid\tests\Dispatcher {
    use Faid\Dispatcher\Route;
    use \Faid\Request\HttpRequest;
    use \Faid\Dispatcher\Dispatcher;
    use \Faid\Dispatcher\HttpRoute;
    use \Faid\Dispatcher\RouteException;
    use \Faid\tests\baseTest;

    class RouteTest extends BasicTest
    {
        const fixtureTestUrl = '/Controller/1/view/helloWorld/2';

        public function testGetName()
        {
            $fixture = 'hello world!';
            $route = new Route(['name' => $fixture]);
            $this->assertEquals($route->getName(), $fixture);
        }

        /**
         * @expectedException \Faid\Dispatcher\RouteException
         */
        public function testDispatchNotReadyRoute()
        {
            // that route correctly initialiized
            $route = new HttpRoute(array(
                'url' => '/Controller/test/',
                'controller' => new testController(),
                'action' => 'someAction'
            ));
            // but when dispatch will be called Exception will thrown, because route method "->test()" did not called
            $route->dispatch();


        }

        /**
         * @expectedException \Faid\Dispatcher\RouteException
         */
        public function testCallBackNotFound()
        {
            $request = new HttpRequest();
            $request->uri(self::fixtureTestUrl);

            $dispatcher = new Dispatcher($request);
            $dispatcher->addRoute(
                new HttpRoute(array(
                    'template' => '/some_url/'
                ))
            );
            $route = $dispatcher->run();
            $route->dispatch();
        }

        /**
         * @expectedException \Faid\Dispatcher\RouteException
         */
        public function testCallBackNotFoundSecond()
        {
            $request = new HttpRequest();
            $request->uri(self::fixtureTestUrl);

            $dispatcher = new Dispatcher($request);
            $dispatcher->addRoute(
                new HttpRoute(array(
                        'url' => '/some_url/',
                        'controller' => 'unknown',
                        'action' => 'unknown',
                    )
                ));
            $route = $dispatcher->run();
            $route->dispatch();
        }

        /**
         * @expectedException \Faid\Dispatcher\RouteException
         */
        public function testRouteNotFound()
        {
            $request = new HttpRequest();
            $request->uri(self::fixtureTestUrl);
            //
            $dispatcher = new Dispatcher($request);
            $dispatcher->addRoute(
                new HttpRoute(
                    array(
                        'controller' => new testController(),
                        'url' => '/test/route/',
                        'action' => 'someAction'
                    )
                )
            );
            $dispatcher->run();
        }

        public function testSimpleRoute()
        {
            $request = new HttpRequest();
            $request->uri(self::fixtureTestUrl);
            //
            $route = new HttpRoute(
                array(
                    'url' => '/Controller/',
                    'controller' => new TestController(),
                    'action' => 'someAction'

                )
            );
            //
            $dispatcher = new Dispatcher($request);
            $dispatcher->addRoute($route);
            //
            $route = $dispatcher->run();
            //
            $this->AssertEquals('someAction', $route->getAction());
        }

        public function testCallback()
        {
            $request = new HttpRequest();
            $request->uri(self::fixtureTestUrl);

            $called = false;
            $route = new HttpRoute(array(
                'url' => self::fixtureTestUrl,
                'callback' => function () use (&$called) {
                        $called = true;
                    }
            ));
            $this->assertTrue((bool)$route->test($request));
            $this->assertFalse($called);
            $route->dispatch();
            $this->assertTrue($called);
        }

        public function testRoutesList()
        {
            $request = new HttpRequest();
            $request->uri(self::fixtureTestUrl);
            //
            $dispatcher = new Dispatcher($request);
            $dispatcher->addRoute(new HttpRoute(array('url' => '/unknownRoute/')));
            $dispatcher->addRoute(new HttpRoute(array('url' => '/unknownRoute2/')));
            $route = new HttpRoute(
                array(
                    'url' => '/Controller/',
                    'controller' => new testController(),
                    'action' => 'someAction'
                )
            );
            $dispatcher->addRoute($route);
            $newRoute = $dispatcher->run();
            //
            $this->assertEquals($route->getController() instanceof testController, true);
            $this->assertEquals($route, $newRoute);

        }

        /**
         * @expectedException \Faid\Dispatcher\RouteException
         * Displaying full process of dispatching routes
         */
        public function testSimpleRouteDispatch()
        {

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
            $dispatcher->addRoute($route);
            //
            $foundRoute = $dispatcher->run();
            //
            $this->assertEquals($route, $foundRoute);
            //
            $route->dispatch();
            //
            $this->expectOutputString('Hello world!');
        }

        /**
         * Tests routes like "/some_url/:action/param1/param2/param3"
         * Example of HttpRoute class usage:
         *
         * HttpRouter::create ( '/some_url/:action/param1', array(
         *            'controller'    => new TestController()
         * ));
         *
         */
        public function testTemplateActionRoute()
        {
            //
            $paramFixture = '123';
            //
            $request = new HttpRequest();
            $request->uri('/Gift/someAction/' . $paramFixture);
            //
            $route = new HttpRoute(
                array(
                    'url' => '/Gift/:action/:param1',
                    'controller' => '\\Faid\\tests\\Dispatcher\\TestController'
                )
            );
            //
            $dispatcher = new Dispatcher($request);
            $dispatcher->addRoute($route);
            //
            $newRoute = $dispatcher->run();
            //
            $this->assertEquals($route, $newRoute);
            $this->assertEquals('someaction', $route->getAction());
            //
            $this->assertEquals($paramFixture, $request->get('param1'));

        }

        /**
         * Tests routes like "/some_url/:controller/some_url/param1" And predefined default action - "someAction"
         * Usage example:
         *
         * new HttpRoute( array(
         *            'url' => '/some_url/:controller/some_url/param1',
         *            'action'        => 'someAction'
         * ));
         *
         */
        public function testTemplateControllerWithSpecificAction()
        {
            //
            $request = new HttpRequest();
            $request->uri('/testController/gift/param1');
            //
            $route = new HttpRoute(
                array(
                    'url' => '/:controller/gift/*',
                    'action' => 'someAction'
                )
            );
            //
            $dispatcher = new Dispatcher($request);
            $dispatcher->addRoute($route);
            //
            $newRoute = $dispatcher->run();
            //
            $this->assertEquals($route, $newRoute);
            $this->assertEquals('someAction', $route->getAction());
        }

        /**
         *
         */
        public function testParametersSet()
        {
            $request = new HttpRequest();
            $request->uri(self::fixtureTestUrl);
            $dispatcher = new Dispatcher($request);
            $dispatcher->addRoute(
                new HttpRoute(

                    array(
                        'url' => '/Controller/:userId/view/:label/:videoId',
                        'controller' => new testController(),
                        'action' => 'someAction',
                    )
                )
            );
            //
            $route = $dispatcher->run();
            //
            $this->assertEquals($request->get('userId'), 1);
            $this->assertEquals($request->get('videoId'), 2);
            $this->assertEquals($request->get('label'), 'helloWorld');
        }

        public function testWildCardSupported()
        {
            //
            $request = new HttpRequest();
            $request->uri(self::fixtureTestUrl);
            //
            $dispatcher = new Dispatcher($request);
            //
            $route = new HttpRoute(
                array(
                    'url' => '/Controller/*',
                    'controller' => 'testController',
                    'action' => 'someAction',
                )
            );
            //
            $dispatcher->addRoute($route);
            //
            $newRoute = $dispatcher->run();
            //
            $this->assertEquals($newRoute, $route);
            //
            $this->assertEquals($request->param1, '1');
            $this->assertEquals($request->param2, 'view');
            $this->assertEquals($request->param3, 'helloWorld');
            $this->assertEquals($request->param4, '2');

        }


        public function testControllerMethodsCalled()
        {
            $request = new HttpRequest();
            $request->uri('/Controller/test/');
            $dispatcher = new Dispatcher($request);
            $dispatcher->addRoute(
                new HttpRoute(
                    array(
                        'url' => '/Controller/test',
                        'controller' => '\\Faid\\tests\\Dispatcher\\testController',
                        'action' => 'someAction'
                    )
                )
            );
            $route = $dispatcher->run();
            //
            $this->assertEquals(testController::getBeforeActionCalled(), false);
            $this->assertEquals(testController::getCalled(), false);
            //
            $route->dispatch();
            //
            $this->assertEquals(testController::getBeforeActionCalled(), true);
            $this->assertEquals(testController::getCalled(), true);
        }
        public function testBuildUrl() {
            $route = new HttpRoute([
                'url' => '/:arg1/:arg2!',
            ]);
            $result = $route->buildUrl([
                'arg1' => 'hello',
                'arg2' => 'world'
            ]);
            $this->assertEquals( $result, '/hello/world!');

        }
    }
}
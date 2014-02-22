<?php
namespace Faid\Tests;

use \Faid\Debug\Debug;
use \Faid\Request\Request;
use \Faid\Request\HttpRequest;
use \Faid\Dispatcher\Dispatcher;
use \Faid\Dispatcher\HttpRoute;
use \Faid\Dispatcher\RouteException;

require_once __DIR__ . '/testController.php';
require_once __DIR__ . '/testFunction.php';
class basicDispatcherTest extends \PHPUnit_Framework_TestCase {
	const fixtureTestUrl = '/Controller/1/view/helloWorld/2';

	/**
	 *
	 */
	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
	}

	public function testRequestObject() {
		$request = new Request(array(
									'phrase1' => 'Hello',
									'phrase2' => 'World'
							   ));
		//
		$this->assertEquals('Hello', $request->get('phrase1'));
		$this->assertEquals('World', $request->get('phrase2'));
	}

	/**
	 * @expectedException \Faid\Dispatcher\RouteException
	 */
	public function testDispatchNotReadyRoute() {
		// that route correctly initialiized
		$route = new HttpRoute('/Controller/test/', array(
														 'controller' => new testController(),
														 'action'     => 'someAction'
													));
		// but when dispatch will be called Exception will thrown, because route method "->test()" did not called
		$route->dispatch();


	}

	/**
	 * @expectedException \Faid\Dispatcher\RouteException
	 */
	public function testCallBackNotFound() {
		$request = new HttpRequest();
		$request->uri(self::fixtureTestUrl);

		$dispatcher = new Dispatcher($request);
		$dispatcher->addRoute(
			HttpRoute::create(
				'/some_url/', array()
			)
		);
		$route = $dispatcher->run();
		$route->dispatch();
	}

	/**
	 * @expectedException \Faid\Dispatcher\RouteException
	 */
	public function testCallBackNotFoundSecond() {
		$request = new HttpRequest();
		$request->uri(self::fixtureTestUrl);

		$dispatcher = new Dispatcher($request);
		$dispatcher->addRoute(
			HttpRoute::create(
				'/some_url/', array(
								   'Controller' => 'unknown',
								   'action'     => 'unknown',
							  )
			)
		);
		$route = $dispatcher->run();
		$route->dispatch();
	}

	/**
	 * @expectedException \Faid\Dispatcher\RouteException
	 */
	public function testRouteNotFound() {
		$request = new HttpRequest();
		$request->uri(self::fixtureTestUrl);
		//
		$dispatcher = new Dispatcher($request);
		$dispatcher->addRoute(
			HttpRoute::create(
				'/test/route/', array(
									 'controller' => new testController(),
									 'action'     => 'someAction'
								)
			)
		);
		$dispatcher->run();
	}

	public function testSimpleRoute() {
		$request = new HttpRequest();
		$request->uri(self::fixtureTestUrl);
		//
		$route = HttpRoute::create(
			'/Controller/', array(
								 // array key was literated with random letter case
								 'ConTroLler' => new TestController(),
								 'AcTiOn'     => 'someAction'

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

	public function testRoutesList() {
		$request = new HttpRequest();
		$request->uri(self::fixtureTestUrl);
		//
		$dispatcher = new Dispatcher($request);
		$dispatcher->addRoute(
			HttpRoute::create('/unknownRoute/', array())
		);
		$dispatcher->addRoute(
			HttpRoute::create('/unknownRoute2/', array())
		);
		$route = HttpRoute::create(
			'/Controller/', array(
								 'Controller' => new testController(),
								 'action'     => 'someAction'
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
	public function testSimpleRouteDispatch() {

		//
		$request = new HttpRequest();
		$request->uri(self::fixtureTestUrl);
		//
		$route = HttpRoute::create(
			'/Controller/', array(
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
	 * Example of HttpRoute::create method:
	 *
	 * HttpRouter::create ( '/some_url/:action/param1', array(
	 *            'controller'    => new TestController()
	 * ));
	 *
	 */
	public function testTemplateActionRoute() {
		//
		$paramFixture = '123';
		//
		$request = new HttpRequest();
		$request->uri('/Gift/someAction/' . $paramFixture);
		//
		$route = HttpRoute::create(
			'/Gift/:action/:param1', array(
									'Controller' => 'TestController'
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
	 * Example of HttpRoute::create method:
	 *
	 * HttpRouter::create ( '/some_url/:controller/some_url/param1', array(
	 *            'action'        => 'someAction'
	 * ));
	 *
	 */
	public function testTemplateControllerWithSpecificAction() {
		//
		$request = new HttpRequest();
		$request->uri('/testController/gift/param1');
		//
		$route = HttpRoute::create(
			'/:controller/gift/*', array(
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
	public function testParametersSet() {
		$request = new HttpRequest();
		$request->uri(self::fixtureTestUrl);
		$dispatcher = new Dispatcher($request);
		$dispatcher->addRoute(
			HttpRoute::create(
				'/Controller/:userId/view/:label/:videoId', array(
																 'controller' => new testController(),
																 'action'     => 'someAction',
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

	public function testWildCardSupported() {
		//
		$request = new HttpRequest();
		$request->uri(self::fixtureTestUrl);
		//
		$dispatcher = new Dispatcher($request);
		//
		$route = HttpRoute::create(
			'/Controller/*', array(
								  'controller' => 'testController',
								  'action'     => 'someAction',
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


	public function testControllerMethodsCalled() {
		$request = new HttpRequest();
		$request->uri('/Controller/test/');
		$dispatcher = new Dispatcher($request);
		$dispatcher->addRoute(
			HttpRoute::create(
				'/Controller/test', array(
										 'Controller'=>'\Faid\Tests\testController',
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
}
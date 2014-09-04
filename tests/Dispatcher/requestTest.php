<?php
namespace Faid\tests\Dispatcher {

	use \Faid\Debug\Debug;
	use \Faid\Request\Request;
	use \Faid\Request\HttpRequest;
	use \Faid\Dispatcher\Dispatcher;
	use \Faid\Dispatcher\HttpRoute;
	use \Faid\Dispatcher\RouteException;

	class requestTest extends basicTest{
		public function testRequestObject() {
			$request = new Request( array(
										'phrase1' => 'Hello',
										'phrase2' => 'World'
									) );
			//
			$this->assertEquals( 'Hello', $request->get( 'phrase1' ) );
			$this->assertEquals( 'World', $request->get( 'phrase2' ) );
		}

		public function testGetRequest() {
			$request    = new Request( array(
										   'phrase1' => 'Hello',
										   'phrase2' => 'World'
									   ) );
			$dispatcher = new Dispatcher( $request );
			$this->assertEquals( $request, $dispatcher->getRequest() );
		}
	}
}
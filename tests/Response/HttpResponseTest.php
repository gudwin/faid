<?php

namespace Faid\tests\Response;

use \Faid\Response\HttpResponse;

class HttpResponseTest extends \Faid\tests\baseTest {
	public function testGetMimeType() {
		$response = new HttpResponse();
		$this->assertEquals( null, $response->getMimeType() );
	}
	public function testSetMimeType() {
		$fixture = 'text/html';

		$response = new HttpResponse();
		$response->setMimeType( $fixture );
		$this->assertEquals( $fixture, $response->getMimeType() );
	}
} 